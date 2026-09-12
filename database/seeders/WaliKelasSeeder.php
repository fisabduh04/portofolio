<?php

namespace Database\Seeders;

use App\Models\WaliKelas;
use Illuminate\Database\Seeder;

class WaliKelasSeeder extends Seeder
{
    public function run(): void
    {
        WaliKelas::factory()->count(3)->create();
    }
}
