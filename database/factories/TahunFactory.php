<?php

namespace Database\Factories;

use App\Models\Tahun;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Tahun> */
class TahunFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['tahun' => (string) fake()->unique()->numberBetween(2000, 9000), 'semester' => 'Ganjil', 'tanggalmulai' => '2026-07-01', 'tanggalakhir' => '2026-12-31', 'isActive' => false];
    }
}
