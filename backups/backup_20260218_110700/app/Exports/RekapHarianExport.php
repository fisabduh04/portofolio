<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapHarianExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $date;
    protected $kelasName;
    protected $typeGuru;

    public function __construct($data, $date, $kelasName, $typeGuru = 'mapel')
    {
        $this->data = $data;
        $this->date = $date;
        $this->kelasName = $kelasName;
        $this->typeGuru = $typeGuru;
    }

    public function view(): View
    {
        return view('absensi.export_rekap_harian', [
            'rekapData' => $this->data,
            'date' => $this->date,
            'kelas' => $this->kelasName,
            'typeGuru' => $this->typeGuru
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Header Title
            3 => ['font' => ['bold' => true]], // Table Header
        ];
    }
}
