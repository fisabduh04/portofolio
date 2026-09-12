<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\WaliKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WaliKelas> */
class WaliKelasFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['tahun_id' => Tahun::factory(), 'kelas_id' => Kelas::factory(), 'pegawai_id' => Pegawai::factory(), 'is_active' => true, 'keterangan' => null];
    }
}
