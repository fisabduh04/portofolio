<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapTahunanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $year;
    protected $kelasName;

    public function __construct($data, $year, $kelasName)
    {
        $this->data = $data;
        $this->year = $year;
        $this->kelasName = $kelasName;
    }

    public function view(): View
    {
        return view('absensi.export_rekap_tahunan', [
            'rekapData' => $this->data,
            'year' => $this->year,
            'kelas' => $this->kelasName
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]], 
            4 => ['font' => ['bold' => true]], 
        ];
    }
}
