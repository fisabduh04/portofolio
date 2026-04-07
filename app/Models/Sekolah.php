<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'nss',
        'status_sekolah',
        'bentuk_pendidikan',
        'kepala_sekolah',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'email',
        'website',
        'no_telp',
        'no_fax',
        'lintang',
        'bujur',
        'sk_pendirian',
        'tgl_sk_pendirian',
        'sk_izin_operasional',
        'tgl_sk_izin_operasional',
        'logo',
        'kop_surat',
    ];

    // Cast dates
    protected $casts = [
        'tgl_sk_pendirian' => 'date',
        'tgl_sk_izin_operasional' => 'date',
    ];

    /**
     * Helper untuk mengambil URL Logo dengan mudah
     * Cara pakai: $sekolah->logo_url
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return asset('img/logo.png'); // Gambar default jika tidak ada logo
        }

        // Cek jika path sudah berupa URL (http) atau path lokal
        return \Illuminate\Support\Str::startsWith($this->logo, ['http://', 'https://']) 
            ? $this->logo 
            : \Illuminate\Support\Facades\Storage::url($this->logo);
    }

    /**
     * Helper untuk mengambil URL Kop Surat
     * Cara pakai: $sekolah->kop_surat_url
     */
    public function getKopSuratUrlAttribute()
    {
         if (empty($this->kop_surat)) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($this->kop_surat, ['http://', 'https://']) 
            ? $this->kop_surat 
            : \Illuminate\Support\Facades\Storage::url($this->kop_surat);
    }
}
