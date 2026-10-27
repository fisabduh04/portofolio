<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
            'role' => UserRole::class,
        ];
    }

    /**
     * Helper methods untuk pengecekan role yang lebih bersih.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isOperator(): bool
    {
        return $this->role === UserRole::Operator;
    }

    public function isKepala(): bool
    {
        return $this->role === UserRole::Kepala;
    }

    public function isManagement(): bool
    {
        return $this->role?->isManagement() ?? false;
    }

    public function canManagePayroll(): bool
    {
        return (bool) $this->is_active && ($this->role?->canManagePayroll() ?? false);
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => $this->pegawai_id !== null
                ? $this->pegawai?->email
                : $value,
        );
    }

    public function scopeForLoginEmail(Builder $query, string $email): Builder
    {
        $email = strtolower(trim($email));

        return $query->where(fn (Builder $accounts): Builder => $accounts
            ->whereHas('pegawai', fn (Builder $employees): Builder => $employees->where('email', $email))
            ->orWhere(fn (Builder $unlinked): Builder => $unlinked->whereNull('pegawai_id')->where('email', $email)));
    }

    public function isPiketToday(): bool
    {
        return $this->isPiketOn(now()->toDateString());
    }

    public function hasPiketSchedule(): bool
    {
        if (! $this->is_active || $this->role->isPayrollOnly()) {
            return false;
        }

        if ($this->isManagement()) {
            return true;
        }

        if (! $this->pegawai_id) {
            return false;
        }

        return JadwalPiket::where('pegawai_id', $this->pegawai_id)
            ->whereHas('tahun', fn ($query) => $query->aktif())
            ->exists();
    }

    public function isPiketOn(string $date): bool
    {
        if (! $this->is_active || $this->role->isPayrollOnly()) {
            return false;
        }

        // Gunakan logika terpusat dari Enum
        if ($this->isManagement()) {
            return true;
        }

        if (! $this->pegawai_id) {
            return false;
        }

        $hari = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd');

        return JadwalPiket::where('pegawai_id', $this->pegawai_id)
            ->where('hari', $hari)
            ->whereHas('tahun', fn ($query) => $query->aktif())
            ->exists();
    }
}
