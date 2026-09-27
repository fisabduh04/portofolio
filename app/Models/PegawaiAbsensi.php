<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiAbsensi extends Model
{
    use HasFactory;

    public const MANUAL_STATUSES = ['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpha' => 'A', 'Pulang' => 'P', 'Telat' => 'T'];

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'durasi_kerja',
        'status',
        'nominal_gaji',
        'nominal_makan',
        'total_honor',
        'attendance_source',
        'created_by',
        'updated_by',
        'is_manual',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_manual' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function presensiStatus(): ?string
    {
        if (array_key_exists($this->status ?? '', self::MANUAL_STATUSES)) {
            return $this->status;
        }

        return match ($this->status) {
            'Hadir (Event)', 'Piket', 'Mengajar', 'Diluar Jadwal' => 'Hadir',
            'Cuti', 'Dinas Luar' => 'Izin',
            default => null,
        };
    }
}
