<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin    = 'admin';
    case Operator = 'operator';
    case Guru     = 'guru';
    case Siswa    = 'siswa';
    case Kepala   = 'kepala';
    case Staff    = 'staff';
    case Bendahara = 'bendahara';

    /**
     * Mendapatkan label yang rapi untuk tampilan (UI).
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin    => 'Administrator',
            self::Operator => 'Operator Sistem',
            self::Guru     => 'Guru Pengajar',
            self::Siswa    => 'Siswa',
            self::Kepala   => 'Kepala Sekolah',
            self::Staff    => 'Staff TU',
            self::Bendahara => 'Bendahara Sekolah',
        };
    }

    /**
     * Mendapatkan tingkatan (rank) role.
     * Semakin tinggi angka, semakin tinggi kekuasaannya.
     */
    public function rank(): int
    {
        return match ($this) {
            self::Kepala   => 4,
            self::Admin    => 3,
            self::Operator, self::Bendahara => 2,
            self::Guru, self::Staff, self::Siswa => 1,
            default => 0,
        };
    }

    /**
     * Mengecek apakah role ini termasuk jajaran manajemen/admin.
     */
    public function isManagement(): bool
    {
        return in_array($this, [self::Admin, self::Operator, self::Kepala, self::Bendahara]);
    }

    /**
     * Mengecek apakah role ini boleh MENGELOLA Payroll/Gaji (Melihat semua orang).
     */
    public function canManagePayroll(): bool
    {
        return in_array($this, [self::Admin, self::Operator, self::Bendahara]);
    }
}
