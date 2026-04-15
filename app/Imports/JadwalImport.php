<?php

namespace App\Imports;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class JadwalImport implements ToModel, WithHeadingRow
{
    // Menyimpan daftar baris yang dilewati beserta alasannya
    public array $skippedRows = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip baris yang hari atau jam-nya kosong (kolom wajib jadwal)
        if (empty($row['hari']) || empty($row['jam'])) {
            $this->skippedRows[] = [
                'data'   => ($row['hari'] ?? '-') . ' / ' . ($row['jam'] ?? '-'),
                'alasan' => 'Kolom "hari" atau "jam" kosong',
            ];
            Log::info('Import Jadwal: Baris dilewati', $row);
            return null;
        }

        // Cari tahun aktif sebagai fallback jika kolom tahun di Excel tidak diisi
        $tahunId = $this->findId(Tahun::class, $row['tahun'] ?? null, 'tahun');
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
            'mulai'      => $row['mulai']      ?? null,
            'akhir'      => $row['akhir']      ?? null,
            'ket'        => $row['keterangan'] ?? null,
        ]);
    }

    /**
     * Cari ID record berdasarkan nama atau langsung gunakan jika sudah berupa ID angka.
     * Mendukung pencarian exact dan fuzzy (LIKE) sebagai fallback.
     */
    private function findId($model, $value, $column)
    {
        if (empty($value)) return null;

        // Jika sudah angka, asumsikan itu adalah ID langsung
        if (is_numeric($value)) {
            return $value;
        }

        // Cari exact match terlebih dahulu
        $record = $model::where($column, $value)->first();
        if ($record) return $record->id;

        // Fallback: cari dengan LIKE (lebih fleksibel)
        $record = $model::where($column, 'like', '%' . $value . '%')->first();
        if ($record) return $record->id;

        return null;
    }
}
