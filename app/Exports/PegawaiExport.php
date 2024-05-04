<?php

namespace App\Exports;

use App\Models\pegawai;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PegawaiExport implements FromQuery, ShouldAutoSize, WithMapping, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return pegawai::query();
    }
    public function map($pegawai):array
    {
        return[
            $pegawai->name,
            $pegawai->nuptk,
            $pegawai->email,
            $pegawai->jk,
            $pegawai->kotalahir,
            $pegawai->tanggallahir,
            $pegawai->jenisptk,
            $pegawai->agama,
            $pegawai->alamat,
            $pegawai->rt,
            $pegawai->rw,
            $pegawai->hp,
            $pegawai->skpengangkatan,
            $pegawai->lembagapengangkatan,
            $pegawai->PangkatGolongan,
            $pegawai->sumbergaji,
            $pegawai->ibukandung,
            $pegawai->statusPerkawinan,
            $pegawai->suamiistri,
            $pegawai->pekerjaansuamiIstri,
            $pegawai->npwp,
            $pegawai->nonik,
            $pegawai->nokk,
            $pegawai->foto,
        ];
    }
    public function headings():array
    {
        return[
            'name',
            'nuptk',
            'email',
            'jk',
            'kota_lahir',
            'tanggal_lahir',
            'jenis_ptk',
            'agama',
            'alamat',
            'rt',
            'rw',
            'hp',
            'sk_pengangkatan',
            'lembaga_pengangkatan',
            'pangkat',
            'sumber_gaji',
            'ibu_kandung',
            'status',
            'suami_istri',
            'kerjaistri',
            'npwp',
            'no_nik',
            'no_kk',
            'foto',
        ];
    }
}
