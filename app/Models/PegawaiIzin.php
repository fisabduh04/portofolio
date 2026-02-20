<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiIzin extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_akhir',
        'keterangan',
        'bukti_dokumen',
        'status_approval',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
