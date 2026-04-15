<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Jurusan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KelasImport implements ToModel, WithHeadingRow
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
        // Skip baris yang kolom wajibnya kosong (mencegah error NOT NULL)
        if (empty($row['kelas'])) {
            $this->skippedRows[] = [
                'data'   => $row['kelas'] ?? '-',
                'alasan' => 'Kolom "kelas" kosong',
            ];
            Log::info('Import Kelas: Baris dilewati', $row);
            return null;
        }

        $jurusanId = Jurusan::where('jurusan', $row['jurusan'] ?? null)->pluck('id')->first();

        return Kelas::updateOrCreate(
            ['kelas' => $row['kelas']], // Kunci unik
            [
                'jurusan_id' => $jurusanId,
                'ket'        => $row['keterangan'] ?? null,
            ]
        );
    }
}
