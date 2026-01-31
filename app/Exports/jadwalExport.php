<?php

namespace App\Exports;

use App\Models\Jadwal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class JadwalExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    use Exportable;

    protected $ids;

    public function __construct($ids = null)
    {
        // $ids can be an array or a comma-separated string
        if (is_string($ids)) {
            $this->ids = explode(',', $ids);
        } else {
            $this->ids = $ids;
        }
    }

    public function query()
    {
        $query = Jadwal::query()->with(['tahun', 'kelas', 'mapel', 'pegawai']);
        
        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }
        
        return $query;
    }

    public function map($jadwal): array
    {
        return [
            $jadwal->id,
            $jadwal->tahun ? $jadwal->tahun->tahun . ' - ' . $jadwal->tahun->semester : '',
            $jadwal->kelas->kelas ?? '',
            $jadwal->hari,
            $jadwal->mapel->mapel ?? '',
            $jadwal->pegawai->name ?? '',
            $jadwal->jam,
            $jadwal->mulai,
            $jadwal->akhir,
            $jadwal->ket
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tahun',
            'Kelas',
            'Hari',
            'Mapel',
            'Guru',
            'Jam',
            'Mulai',
            'Akhir',
            'Keterangan'
        ];
    }
}
