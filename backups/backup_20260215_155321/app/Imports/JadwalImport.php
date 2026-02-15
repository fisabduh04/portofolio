<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class JadwalImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip empty rows
        if (!isset($row['hari']) || !isset($row['jam'])) {
            return null;
        }

        // Helper to find ID by Name or use ID directly
        $tahunId = $this->findId(Tahun::class, $row['tahun'] ?? null, 'tahun');
        // Fallback to active year if not found
        if (!$tahunId) {
            $activeTahun = Tahun::aktif()->first();
            $tahunId = $activeTahun ? $activeTahun->id : null;
        }

        return new Jadwal([
            'tahun_id'   => $tahunId,
            'kelas_id'   => $this->findId(Kelas::class, $row['kelas'] ?? null, 'kelas'),
            'hari'       => ucfirst(strtolower($row['hari'])),
            'mapel_id'   => $this->findId(Mapel::class, $row['mapel'] ?? null, 'mapel'),
            'pegawai_id' => $this->findId(Pegawai::class, $row['guru'] ?? null, 'name'),
            'jam'        => $row['jam'],
            'mulai'      => $row['mulai'],
            'akhir'      => $row['akhir'],
            'ket'        => $row['keterangan'] ?? null,
        ]);
    }

    private function findId($model, $value, $column)
    {
        if (empty($value)) return null;

        // If it's numeric, assume it's an ID
        if (is_numeric($value)) {
            // Optional: verify it exists? For speed, we might skip verify or do a quick check
            return $value; 
        }

        // Try to find by column (e.g. name)
        // Fuzzy match or exact? Let's try exact first.
        $record = $model::where($column, $value)->first();
        if ($record) return $record->id;

        // Try 'like' search for forgiveness
        $record = $model::where($column, 'like', '%' . $value . '%')->first();
        if ($record) return $record->id;

        return null;
    }
}
