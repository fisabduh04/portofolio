<?php

namespace App\Models;

use App\Livewire\kelas;
use App\Models\kelas as ModelsKelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class jurusan extends Model
{
    use HasFactory;
    protected $fillable = ['kode', 'jurusan','deskripsi'];

    public function kelas()
    {
        return $this->hasMany(ModelsKelas::class);
    }
    public function mapel()
    {
        return $this->hasMany(mapel::class);
    }
}
