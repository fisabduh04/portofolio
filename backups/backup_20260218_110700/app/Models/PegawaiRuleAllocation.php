<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiRuleAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'tahun_id',
        'attendance_rule_id',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function attendanceRule()
    {
        return $this->belongsTo(AttendanceRule::class);
    }
}
