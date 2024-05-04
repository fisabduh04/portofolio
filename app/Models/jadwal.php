<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal extends Model
{
    use HasFactory;

    protected $fillable=[
        'tahun_id',
        'kelas_id',
        'pegawai_id',
        'mapel_id',
        'hari',
        'jam',
        'mulai',
        'akhir',
        'status',
        'ket',
    ];

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
}
