<?php

namespace Database\Factories;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Pegawai> */
class PegawaiFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['name' => fake()->name(), 'aktif' => 'Aktif', 'status' => 'GTY'];
    }
}
