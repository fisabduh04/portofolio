<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class siswa extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function KelasSiswa(){
        return $this->hasMany(KelasSiswa::class);
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(kelas::class, 'kelas_siswas', 'kelas_id', 'tahun_id')
        ->withPivot('kelas_id')
        ->withTimestamps();
    }

    public function tahun(): BelongsToMany
    {
        return $this->belongsToMany(tahun::class, 'kelas_siswas', 'tahun_id', 'kelas_id')
        ->withPivot('tahun_id')
        ->withTimestamps();
    }
}
