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
}
