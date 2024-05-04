<?php

namespace App\Imports;

use App\Models\pegawai;
use App\Models\jurusan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PegawaiImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // return new pegawai([
        //     //
        // ]);
        {
            return pegawai::updateOrCreate(
                ['nuptk' => $row['nuptk']], // Kolom yang digunakan sebagai kunci unik
                [
                   'name'=>$row['name'],
                   'email'=>$row['email'],
                   'jk'=>$row['jk'],
                   'kotalahir'=>$row['kota_lahir'],
                'tanggallahir'=>$row['tanggal_lahir'],
                   'jenisptk'=>$row['jenis_ptk'],
                   'agama'=>$row['agama'],
                   'alamat'=>$row['alamat'],
                   'rt'=>$row['rt'],
                   'rw'=>$row['rw'],
                   'hp'=>$row['hp'],
                   'skpengangkatan'=>$row['sk_pengangkatan'],
                   'lembagapengangkatan'=>$row['lembaga_pengangkatan'],
                   'PangkatGolongan'=>$row['pangkat'],
                   'sumbergaji'=>$row['sumber_gaji'],
                   'ibukandung'=>$row['ibu_kandung'],
                   'statusPerkawinan'=>$row['status'],
                   'suamiistri'=>$row['suami_istri'],
                   'pekerjaansuamiIstri'=>$row['kerjaistri'],
                   'npwp'=>$row['npwp'],
                   'nonik'=>$row['no_nik'],
                   'nokk'=>$row['no_kk'],
                   'foto'=>$row['foto'],
                ]
            );
        }
    }
}
