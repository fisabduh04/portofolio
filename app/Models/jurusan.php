<?php

namespace App\Models;

use App\Livewire\Kelas;
use App\Models\kelas as ModelsKelas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jurusan extends Model
{
    use HasFactory;
    protected $fillable = ['kode', 'jurusan'];

    public function kelas()
    {
        return $this->hasMany(kelas::class);
    }
    public function mapel()
    {
        return $this->hasMany(mapel::class);
    }
}
