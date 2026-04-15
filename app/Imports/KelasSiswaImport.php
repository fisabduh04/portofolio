<?php

namespace App\Imports;

use App\Models\KelasSiswa;
use App\Models\Tahun;
use App\Models\Kelas;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KelasSiswaImport implements ToModel, WithHeadingRow
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
        // Skip baris yang NIPD atau Kelas-nya kosong (kolom wajib)
        if (empty($row['nipd']) || empty($row['kelas'])) {
            $this->skippedRows[] = [
                'data'   => $row['nipd'] ?? '-',
                'alasan' => 'Kolom "nipd" atau "kelas" kosong',
            ];
            Log::info('Import KelasSiswa: Baris dilewati', $row);
            return null;
        }

        $siswaId = Siswa::where('nipd', $row['nipd'])->pluck('id')->first();
        $kelasId = Kelas::where('kelas', $row['kelas'])->pluck('id')->first();
        $tahunId = Tahun::where('tahun', $row['tahun'] ?? null)
                        ->where('semester', $row['semester'] ?? null)
                        ->pluck('id')->first();

        // Skip jika siswa tidak ditemukan di database
        if (!$siswaId) {
            $this->skippedRows[] = [
                'data'   => $row['nipd'],
                'alasan' => "NIPD '{$row['nipd']}' tidak ditemukan di database",
            ];
            Log::info("Import KelasSiswa: NIPD {$row['nipd']} tidak ditemukan", $row);
            return null;
        }

        return KelasSiswa::updateOrCreate(
            [
                'siswa_id' => $siswaId,
                'tahun_id' => $tahunId,
            ],
            [
                'kelas_id' => $kelasId,
                'ket'      => $row['ket'] ?? null,
            ]
        );
    }
}
