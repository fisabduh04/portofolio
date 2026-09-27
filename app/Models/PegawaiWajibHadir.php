<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiWajibHadir extends Model
{
    use HasFactory;

    public const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    protected $fillable = ['pegawai_id', 'tahun_id', 'hari'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }
}
