<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToModel, WithHeadingRow
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
        // Skip baris yang NIPD atau nama-nya kosong (kolom wajib untuk identifikasi)
        if (empty($row['nipd']) || empty($row['nama'])) {
            $this->skippedRows[] = [
                'data'   => $row['nipd'] ?? '-',
                'alasan' => 'Kolom "nipd" atau "nama" kosong',
            ];
            Log::info('Import Siswa: Baris dilewati', $row);
            return null;
        }

        return Siswa::updateOrCreate(
            ['nipd' => $row['nipd']], // Kunci unik untuk update atau insert
            [
                'nama'                  => $row['nama'],
                'jk'                    => $row['jenis_kelamin']         ?? null,
                'nisn'                  => $row['nisn']                  ?? null,
                'status'                => $row['status']                ?? null,
                'aktif'                 => $row['aktif']                 ?? null,
                'tempatlahir'           => $row['tempat_lahir']          ?? null,
                'tanggallahir'          => $row['tanggallahir']          ?? null,
                'nik'                   => $row['nik']                   ?? null,
                'agama'                 => $row['agama']                 ?? null,
                'alamat'                => $row['alamat']                ?? null,
                'rt'                    => $row['rt']                    ?? null,
                'rw'                    => $row['rw']                    ?? null,
                'dusun'                 => $row['dusun']                 ?? null,
                'kelurahan'             => $row['kelurahan']             ?? null,
                'kecamatan'             => $row['kecamatan']             ?? null,
                'kode_pos'              => $row['kode_pos']              ?? null,
                'jenis_tinggal'         => $row['jenis_tinggal']         ?? null,
                'alat_transportasi'     => $row['alat_transportasi']     ?? null,
                'telepon'               => $row['telepon']               ?? null,
                'hp'                    => $row['hp']                    ?? null,
                'email'                 => $row['email']                 ?? null,
                'skhun'                 => $row['skhun']                 ?? null,
                'penerima_kps'          => $row['penerima_kps']          ?? null,
                'nokps'                 => $row['nokps']                 ?? null,
                'ayah'                  => $row['ayah']                  ?? null,
                'tahunlahirayah'        => $row['tahunlahirayah']        ?? null,
                'pendidikanayah'        => $row['pendidikanayah']        ?? null,
                'pekerjaanayah'         => $row['pekerjaanayah']        ?? null,
                'penghasilanayah'       => $row['penghasilanayah']       ?? null,
                'nikayah'               => $row['nikayah']               ?? null,
                'namaibu'               => $row['namaibu']               ?? null,
                'tahunlahiribu'         => $row['tahunlahiribu']         ?? null,
                'pendidikanibu'         => $row['pendidikanibu']         ?? null,
                'pekerjaanibu'          => $row['pekerjaanibu']          ?? null,
                'penghasilanibu'        => $row['penghasilanibu']        ?? null,
                'nikibu'                => $row['nikibu']                ?? null,
                'namawali'              => $row['namawali']              ?? null,
                'tahunlahirwali'        => $row['tahunlahirwali']        ?? null,
                'pendidikanwali'        => $row['pendidikanwali']        ?? null,
                'pekerjaanwali'         => $row['pekerjaanwali']        ?? null,
                'penghasilanwali'       => $row['penghasilanwali']       ?? null,
                'nikwali'               => $row['nikwali']               ?? null,
                'rombelsaatini'         => $row['rombelsaatini']         ?? null,
                'nopesertaunas'         => $row['nopesertaunas']         ?? null,
                'noijazah'              => $row['noijazah']              ?? null,
                'penerimakip'           => $row['penerimakip']           ?? null,
                'nomorkip'              => $row['nomorkip']              ?? null,
                'namadikip'             => $row['namadikip']             ?? null,
                'nomorkks'              => $row['nomorkks']              ?? null,
                'noaktalahir'           => $row['noaktalahir']           ?? null,
                'bank'                  => $row['bank']                  ?? null,
                'nomor_rekening_bank'   => $row['nomor_rekening_bank']   ?? null,
                'rekening_atas_nama'    => $row['rekening_atas_nama']    ?? null,
                'layakpip'              => $row['layakpip']              ?? null,
                'alasanlayakpip'        => $row['alasanlayakpip']        ?? null,
                'kebutuhankhusus'       => $row['kebutuhankhusus']       ?? null,
                'sekolahasal'           => $row['sekolahasal']           ?? null,
                'anakke'                => $row['anakke']                ?? null,
                'lintang'               => $row['lintang']               ?? null,
                'bujur'                 => $row['bujur']                 ?? null,
                'nokk'                  => $row['nokk']                  ?? null,
                'beratbadan'            => $row['beratbadan']            ?? null,
                'tinggibadan'           => $row['tinggibadan']           ?? null,
                'lingkarkepala'         => $row['lingkarkepala']         ?? null,
                'jmlsaudara'            => $row['jmlsaudara']            ?? null,
                'jarakrumah'            => $row['jarakrumah']            ?? null,
            ]
        );
    }
}
