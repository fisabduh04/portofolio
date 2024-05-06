<?php

namespace App\Exports;

use App\Models\siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class Siswaexport implements FromQuery, ShouldAutoSize, WithMapping, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return siswa::query();

    }
    public function map($siswa):array
    {
        return[
            $siswa->nama,
            $siswa->nipd,
            $siswa->jk,
            $siswa->nisn,
            $siswa->status,
            $siswa->aktif,
            $siswa->tempatlahir,
            $siswa->tanggallahir,
            $siswa->nik,
            $siswa->agama,
            $siswa->alamat,
            $siswa->rt,
            $siswa->rw,
            $siswa->dusun,
            $siswa->kelurahan,
        $siswa->kecamatan,
        $siswa->kode_pos,
        $siswa->jenis_tinggal,
        $siswa->alat_transportasi,
        $siswa->telepon,
        $siswa->hp,
        $siswa->email,
        $siswa->skhun,
        $siswa->penerima_kps,
        $siswa->nokps,
        $siswa->ayah,
        $siswa->tahunlahirayah,
        $siswa->pendidikanayah,
        $siswa->pekerjaanayah,
        $siswa->penghasilanayah,
        $siswa->nikayah,
        $siswa->namaibu,
        $siswa->tahunlahiribu,
        $siswa->pendidikanibu,
        $siswa->pekerjaanibu,
        $siswa->penghasilanibu,
        $siswa->nikibu,
        $siswa->namawali,
        $siswa->tahunlahirwali,
        $siswa->pendidikanwali,
        $siswa->pekerjaanwali,
        $siswa->penghasilanwali,
        $siswa->nikwali,
        $siswa->rombelsaatini,
        $siswa->nopesertaunas,
        $siswa->noijazah,
        $siswa->penerimakip,
        $siswa->nomorkip,
        $siswa->namadikip,
        $siswa->nomorkks,
        $siswa->noaktalahir,
        $siswa->bank,
        $siswa->nomor_rekening_bank,
        $siswa->rekening_atas_nama,
        $siswa->layakpip,
        $siswa->alasanlayakpip,
        $siswa->kebutuhankhusus,
        $siswa->sekolahasal,
        $siswa->anakke,
        $siswa->lintang,
        $siswa->bujur,
        $siswa->nokk,
        $siswa->beratbadan,
        $siswa->tinggibadan,
        $siswa->lingkarkepala,
        $siswa->jmlsaudara,
        ];
    }
    public function headings():array
    {
        return[
            'nama',
            'nipd',
            'jenis_kelamin',
            'nisn',
            'status',
            'aktif',
            'tempat_lahir',
            'tanggallahir',
            'nik',
            'Agama',
            'Alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
        'kecamatan',
        'kode_pos',
        'jenis_tinggal',
        'alat_transportasi',
        'telepon',
        'hp',
        'email',
        'skhun',
        'penerima_kps',
        'nokps',
        'ayah',
        'tahunlahirayah',
        'pendidikanayah',
        'pekerjaanayah',
        'penghasilanayah',
        'nikayah',
        'namaibu',
        'tahunlahiribu',
        'pendidikanibu',
        'pekerjaanibu',
        'penghasilanibu',
        'nikibu',
        'namawali',
        'tahunlahirwali',
        'pendidikanwali',
        'pekerjaanwali',
        'penghasilanwali',
        'nikwali',
        'rombelsaatini',
        'nopesertaunas',
        'noijazah',
        'penerimakip',
        'nomorkip',
        'namadikip',
        'nomorkks',
        'noaktalahir',
        'bank',
        'nomor_rekening bank',
        'rekening_atas_nama',
        'layakpip',
        'alasanlayakpip',
        'kebutuhankhusus',
        'sekolahasal',
        'anakke',
        'lintang',
        'bujur',
        'nokk',
        'beratbadan',
        'tinggibadan',
        'lingkarkepala',
        'jmlsaudara',
        'jarakrumah',
        ];
    }
}
