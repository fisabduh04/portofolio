<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HariLibur extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'hari',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $tahunId)
    {
        return $query->where(function ($q) use ($tahunId) {
            $q->where('tahun_id', $tahunId)
              ->orWhereNull('tahun_id');
        });
    }

    /**
     * Check if a given date is a holiday.
     * 
     * @param string|Carbon $date
     * @return bool
     */
    /**
     * Get holiday details for a given date.
     * 
     * @param string|Carbon $date
     * @param int|null $tahunId
     * @return HariLibur|null
     */
    public static function getHoliday($date, $tahunId = null)
    {
        $date = Carbon::parse($date);
        $dayName = $date->locale('id')->isoFormat('dddd');

        // Check active holidays
        $holidays = self::active();
        
        if ($tahunId) {
            $holidays->forYear($tahunId);
        }
        
        $holidays = $holidays->get();

        foreach ($holidays as $libur) {
            // 1. GLOBAL DATE RANGE CHECK
            if ($libur->tanggal_mulai) {
                $start = $libur->tanggal_mulai;
                $end = $libur->tanggal_akhir ?? $start;

                if (!$date->between($start, $end)) {
                    continue; 
                }
            }

            // 2. CHECK RULE TYPE
            if ($libur->hari) {
                if (strtolower($libur->hari) === strtolower($dayName)) {
                    return $libur;
                }
            } else {
                if ($libur->tanggal_mulai) {
                     return $libur;
                }
            }
        }

        // 3. WEEKEND CHECK (Default Sunday) - Optional, but good to have explicit
        if ($dayName === 'Minggu') {
             // Create dummy holiday object for Sunday if no DB rule overrides/defines it
             // But usually user defines Minggu as holiday in DB. 
             // Let's stick to DB rules for now to be safe.
        }

        return null;
    }

    public static function isLibur($date, $tahunId = null)
    {
        return self::getHoliday($date, $tahunId) !== null;
    }
}
