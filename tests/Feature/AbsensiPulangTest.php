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
use App\Traits\AbsensiRekapTrait;
use Tests\TestCase;

class AbsensiRekapTestClass
{
    use AbsensiRekapTrait;

    public function getRekap($date, $kelasId, $pegawaiId, $typeGuru)
    {
        return $this->getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru);
    }
}

class AbsensiPulangTest extends TestCase
{
    public function test_can_store_student_attendance_with_pulang_status_and_count_it_in_rekap()
    {
        // 1. Setup Data
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
            'name' => 'Absensi Test Teacher',
            'aktif' => 'Aktif',
            'status' => 'GTY',
            'email' => 'teacher-test@example.com',
        ]);

        $jurusan = Jurusan::firstOrCreate(
            ['kode' => 'TEST', 'jurusan' => 'TEST'],
            ['deskripsi' => 'TEST']
        );

        $kelas = Kelas::firstOrCreate(
            ['kelas' => 'X-TEST', 'jurusan_id' => $jurusan->id],
            ['ket' => 'Test']
        );

        $mapel = Mapel::firstOrCreate(
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
            'status' => 'Aktif',
            'ket' => 'Test',
        ]);

        $siswa = Siswa::create([
            'nama' => 'Test Student Pulang',
            'nipd' => 'NIPD-'.uniqid(),
            'nisn' => '1234567890',
            'aktif' => 'Aktif',
        ]);

        // Link Siswa to Kelas
        \DB::table('kelas_siswas')->insert([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_id' => $tahun->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Authenticate
        $adminUser = User::whereIn('role', ['admin', 'operator'])->where('is_active', 1)->first();
        if (! $adminUser) {
            $adminUser = User::create([
                'name' => 'Admin Test',
                'email' => 'admin-test-'.uniqid().'@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
                'pegawai_id' => $pegawai->id,
            ]);
        } else {
            // Ensure adminUser has pegawai_id for the test
            $adminUser->update(['pegawai_id' => $pegawai->id]);
        }

        $this->actingAs($adminUser);

        // 3. Post to Absensi Store
        $date = '2024-07-08';
        $response = $this->post(route('absensi.store'), [
            'jadwal_id' => $jadwal->id,
            'materi' => 'Materi Tes Pulang',
            'catatan' => 'Catatan Tes Pulang',
            'kategori' => 'mapel',
            'tanggal' => $date,
            'attendance' => [
                $siswa->id => [
                    'status' => 'Pulang',
                    'keterangan' => 'Pulang cepat karena keperluan keluarga',
                ],
            ],
        ]);

        $response->assertRedirect();

        // 4. Verify in Database
        $this->assertDatabaseHas('absensis', [
            'siswa_id' => $siswa->id,
            'status' => 'Pulang',
            'keterangan' => 'Pulang cepat karena keperluan keluarga',
        ]);

        // 5. Test Rekap Data
        $rekapHelper = new AbsensiRekapTestClass;
        $rekap = $rekapHelper->getRekap($date, $kelas->id, $pegawai->id, 'mapel');

        $studentRekap = $rekap['rekapData']->firstWhere('id', $siswa->id);
        $this->assertNotNull($studentRekap);
        $this->assertEquals('Pulang', $studentRekap->daily_status);
        $this->assertEquals(1, $studentRekap->stats['Pulang']);

        // 6. Cleanup
        Absensi::where('siswa_id', $siswa->id)->delete();
        Logbook::where('jadwal_id', $jadwal->id)->delete();
        \DB::table('kelas_siswas')->where('siswa_id', $siswa->id)->delete();
        $siswa->delete();
        $jadwal->delete();
        $pegawai->delete();
    }
}
