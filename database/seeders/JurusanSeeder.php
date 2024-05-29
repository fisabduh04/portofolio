<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\jurusan;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        jurusan::create(['kode'=>'TKJ','jurusan'=>'Teknik Komputer dan Jaringan','deskripsi'=>'']);
        jurusan::create(['kode'=>'MM','jurusan'=>'Multimedia','deskripsi'=>'']);
        jurusan::create(['kode'=>'TBSM','jurusan'=>'Teknik dan Bisnis Sepeda Motor','deskripsi'=>'']);
        jurusan::create(['kode'=>'TB','jurusan'=>'Tata Busana','deskripsi'=>'']);
    }
}
// nama table yang diinput
// kode
// jurusan
// deskripsi
