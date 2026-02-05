<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function isPiketToday()
    {
        if (in_array($this->role, ['admin', 'operator', 'kepala'])) {
            return true;
        }

        if (!$this->pegawai_id) {
            return false;
        }

        // Cek JadwalPiket hari ini
        $hariIni = \Carbon\Carbon::now()->locale('id')->isoFormat('dddd');
        $tahunAktif = \App\Models\Tahun::aktif()->first();

        if (!$tahunAktif) return false;

        return \App\Models\JadwalPiket::where('pegawai_id', $this->pegawai_id)
            ->where('hari', $hariIni)
            ->where('tahun_id', $tahunAktif->id)
            ->exists();
    }
}
