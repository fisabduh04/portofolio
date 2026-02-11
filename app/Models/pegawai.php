<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status', 'aktif', 'email', 'nuptk', 'jk', 'kotalahir', 'tanggallahir', 'jenisptk', 
        'agama', 'alamat', 'rt', 'rw', 'hp', 'skpengangkatan', 'lembagapengangkatan', 'PangkatGolongan', 
        'sumbergaji', 'ibukandung', 'kawin', 'suamiistri', 'pekerjaansuamiIstri', 'npwp', 'nonik', 
        'nokk', 'foto', 'deskripsi', 'password'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
