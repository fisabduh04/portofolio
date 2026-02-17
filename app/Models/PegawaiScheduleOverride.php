<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiScheduleOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'attendance_rule_id',
        'date',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function attendanceRule()
    {
        return $this->belongsTo(AttendanceRule::class);
    }
}
