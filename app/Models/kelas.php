<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class kelas extends Model
{
    use HasFactory;
    protected $fillable = ['id','kelas', 'jurusan_id','ket'];

    public function jurusan()
    {
        return $this->belongsTo(jurusan::class, 'jurusan_id');
    }

    public function KelasSiswa(){
        return $this->hasMany(KelasSiswa::class);
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(siswa::class, 'kelas_siswas', 'siswa_id', 'tahun_id')
        ->withPivot('siswa_id')
        ->withTimestamps();
    }

    public function tahun(): BelongsToMany
    {
        return $this->belongsToMany(tahun::class, 'kelas_siswas', 'tahun_id', 'siswa_id')
        ->withPivot('tahun_id')
        ->withTimestamps();
    }
}
