<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Kelas> */
class KelasFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['kelas' => 'X-'.fake()->unique()->numerify('####'), 'jurusan_id' => Jurusan::factory()];
    }
}
