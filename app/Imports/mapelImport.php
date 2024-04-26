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

        // return new mapel([
        //     'kode'=>$row[0],
        //     'mapel'=>$row[1],
        //     'jurusan_id'=>$row[2],
        //     'ket'=>$row[3],
        // ]);

        return Mapel::updateOrCreate(
            ['kode' => $row['kode']], // Kolom yang digunakan sebagai kunci unik
            [
                'mapel' => $row['mapel'],
                'jurusan_id' => $row['jurusan_id'],
                'ket' => $row['ket'],
            ]
        );
    }
}
