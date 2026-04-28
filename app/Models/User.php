<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use App\Models\JadwalPiket;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pegawai_id',
        'is_active',
        'foto',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Helper methods untuk pengecekan role yang lebih bersih.
     */
    public function isAdmin(): bool { return $this->role === UserRole::Admin; }
    public function isOperator(): bool { return $this->role === UserRole::Operator; }
    public function isKepala(): bool { return $this->role === UserRole::Kepala; }
    public function isManagement(): bool { return $this->role?->isManagement() ?? false; }
    public function canManagePayroll(): bool { return $this->role?->canManagePayroll() ?? false; }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function isPiketToday()
    {
        // Gunakan logika terpusat dari Enum
        if ($this->isManagement()) {
            return true;
        }

        if (!$this->pegawai_id) {
            return false;
        }

        // Cek JadwalPiket hari ini
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        $tahunAktif = \App\Models\Tahun::aktif()->first();

        if (!$tahunAktif) return false;

        return JadwalPiket::where('pegawai_id', $this->pegawai_id)
            ->where('hari', $hariIni)
            ->where('tahun_id', $tahunAktif->id)
            ->exists();
    }
}
