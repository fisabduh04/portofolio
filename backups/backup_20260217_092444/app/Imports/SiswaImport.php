<?php

namespace App\Imports;


use App\Models\siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;



class SiswaImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){

        // $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggallahir'])->format('Y-m-d');

        return siswa::updateOrCreate(
            ['nipd' => $row['nipd']], // Kunci untuk mencari dan memutuskan apakah akan update atau insert
            [
                'nama' => $row['nama'],
                'alamat' => $row['alamat'],
                'jk' => $row['jenis_kelamin'],
                'nisn'=>$row['nisn'],
                'status'=>$row['status'],
                'aktif'=>$row['aktif'],
                'tempatlahir'=>$row['tempat_lahir'],
                'tanggallahir'=>$row['tanggallahir'],
                'nik'=>$row['nik'],
                'agama'=>$row['agama'],
                'alamat'=>$row['alamat'],
                'rt'=>$row['rt'],
                'rw'=>$row['rw'],
                'dusun'=>$row['dusun'],
                'kelurahan'=>$row['kelurahan'],
                'kecamatan'=>$row['kecamatan'],
                'kode_pos'=>$row['kode_pos'],
                'jenis_tinggal'=>$row['jenis_tinggal'],
                'alat_transportasi'=>$row['alat_transportasi'],
                'telepon'=>$row['telepon'],
                'hp'=>$row['hp'],
                'email'=>$row['email'],
                'skhun'=>$row['skhun'],
                'penerima_kps'=>$row['penerima_kps'],
                'nokps'=>$row['nokps'],
                'ayah'=>$row['ayah'],
                'tahunlahirayah'=>$row['tahunlahirayah'],
                'pendidikanayah'=>$row['pendidikanayah'],
                'pekerjaanayah'=>$row['pekerjaanayah'],
                'penghasilanayah'=>$row['penghasilanayah'],
                'nikayah'=>$row['nikayah'],
                'namaibu'=>$row['namaibu'],
                'tahunlahiribu'=>$row['tahunlahiribu'],
                'pendidikanibu'=>$row['pendidikanibu'],
                'pekerjaanibu'=>$row['pekerjaanibu'],
                'penghasilanibu'=>$row['penghasilanibu'],
                'nikibu'=>$row['nikibu'],
                'namawali'=>$row['namawali'],
                'tahunlahirwali'=>$row['tahunlahirwali'],
                'pendidikanwali'=>$row['pendidikanwali'],
                'pekerjaanwali'=>$row['pekerjaanwali'],
                'penghasilanwali'=>$row['penghasilanwali'],
                'nikwali'=>$row['nikwali'],
                'rombelsaatini'=>$row['rombelsaatini'],
                'nopesertaunas'=>$row['nopesertaunas'],
                'noijazah'=>$row['noijazah'],
                'penerimakip'=>$row['penerimakip'],
                'nomorkip'=>$row['nomorkip'],
                'namadikip'=>$row['namadikip'],
                'nomorkks'=>$row['nomorkks'],
                'noaktalahir'=>$row['noaktalahir'],
                'bank'=>$row['bank'],
                'nomor_rekening_bank'=>$row['nomor_rekening_bank'],
                'rekening_atas_nama'=>$row['rekening_atas_nama'],
                'layakpip'=>$row['layakpip'],
                'alasanlayakpip'=>$row['alasanlayakpip'],
                'kebutuhankhusus'=>$row['kebutuhankhusus'],
                'sekolahasal'=>$row['sekolahasal'],
                'anakke'=>$row['anakke'],
                'lintang'=>$row['lintang'],
                'bujur'=>$row['bujur'],
                'nokk'=>$row['nokk'],
                'beratbadan'=>$row['beratbadan'],
                'tinggibadan'=>$row['tinggibadan'],
                'lingkarkepala'=>$row['lingkarkepala'],
                'jmlsaudara'=>$row['jmlsaudara'],
                'jarakrumah'=>$row['jarakrumah'],
            ]
            );
    }
}
