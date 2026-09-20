<?php

namespace App\Exports;

use App\Models\Pegawai;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PegawaiExport extends DefaultValueBinder implements FromQuery, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    use Exportable;

    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (in_array($cell->getColumn(), ['B', 'N', 'O', 'W', 'X', 'Y'], true) && $cell->getRow() > 1 && $value !== null) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            $cell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return pegawai::query();
    }

    public function map($pegawai): array
    {
        return [
            $pegawai->name,
            $pegawai->nuptk,
            $pegawai->email,
            $pegawai->status,
            $pegawai->aktif,
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

    public function headings(): array
    {
        return [
            'name',
            'nuptk',
            'email',
            'status',
            'aktif',
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
            'kawin',
            'suami_istri',
            'kerjaistri',
            'npwp',
            'no_nik',
            'no_kk',
            'foto',
        ];
    }
}
