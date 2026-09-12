<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tahun extends Model
{
    use HasFactory;

    public function waliKelas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    protected $fillable = ['tanggalmulai', 'tanggalakhir', 'tahun', 'semester', 'isActive'];

    public function KelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class);
    }

    /**
     * The roles that belong to the tahun
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswas', 'kelas_id', 'siswa_id')
            ->withPivot('kelas_id')
            ->withTimestamps();
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswas', 'siswa_id', 'kelas_id')
            ->withPivot('siswa_id')
            ->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('isActive', 1);
    }
}
