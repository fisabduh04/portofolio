<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    public function waliKelas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    public function kelasWali(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'wali_kelas', 'pegawai_id', 'kelas_id')
            ->withPivot(['id', 'tahun_id', 'is_active', 'keterangan'])->withTimestamps();
    }

    protected $fillable = [
        'name', 'status', 'aktif', 'email', 'nuptk', 'jk', 'kotalahir', 'tanggallahir', 'jenisptk',
        'agama', 'alamat', 'rt', 'rw', 'hp', 'skpengangkatan', 'lembagapengangkatan', 'PangkatGolongan',
        'sumbergaji', 'ibukandung', 'kawin', 'suamiistri', 'pekerjaansuamiIstri', 'npwp', 'nonik',
        'nokk', 'foto', 'deskripsi',
    ];

    protected $appends = ['fingerprint_id'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function ruleAllocations()
    {
        return $this->hasMany(PegawaiRuleAllocation::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function pegawaiAbsensis()
    {
        return $this->hasMany(PegawaiAbsensi::class);
    }

    public function fingerprintEnrollments()
    {
        return $this->hasMany(FingerprintEnrollment::class);
    }

    public function wajibHadirs()
    {
        return $this->hasMany(PegawaiWajibHadir::class);
    }

    // Accessor for backward compatibility
    public function getFingerprintIdAttribute()
    {
        // Return the first enrollment ID found, or null
        return $this->fingerprintEnrollments->first()?->fingerprint_user_id;
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
