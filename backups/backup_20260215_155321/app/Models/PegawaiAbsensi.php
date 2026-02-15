<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiAbsensi extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'durasi_kerja',
        'status',
        'nominal_gaji',
        'nominal_makan',
        'total_honor',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
