<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class mapel extends Model
{
    use HasFactory;

    protected $fillable = ['kode','jurusan_id','mapel', 'ket'];

    public function jurusan()
    {
        return $this->belongsTo(jurusan::class, 'jurusan_id');
    }

}
