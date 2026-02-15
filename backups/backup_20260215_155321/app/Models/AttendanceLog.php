<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'pegawai_id',
        'scan_time',
        'machine_id',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
