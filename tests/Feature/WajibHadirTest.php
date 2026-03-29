<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\Jadwal;
use App\Models\PegawaiWajibHadir;
use App\Models\User;
use Carbon\Carbon;

class WajibHadirTest extends TestCase
{
    // use RefreshDatabase; // Use if needed, but risky on persistent dev DB.
    // We will clean up manually.

    public function test_wajib_hadir_flow()
    {
        // Cleanup potential leftovers from failed runs
        $existingPegawai = Pegawai::where('name', 'Wajib Hadir Test User')->first();
        if ($existingPegawai) {
             Jadwal::where('pegawai_id', $existingPegawai->id)->delete();
             PegawaiWajibHadir::where('pegawai_id', $existingPegawai->id)->delete();
             $existingPegawai->delete();
        }

        // 1. Setup Data
        // Use a known Monday date for testing
        $testDate = Carbon::parse('2024-07-08'); // Known Monday
        
        // Find the Tahun that covers this date (same one markAsAlpha will find)
        $tahun = \App\Models\Tahun::where('tanggalmulai', '<=', $testDate->toDateString())
            ->where('tanggalakhir', '>=', $testDate->toDateString())
            ->first();
        
        if (!$tahun) {
            // No Tahun covers this date, create one
            $tahun = Tahun::create([
                'tahun' => '2024/2025',
                'semester' => 'Ganjil',
                'isActive' => 1,
                'tanggalmulai' => '2024-07-01',
                'tanggalakhir' => '2024-12-31'
            ]);
        }
        
        // Ensure this Tahun is active (for the store controller)
        Tahun::where('isActive', 1)->where('id', '!=', $tahun->id)->update(['isActive' => 0]);
        $tahun->update(['isActive' => 1]);


        $pegawai = Pegawai::create([
            'name' => 'Wajib Hadir Test User',
            'aktif' => 'Aktif',
            'status' => 'GTY'
        ]);

        // 2. Create Schedule (Auto Wajib Hadir for MON)
        // Use try-catch or ensure uniqueness.
        // Assuming (pegawai_id, hari, jam, tahun_id) unique?
        // We just deleted all jadwals for this pegawai, so should be safe.
        // However, mapel_id/kelas_id might be foreign keys. Need valid ones.
        $jurusan = \App\Models\Jurusan::first() ?? \App\Models\Jurusan::firstOrCreate(
            ['kode' => 'TEST', 'jurusan' => 'TEST'],
            ['deskripsi' => 'TEST']
        );
        $kelas = \App\Models\Kelas::firstOrCreate(
            ['kelas' => 'X-TEST', 'jurusan_id' => $jurusan->id],
            ['ket' => 'Test']
        );
        $mapel = \App\Models\Mapel::firstOrCreate(
            ['mapel' => 'TEST-MAPEL', 'jurusan_id' => $jurusan->id],
            ['kode' => null, 'ket' => 'Test']
        );

        $jadwal = Jadwal::create([
            'tahun_id' => $tahun->id,
            'pegawai_id' => $pegawai->id,
            'hari' => 'Senin',
            'jam' => '1',
            'mulai' => '07:00:00',
            'akhir' => '08:00:00',
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'status' => 'Aktif', // Assuming generic status
            'ket' => 'Test'
        ]);

        // 3. Hit Store Endpoint (Simulate Manual Check for TUE)
        // Must authenticate first - route requires 'auth' middleware
        $adminUser = User::whereIn('role', ['admin', 'operator'])
            ->where('is_active', 1)
            ->first();
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Admin Test',
                'email' => 'admin-test-' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
            ]);
        }
        $this->actingAs($adminUser);
        
        $response = $this->post(route('attendance.wajib-hadir.store'), [
            'manual_days' => [
                $pegawai->id => ['Selasa']
            ]
        ]);
        
        // Debugging validation errors if redirect back with errors
        if ($response->status() === 302 && session('errors')) {
             dump(session('errors')->all());
        }
        
        $response->assertRedirect();

        // 4. Verification: Check DB contains BOTH Monday (Auto) and Tuesday (Manual)
        // Controller logic merges Auto + Manual.
        $this->assertDatabaseHas('pegawai_wajib_hadirs', [
            'pegawai_id' => $pegawai->id,
            'hari' => 'Senin',
            'tahun_id' => $tahun->id
        ]);
        $this->assertDatabaseHas('pegawai_wajib_hadirs', [
            'pegawai_id' => $pegawai->id,
            'hari' => 'Selasa',
            'tahun_id' => $tahun->id
        ]);
        
        // 5. Check AttendanceService Logic
        $service = new \App\Services\AttendanceService();

        // Debug: Verify Wajib Hadir is in DB for this pegawai
        $wajibCount = PegawaiWajibHadir::where('pegawai_id', $pegawai->id)->where('tahun_id', $tahun->id)->count();
        $this->assertGreaterThan(0, $wajibCount, 'PegawaiWajibHadir should have entries for this pegawai');

        // Debug: Verify Tahun lookup by date works
        $start = Carbon::parse($tahun->tanggalmulai);
        $mondayDate = $start->copy()->next('Monday');
        
        $tahunFound = \App\Models\Tahun::where('tanggalmulai', '<=', $mondayDate->toDateString())
            ->where('tanggalakhir', '>=', $mondayDate->toDateString())
            ->first();
        $this->assertNotNull($tahunFound, 'Tahun should be found for date ' . $mondayDate->toDateString());
        $this->assertEquals($tahun->id, $tahunFound->id, 'Tahun ID should match');

        // Debug: Verify Wajib Hadir for Monday
        $isWajib = PegawaiWajibHadir::where('pegawai_id', $pegawai->id)
            ->where('tahun_id', $tahun->id)
            ->where('hari', 'Senin')
            ->exists();
        $this->assertTrue($isWajib, 'Pegawai should be wajib hadir on Senin');

        // Case A: Monday (Wajib Hadir, No Log) -> Should be Alpha
        
        $absensiSenin = $service->calculateDailyAttendance($pegawai, $mondayDate->toDateString());
        
        if ($absensiSenin) {
            $this->assertEquals('Alpha', $absensiSenin->status, 'Monday should be Alpha (' . $mondayDate->toDateString() . ')');
        } else {
             $this->fail('Absensi Monday returned null, expected Alpha (' . $mondayDate->toDateString() . ')');
        }

        // Case B: Wednesday (Not Wajib, No Log) -> Should be Null/Empty (Not Alpha)
        $wednesdayDate = $start->copy()->next('Wednesday');
        $absensiRabu = $service->calculateDailyAttendance($pegawai, $wednesdayDate->toDateString());

        $this->assertNull($absensiRabu, 'Wednesday should not create Alpha (' . $wednesdayDate->toDateString() . ')');

        // Cleanup
        PegawaiWajibHadir::where('pegawai_id', $pegawai->id)->delete();
        $jadwal->delete();
        if ($absensiSenin) $absensiSenin->delete();
        $pegawai->delete();
    }
}
