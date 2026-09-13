<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class WaliKelasExport extends StringValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    public function __construct(private Builder $assignments) {}

    public function query(): Builder
    {
        return $this->assignments->with(['tahun', 'kelas', 'pegawai']);
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return ['tahun', 'semester', 'kelas', 'pegawai_id', 'nuptk', 'nama', 'status', 'keterangan'];
    }

    /** @return array<int, mixed> */
    public function map(mixed $row): array
    {
        return [$row->tahun->tahun, $row->tahun->semester, $row->kelas->kelas, $row->pegawai_id,
            $row->pegawai->nuptk, $row->pegawai->name, $row->is_active ? 'aktif' : 'nonaktif', $row->keterangan];
    }
}
