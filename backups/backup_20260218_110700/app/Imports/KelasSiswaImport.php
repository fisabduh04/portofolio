<?php

namespace App\Imports;

use App\Models\KelasSiswa;
use App\Models\tahun;
use App\Models\kelas;
use App\Models\siswa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasSiswaImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $siswaId=siswa::where('nipd',$row['nipd'])->pluck('id')->first();
        $kelasId=kelas::where('kelas',$row['kelas'])->pluck('id')->first();
        $tahunId=tahun::where('tahun',$row['tahun'])
                        ->where('semester',$row['semester'])
                        ->pluck('id')->first();
        return KelasSiswa::updateOrCreate(
            ['siswa_id'=>$siswaId,
            'tahun_id'=>$tahunId],
            [
                'kelas_id'=>$kelasId,
                'ket'=>$row['ket']

            ]
    );
    }
}
