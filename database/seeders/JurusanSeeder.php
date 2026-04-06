<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::updateOrCreate(
            ['kode' => 'TKJ'], 
            ['jurusan' => 'Teknik Komputer dan Jaringan', 'deskripsi' => null]
        );
        Jurusan::updateOrCreate(
            ['kode' => 'MM'], 
            ['jurusan' => 'Multimedia', 'deskripsi' => null]
        );
        Jurusan::updateOrCreate(
            ['kode' => 'TBSM'], 
            ['jurusan' => 'Teknik dan Bisnis Sepeda Motor', 'deskripsi' => null]
        );
        Jurusan::updateOrCreate(
            ['kode' => 'TB'], 
            ['jurusan' => 'Tata Busana', 'deskripsi' => null]
        );
    }
}
// nama table yang diinput
// kode
// jurusan
// deskripsi
