<?php

namespace App\Exports;

use App\Models\kelas;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KelasExport implements FromQuery,WithMapping, ShouldAutoSize, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return kelas::query();
    }
    public function map($kelas):array
    {
        return[
            $kelas->id,
            $kelas->kelas,
            $kelas->jurusan->jurusan,
            $kelas->ket
        ];
    }
    public function headings():array
    {
        return[
            'id',
            'kelas',
            'jurusan',
            'keterangan'
        ];
    }
}
