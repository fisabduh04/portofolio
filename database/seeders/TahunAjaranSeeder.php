<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\tahun;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        tahun::create(['tahun'=>'Tahun Ajaran 2023/2024',
        'semester'=>'Genap',
        'tanggalmulai'=>'2024-02-01',
        'tanggalakhir'=>'2024-06-01',
        'isActive'=>'1',
        'deskripsi'=>'',
    ]);

        tahun::create(['tahun'=>'Tahun Ajaran 2024/2025',
        'semester'=>'Ganjil',
        'tanggalmulai'=>'2024-06-01',
        'tanggalakhir'=>'2024-12-01',
        'isActive'=>'0',
        'deskripsi'=>'',
    ]);
    }
}
// tahun
// semester
// tanggalmulai
// tanggalakhir
// isActive
// deskripsi
