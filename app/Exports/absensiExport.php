<?php

namespace App\Exports;

use App\Models\absensi;
use Maatwebsite\Excel\Concerns\FromCollection;

class absensiExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return absensi::all();
    }
}
