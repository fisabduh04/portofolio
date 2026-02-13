<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;
    protected $fillable = ['kategori', 'jadwal_id', 'kelas_id', 'tanggal', 'materi', 'catatan', 'foto', 'pegawai_id'];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}

