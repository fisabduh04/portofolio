<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapBulananExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $dates;
    protected $month;
    protected $year;
    protected $kelasName;

    public function __construct($data, $dates, $month, $year, $kelasName)
    {
        $this->data = $data;
        $this->dates = $dates;
        $this->month = $month;
        $this->year = $year;
        $this->kelasName = $kelasName;
    }

    public function view(): View
    {
        return view('absensi.export_rekap_bulanan', [
            'rekapData' => $this->data,
            'dates' => $this->dates,
            'month' => $this->month,
            'year' => $this->year,
            'kelas' => $this->kelasName
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]], // Table Header
            4 => ['font' => ['bold' => true]], // Table Header
        ];
    }
}
