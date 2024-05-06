<?php

namespace App\Exports;

use App\Models\jadwal;
use Maatwebsite\Excel\Concerns\FromCollection;

class jadwalExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return jadwal::all();
    }
}
