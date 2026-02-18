<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\kelas;
use App\Models\siswa;


class Tahun extends Model
{
    use HasFactory;
    protected $fillable = ['tanggalmulai', 'tanggalakhir', 'tahun', 'semester', 'isActive'];


    public function KelasSiswa(){
        return $this->hasMany(KelasSiswa::class);
    }

    public function mandatorySchedules()
    {
        return $this->hasMany(PegawaiWajibHadir::class);
    }
    /**
     * The roles that belong to the tahun
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswas', 'kelas_id', 'siswa_id')
        ->withPivot('kelas_id')
        ->withTimestamps();
    }

    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswas', 'siswa_id','kelas_id')
        ->withPivot('siswa_id')
        ->withTimestamps();
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('tanggalmulai', '<=', $date)
                     ->where('tanggalakhir', '>=', $date);
    }

    public function scopeAktif($query)
    {
        return $query->where('isActive', 1);
    }

}
