<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ImportMapel implements ToModel, WithHeadingRow
{
    use Importable;

    // Menyimpan daftar baris yang dilewati beserta alasannya
    public array $skippedRows = [];

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip baris yang kode atau mata_pelajaran-nya kosong
        // Ini mencegah error "Column 'mapel' cannot be null"
        if (empty($row['kode']) || empty($row['mata_pelajaran'])) {
            $this->skippedRows[] = [
                'data'   => $row['kode'] ?? '-',
                'alasan' => 'Kolom "kode" atau "mata_pelajaran" kosong',
            ];
            Log::info('Import Mapel: Baris dilewati karena kode/mata_pelajaran kosong', $row);
            return null; // return null = skip baris ini
        }

        $jurusanId = Jurusan::where('jurusan', $row['jurusan'])->pluck('id')->first();

        return Mapel::updateOrCreate(
            ['kode' => $row['kode']], // Kolom yang digunakan sebagai kunci unik
            [
                'mapel'      => $row['mata_pelajaran'],
                'jurusan_id' => $jurusanId,
                'ket'        => $row['keterangan'] ?? 'aktif', // Default 'aktif' jika kosong
            ]
        );
    }
}
