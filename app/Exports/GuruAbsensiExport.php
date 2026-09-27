<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GuruAbsensiExport implements FromView, ShouldAutoSize
{
    /** @param array<string, mixed> $data */
    public function __construct(private array $data) {}

    public function view(): View
    {
        return view('attendance.export_rekap_guru', $this->data);
    }
}
