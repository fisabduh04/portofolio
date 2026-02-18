<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    protected $fillable = [
        'name',
        'jam_masuk',
        'jam_pulang',
        'scan_masuk_start',
        'scan_pulang_end',
        'toleransi_telat',
        'bantuan_makan',
        'gaji_harian',
        'gaji_per_jam',
        'hari_kerja',
        'denda_telat',
    ];

    protected $casts = [
        'hari_kerja' => 'array',
    ];

    public function ruleAllocations()
    {
        return $this->hasMany(PegawaiRuleAllocation::class);
    }

    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_rule_allocations', 'attendance_rule_id', 'pegawai_id')->withTimestamps();
    }
}
