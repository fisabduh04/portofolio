<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tahun;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tahun::updateOrCreate(
            ['tahun' => 'Tahun Ajaran 2025/2026', 'semester' => 'Genap'],
            [
                'tanggalmulai' => '2025-02-01',
                'tanggalakhir' => '2025-06-01',
                'isActive' => '1',
                'deskripsi' => null,
            ]
        );

        Tahun::updateOrCreate(
            ['tahun' => 'Tahun Ajaran 2025/2026', 'semester' => 'Ganjil'],
            [
                'tanggalmulai' => '2025-06-01',
                'tanggalakhir' => '2025-12-01',
                'isActive' => '0',
                'deskripsi' => null,
            ]
        );
    }
}
// tahun
// semester
// tanggalmulai
// tanggalakhir
// isActive
// deskripsi
