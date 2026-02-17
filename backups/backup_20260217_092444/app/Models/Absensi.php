<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $fillable = ['logbook_id', 'siswa_id', 'status', 'keterangan'];

    public function logbook()
    {
        return $this->belongsTo(Logbook::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
