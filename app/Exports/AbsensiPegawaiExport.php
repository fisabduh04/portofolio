<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiPegawaiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $attendanceData;
    protected $pegawai;
    protected $dates;
    protected $month;
    protected $year;
    protected $sekolah;
    protected $months;

    public function __construct($attendanceData, $pegawai, $dates, $month, $year, $sekolah, $months)
    {
        $this->attendanceData = $attendanceData;
        $this->pegawai = $pegawai;
        $this->dates = $dates;
        $this->month = $month;
        $this->year = $year;
        $this->sekolah = $sekolah;
        $this->months = $months;
    }

    public function view(): View
    {
        return view('attendance.report.export_employee_excel', [
            'attendanceData' => $this->attendanceData,
            'pegawai' => $this->pegawai,
            'dates' => $this->dates,
            'month' => $this->month,
            'year' => $this->year,
            'sekolah' => $this->sekolah,
            'months' => $this->months
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Nama Sekolah
            3 => ['font' => ['bold' => true, 'underline' => true]], // Judul Laporan
            11 => ['font' => ['bold' => true]], // Table Header
        ];
    }
}
