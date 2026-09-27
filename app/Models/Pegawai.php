<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use HasFactory;

    public function scopeGuru(Builder $query): Builder
    {
        return $query->where(fn (Builder $teacher): Builder => $teacher
            ->whereRaw('LOWER(TRIM(jenisptk)) = ?', ['guru'])
            ->orWhere(fn (Builder $unclassified): Builder => $unclassified
                ->where(fn (Builder $type): Builder => $type->whereNull('jenisptk')->orWhereRaw("TRIM(jenisptk) = ''"))
                ->whereHas('user', fn (Builder $user): Builder => $user->where('role', 'guru'))));
    }

    public function scopeGuruAktif(Builder $query): Builder
    {
        return $query->guru()->whereRaw('LOWER(TRIM(aktif)) = ?', ['aktif']);
    }

    public function scopeWajibHadirPada(Builder $query, string $date): Builder
    {
        $tahunId = Tahun::aktif()->value('id');
        if ($tahunId === null) {
            return $query->whereRaw('1 = 0');
        }

        $hari = Carbon::parse($date)->locale('id')->isoFormat('dddd');
        $schedule = fn (Builder $scheduleQuery): Builder => $scheduleQuery->where('tahun_id', $tahunId)->where('hari', $hari);

        return $query->where(fn (Builder $pegawaiQuery): Builder => $pegawaiQuery
            ->whereHas('wajibHadirs', $schedule)
            ->orWhereHas('jadwals', $schedule)
            ->orWhereHas('jadwalPikets', $schedule));
    }

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

    public function jadwalPikets(): HasMany
    {
        return $this->hasMany(JadwalPiket::class);
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
