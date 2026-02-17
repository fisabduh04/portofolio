<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Siswa extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama', 'nipd', 'jk', 'nisn', 'status', 'aktif', 'tempatlahir', 'tanggallahir', 'nik', 'agama', 
        'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'jenis_tinggal', 
        'alat_transportasi', 'telepon', 'hp', 'email', 'skhun', 'penerima_kps', 'nokps', 
        'ayah', 'tahunlahirayah', 'pendidikanayah', 'pekerjaanayah', 'penghasilanayah', 'nikayah', 
        'namaibu', 'tahunlahiribu', 'pendidikanibu', 'pekerjaanibu', 'penghasilanibu', 'nikibu', 
        'namawali', 'tahunlahirwali', 'pendidikanwali', 'pekerjaanwali', 'penghasilanwali', 'nikwali', 
        'rombelsaatini', 'nopesertaunas', 'noijazah', 'penerimakip', 'nomorkip', 'namadikip', 
        'nomorkks', 'noaktalahir', 'bank', 'nomor_rekening_bank', 'rekening_atas_nama', 'layakpip', 
        'alasanlayakpip', 'kebutuhankhusus', 'sekolahasal', 'anakke', 'lintang', 'bujur', 'nokk', 
        'beratbadan', 'tinggibadan', 'lingkarkepala', 'jmlsaudara', 'jarakrumah', 'deskripsi', 'foto'
    ];


    public function KelasSiswa(){
        return $this->hasMany(KelasSiswa::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswas', 'kelas_id', 'tahun_id')
        ->withPivot('kelas_id')
        ->withTimestamps();
    }

    public function tahun(): BelongsToMany
    {
        return $this->belongsToMany(Tahun::class, 'kelas_siswas', 'tahun_id', 'kelas_id')
        ->withPivot('tahun_id')
        ->withTimestamps();
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
