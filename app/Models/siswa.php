<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Siswa extends Model
{
    use HasFactory;
    protected $guarded=[];


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
