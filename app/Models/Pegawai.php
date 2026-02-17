<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status', 'aktif', 'email', 'nuptk', 'jk', 'kotalahir', 'tanggallahir', 'jenisptk', 
        'agama', 'alamat', 'rt', 'rw', 'hp', 'skpengangkatan', 'lembagapengangkatan', 'PangkatGolongan', 
        'sumbergaji', 'ibukandung', 'kawin', 'suamiistri', 'pekerjaansuamiIstri', 'npwp', 'nonik', 
        'nokk', 'foto', 'deskripsi', 'fingerprint_id', 'attendance_rule_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function attendanceRule()
    {
        return $this->belongsTo(AttendanceRule::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function pegawaiAbsensis()
    {
        return $this->hasMany(PegawaiAbsensi::class);
    }

    public function specialEvents()
    {
        return $this->belongsToMany(SpecialEvent::class, 'special_event_participants');
    }

    public function scheduleOverrides()
    {
        return $this->hasMany(PegawaiScheduleOverride::class);
    }
}
