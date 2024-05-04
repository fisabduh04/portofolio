<?php

namespace App\Imports;

use App\Models\mapel;
use App\Models\jurusan;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class mapelImport implements ToModel, WithHeadingRow
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
        return Mapel::updateOrCreate(
            ['kode' => $row['kode']], // Kolom yang digunakan sebagai kunci unik
            [
                'mapel' => $row['mata_pelajaran'],
                'jurusan_id' => $jurusanId,
                'ket' => $row['keterangan'],
            ]
        );
    }
}
