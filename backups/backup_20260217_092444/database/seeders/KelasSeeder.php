<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kelas::create(['tingkat' => '1', 'kelas' => 'X A TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI A TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII A TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '1', 'kelas' => 'X B TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI B TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII B TKJ', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '1', 'kelas' => 'X A MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI A MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII A MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '1', 'kelas' => 'X B MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI B MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII B MM', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '1', 'kelas' => 'X TBSM', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI TBSM', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII TBSM', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '1', 'kelas' => 'X TB', 'jurusan_id' => 4, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '2', 'kelas' => 'XI TB', 'jurusan_id' => 4, 'ket' => 'aktif']);
        Kelas::create(['tingkat' => '3', 'kelas' => 'XII TB', 'jurusan_id' => 4, 'ket' => 'aktif']);
    }
}
