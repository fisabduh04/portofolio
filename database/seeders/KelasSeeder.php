<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kelas::updateOrCreate(['kelas' => 'X A TKJ'], ['tingkat' => '1', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI A TKJ'], ['tingkat' => '2', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII A TKJ'], ['tingkat' => '3', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'X B TKJ'], ['tingkat' => '1', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI B TKJ'], ['tingkat' => '2', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII B TKJ'], ['tingkat' => '3', 'jurusan_id' => 1, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'X A MM'], ['tingkat' => '1', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI A MM'], ['tingkat' => '2', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII A MM'], ['tingkat' => '3', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'X B MM'], ['tingkat' => '1', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI B MM'], ['tingkat' => '2', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII B MM'], ['tingkat' => '3', 'jurusan_id' => 2, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'X TBSM'], ['tingkat' => '1', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI TBSM'], ['tingkat' => '2', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII TBSM'], ['tingkat' => '3', 'jurusan_id' => 3, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'X TB'], ['tingkat' => '1', 'jurusan_id' => 4, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XI TB'], ['tingkat' => '2', 'jurusan_id' => 4, 'ket' => 'aktif']);
        Kelas::updateOrCreate(['kelas' => 'XII TB'], ['tingkat' => '3', 'jurusan_id' => 4, 'ket' => 'aktif']);
    }
}
