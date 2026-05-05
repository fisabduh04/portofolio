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

    /**
     * Mendapatkan class CSS Tailwind untuk warna badge berdasarkan role.
     */
    public function color(): string
    {
        return match ($this) {
            self::Kepala   => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            self::Admin    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::Operator => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::Guru     => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::Siswa    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            self::Staff    => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
            self::Bendahara => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
            default        => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
