<?php

namespace Database\Factories;

use App\Models\Jadwal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jadwal>
 */
class JadwalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tahun_id' => \App\Models\Tahun::factory(),
            'kelas_id' => \App\Models\Kelas::factory(),
            'mapel_id' => \App\Models\Mapel::factory(),
            'pegawai_id' => \App\Models\Pegawai::factory(),
            'hari' => 'Senin',
            'jam' => 1,
            'mulai' => '07:00',
            'akhir' => '08:00',
        ];
    }
}
