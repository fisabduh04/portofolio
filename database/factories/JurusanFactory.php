<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Jurusan> */
class JurusanFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['kode' => fake()->unique()->bothify('J-####??'), 'jurusan' => fake()->unique()->bothify('Jurusan ####??')];
    }
}
