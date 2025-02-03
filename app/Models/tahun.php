<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\kelas;
use App\Models\siswa;


class tahun extends Model
{
    use HasFactory;
    protected $fillable = ['tanggalmulai', 'tanggalakhir', 'tahun', 'semester', 'isActive'];


    public function KelasSiswa(){
        return $this->hasMany(KelasSiswa::class);
    }
    /**
     * The roles that belong to the tahun
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(kelas::class, 'kelas_siswas', 'kelas_id', 'siswa_id')
        ->withPivot('kelas_id')
        ->withTimestamps();
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(kelas::class, 'kelas_siswas', 'siswa_id','kelas_id')
        ->withPivot('siswa_id')
        ->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('isActive', 1);
    }

}
