<?php

namespace App\Exports;

use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\Kelas;
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
    protected $ids;
    protected $activeYearId;

    public function __construct($ids = null, $activeYearId = null)
    {
        $this->ids = $ids;
        $this->activeYearId = $activeYearId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
       $query = KelasSiswa::query()->with(['siswa', 'kelas', 'tahun']);

       if (!empty($this->ids)) {
           $query->whereIn('id', $this->ids);
       } elseif ($this->activeYearId) {
           $query->where('tahun_id', $this->activeYearId);
       }

       return $query;
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
