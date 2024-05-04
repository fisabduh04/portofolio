<?php

namespace App\Imports;

use App\Models\jurusan;
use App\Models\kelas;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $jurusanId=jurusan::where('jurusan',$row['jurusan'])->pluck('id')->first();
        return kelas::updateOrCreate(
            ['id' => $row['id']], // Kolom yang digunakan sebagai kunci unik
            [
                'kelas' => $row['kelas'],
                'jurusan_id' => $jurusanId,
                'ket' => $row['keterangan'],
            ]
        );
    }
}
