<?php

namespace App\Exports;

use App\Models\KelasSiswa;
use App\Models\siswa;
use App\Models\kelas;
use App\Models\tahun;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KelasSiswaExport implements FromQuery,WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
       return KelasSiswa::query();
    }

    public function map($KelasSiswa):array
    {
        return[
            $KelasSiswa->tahun->tahun,
            $KelasSiswa->tahun->semester,
            $KelasSiswa->kelas->kelas,
            $KelasSiswa->siswa->nipd,
            $KelasSiswa->siswa->nama,
            $KelasSiswa->ket
        ];
    }
    public function headings():array
    {
        return[
            'tahun',
            'semester',
            'kelas',
            'nipd',
            'nama',
            'ket'
        ];
    }
}
