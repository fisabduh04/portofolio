<?php

namespace App\Models;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Jurusan extends Model
{
    use HasFactory;
    protected $fillable = ['kode', 'jurusan','deskripsi'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
    public function mapel()
    {
        return $this->hasMany(Mapel::class);
    }

    /**
     * Accessor: Warna badge berdasarkan ID jurusan.
     * Tambahkan ID baru di sini kalau jurusan bertambah.
     */
    public function getBadgeColorAttribute(): string
    {
        // Mapping ID jurusan ke warna badge
        $colors = [
            1 => 'blue',
            2 => 'green',
            3 => 'yellow',
            4 => 'purple',
            5 => 'red',
            6 => 'indigo',
        ];

        return $colors[$this->id] ?? 'gray';
    }
}
