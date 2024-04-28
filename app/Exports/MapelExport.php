<?php

namespace App\Exports;

use App\Models\mapel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MapelExport implements FromQuery,WithMapping, ShouldAutoSize, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return mapel::query();
    }
    public function map($mapel):array
    {
        return[
            $mapel->kode,
            $mapel->mapel,
            $mapel->jurusan->jurusan,
            $mapel->ket
        ];
    }
    public function headings():array
    {
        return[
            'Kode',
            'mata_pelajaran',
            'jurusan',
            'keterangan'
        ];
    }
}
