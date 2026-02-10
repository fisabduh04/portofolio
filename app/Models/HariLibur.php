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
    public static function isLibur($date, $tahunId = null)
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
            // If the holiday record has specific valid dates, the current date MUST be within them.
            // This applies to both "Weekly" (e.g. only Sunday in Jan) and "Special" holidays.
            if ($libur->tanggal_mulai) {
                $start = $libur->tanggal_mulai;
                $end = $libur->tanggal_akhir ?? $start; // If end is null, assume single day

                if (!$date->between($start, $end)) {
                    continue; // Skip this rule if we are outside its valid range
                }
            }

            // 2. CHECK RULE TYPE
            if ($libur->hari) {
                // Weekly Holiday Rule (e.g., "Every Minggu")
                if (strtolower($libur->hari) === strtolower($dayName)) {
                    return true;
                }
            } else {
                // Special Holiday Rule (Date-based only)
                // If we passed Step 1 (Date Range Check) and 'hari' is null, it means we matched the special date.
                // (Note: For special holidays without 'hari', logic relies entirely on Step 1)
                if ($libur->tanggal_mulai) {
                     return true;
                }
            }
        }

        return false;
    }
}
