<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Logbook;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * Test dashboard page load and stats aggregation with Pulang status.
     */
    public function test_dashboard_loads_correctly_and_includes_pulang_status(): void
    {
        // 1. Setup Master Data
        $tahun = Tahun::aktif()->first();
        if (! $tahun) {
            $tahun = Tahun::create([
                'tahun' => '2024/2025',
                'semester' => 'Ganjil',
                'isActive' => 1,
                'tanggalmulai' => '2024-07-01',
                'tanggalakhir' => '2024-12-31',
            ]);
        }

        $pegawai = Pegawai::create([
            'name' => 'Dashboard Test Teacher',
            'aktif' => 'Aktif',
            'status' => 'GTY',
            'email' => 'dashboard-teacher-test@example.com',
        ]);

        $jurusan = Jurusan::firstOrCreate(
            ['kode' => 'TEST-DASHBOARD', 'jurusan' => 'TEST-DASHBOARD'],
            ['deskripsi' => 'TEST-DASHBOARD']
        );

        $kelas = Kelas::firstOrCreate(
            ['kelas' => 'X-TEST-DASHBOARD', 'jurusan_id' => $jurusan->id],
            ['ket' => 'Test']
        );

        $mapel = Mapel::firstOrCreate(
            ['mapel' => 'DASHBOARD-MAPEL', 'jurusan_id' => $jurusan->id],
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
            'status' => 'Aktif',
            'ket' => 'Test',
        ]);

        $siswaHadir = Siswa::create([
            'nama' => 'Student Hadir Dashboard',
            'nipd' => 'NIPD-'.uniqid(),
            'nisn' => '1234567891',
            'aktif' => 'Aktif',
        ]);

        $siswaPulang = Siswa::create([
            'nama' => 'Student Pulang Dashboard',
            'nipd' => 'NIPD-'.uniqid(),
            'nisn' => '1234567892',
            'aktif' => 'Aktif',
        ]);

        // Link Siswa to Kelas
        \DB::table('kelas_siswas')->insert([
            [
                'siswa_id' => $siswaHadir->id,
                'kelas_id' => $kelas->id,
                'tahun_id' => $tahun->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'siswa_id' => $siswaPulang->id,
                'kelas_id' => $kelas->id,
                'tahun_id' => $tahun->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 2. Create Attendance Records for Today
        $today = now()->toDateString();
        $logbook = Logbook::create([
            'kategori' => 'mapel',
            'tanggal' => $today,
            'jadwal_id' => $jadwal->id,
            'kelas_id' => $kelas->id,
            'pegawai_id' => $pegawai->id,
            'materi' => 'Dashboard Test Class',
            'catatan' => 'catatan dashboard',
        ]);

        $absensiHadir = Absensi::create([
            'logbook_id' => $logbook->id,
            'siswa_id' => $siswaHadir->id,
            'status' => 'Hadir',
        ]);

        $absensiPulang = Absensi::create([
            'logbook_id' => $logbook->id,
            'siswa_id' => $siswaPulang->id,
            'status' => 'Pulang',
        ]);

        // 3. Authenticate User
        $adminUser = User::whereIn('role', ['admin', 'operator'])->where('is_active', 1)->first();
        if (! $adminUser) {
            $adminUser = User::create([
                'name' => 'Admin Test Dashboard',
                'email' => 'admin-test-dashboard-'.uniqid().'@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
                'pegawai_id' => $pegawai->id,
            ]);
        }

        $this->actingAs($adminUser);

        // Share sekolah variable to bypass runningInConsole restriction in AppServiceProvider
        \Illuminate\Support\Facades\View::share('sekolah', new \App\Models\Sekolah);

        // 4. Request Dashboard Page
        $response = $this->get(route('dashboard.index'));
        $response->assertSuccessful();

        // 5. Verify Pulang & Hadir stats are reflected on page
        $response->assertSee('Hadir');
        $response->assertSee('Pulang');

        // Asserting specific numbers can be done by checking view variables
        $response->assertViewHas('todayStats', function ($todayStats) {
            return $todayStats['Hadir'] >= 1 && $todayStats['Pulang'] >= 1;
        });

        // 6. Cleanup
        $absensiHadir->delete();
        $absensiPulang->delete();
        $logbook->delete();
        \DB::table('kelas_siswas')->whereIn('siswa_id', [$siswaHadir->id, $siswaPulang->id])->delete();
        $siswaHadir->delete();
        $siswaPulang->delete();
        $jadwal->delete();
        $pegawai->delete();
    }
}
