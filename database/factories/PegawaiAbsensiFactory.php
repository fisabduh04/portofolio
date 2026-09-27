<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PegawaiAbsensi>
 */
class PegawaiAbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pegawai_id' => Pegawai::factory()->state(['jenisptk' => 'Guru']),
            'tanggal' => fake()->date(),
            'status' => 'Hadir',
            'attendance_source' => 'Fingerprint',
        ];
    }
}
