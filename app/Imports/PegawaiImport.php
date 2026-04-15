<?php

namespace App\Imports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements ToModel, WithHeadingRow
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
        // Skip baris yang NUPTK atau name-nya kosong (kolom wajib untuk identifikasi)
        if (empty($row['nuptk']) || empty($row['name'])) {
            $this->skippedRows[] = [
                'data'   => $row['nuptk'] ?? '-',
                'alasan' => 'Kolom "nuptk" atau "name" kosong',
            ];
            Log::info('Import Pegawai: Baris dilewati', $row);
            return null;
        }

        return Pegawai::updateOrCreate(
            ['nuptk' => $row['nuptk']], // Kunci unik
            [
                'name'                  => $row['name'],
                'status'                => $row['status']                ?? null,
                'aktif'                 => $row['aktif']                 ?? null,
                'email'                 => $row['email']                 ?? null,
                'jk'                    => $row['jk']                    ?? null,
                'kotalahir'             => $row['kota_lahir']            ?? null,
                'tanggallahir'          => $row['tanggal_lahir']         ?? null,
                'jenisptk'              => $row['jenis_ptk']             ?? null,
                'agama'                 => $row['agama']                 ?? null,
                'alamat'                => $row['alamat']                ?? null,
                'rt'                    => $row['rt']                    ?? null,
                'rw'                    => $row['rw']                    ?? null,
                'hp'                    => $row['hp']                    ?? null,
                'skpengangkatan'        => $row['sk_pengangkatan']       ?? null,
                'lembagapengangkatan'   => $row['lembaga_pengangkatan']  ?? null,
                'PangkatGolongan'       => $row['pangkat']               ?? null,
                'sumbergaji'            => $row['sumber_gaji']           ?? null,
                'ibukandung'            => $row['ibu_kandung']           ?? null,
                'kawin'                 => $row['kawin']                 ?? null,
                'suamiistri'            => $row['suami_istri']           ?? null,
                'pekerjaansuamiIstri'   => $row['kerjaistri']            ?? null,
                'npwp'                  => $row['npwp']                  ?? null,
                'nonik'                 => $row['no_nik']                ?? null,
                'nokk'                  => $row['no_kk']                 ?? null,
                'foto'                  => $row['foto']                  ?? null,
            ]
        );
    }
}
