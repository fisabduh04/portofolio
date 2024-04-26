<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelas extends Model
{
    use HasFactory;
    protected $fillable = ['kelas', 'jurusan_id'];

    public function jurusan()
    {
        return $this->belongsTo(jurusan::class, 'jurusan_id');
    }
}
