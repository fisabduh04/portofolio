<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\PegawaiAbsensi;
use App\Models\AttendanceRule;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate and store daily attendance for a specific employee and date.
     */
    public function calculateDailyAttendance($pegawai, $date)
    {
        // 1. Get Logs for the date
        $logs = AttendanceLog::where('pegawai_id', $pegawai->id)
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time')
            ->get();

        // 2. Get Rule
        $rule = $pegawai->attendanceRule;
        $dayName = Carbon::parse($date)->format('D'); // Mon, Tue, etc.

        // Check if today is a working day (if rule has hari_kerja)
        $isWorkingDay = true;
        if ($rule && !empty($rule->hari_kerja) && is_array($rule->hari_kerja)) {
            if (!in_array($dayName, $rule->hari_kerja)) {
                $isWorkingDay = false;
            }
        }

        if (!$isWorkingDay) {
             // If not a working day, check if they showed up anyway?
             // If logs exist, we treat as "Lembur" or just simple presence? 
             // For simplicity, if they scan on holiday, we count as Hadir but maybe mark status "Lembur/Hadir Libur"
             // If no logs, then it's NOT Alpha.
             if ($logs->isEmpty()) {
                 return; // Do nothing, not absent, not present.
             }
        }

        if ($logs->isEmpty()) {
            return $this->markAsAlpha($pegawai, $date);
        }

        if (!$rule) {
            // If no rule, we cannot calculate salary/status accurately.
            // For now, we save as 'Hadir' with 0 salary or handle as error.
            // Let's save as 'Unregistered Rule'
             return PegawaiAbsensi::updateOrCreate(
                ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
                [
                    'jam_masuk' => $logs->first()->scan_time->format('H:i:s'),
                    'jam_pulang' => $logs->last()->scan_time->format('H:i:s'),
                    'status' => 'Tanpa Aturan',
                    'nominal_gaji' => 0,
                    'nominal_makan' => 0,
                    'total_honor' => 0,
                ]
            );
        }

        // 3. Determine In/Out (First-In, Last-Out)
        // Filter logs strictly within scan window if needed, but user said "taken from last finger"
        // typically implies within the day. We rely on the date filtering above.
        
        $firstLog = $logs->first();
        $lastLog = $logs->last();

        $jamMasuk = Carbon::parse($firstLog->scan_time);
        $jamPulang = Carbon::parse($lastLog->scan_time);
        
        // 4. Calculate Status
        $status = 'Hadir';
        
        // Rule Time
        $ruleMasuk = Carbon::parse($date . ' ' . $rule->jam_masuk);
        $lateThreshold = $ruleMasuk->copy()->addMinutes($rule->toleransi_telat);

        if ($jamMasuk->gt($lateThreshold)) {
            $status = 'Telat';
        }

        // 5. Calculate Duration
        // If there is only 1 log, Jam Masuk = Jam Pulang, Duration = 0
        $duration = $jamMasuk->diff($jamPulang);
        $formattedDuration = $duration->format('%H:%I:%S');

        // 6. Calculate Financials
        $honorHarian = $rule->gaji_harian;
        $uangMakan = $rule->bantuan_makan;
        $potongan = 0;

        // Apply Penalty
        if ($status === 'Telat') {
            $potongan = $rule->denda_telat ?? 0;
        }

        // Ensure total honor is not negative
        $totalHonor = ($honorHarian + $uangMakan) - $potongan;
        if ($totalHonor < 0) $totalHonor = 0;

        // 7. Store Result
        return PegawaiAbsensi::updateOrCreate(
            [
                'pegawai_id' => $pegawai->id,
                'tanggal' => $date,
            ],
            [
                'jam_masuk' => $jamMasuk->toTimeString(),
                'jam_pulang' => $jamPulang->toTimeString(),
                'durasi_kerja' => $formattedDuration,
                'status' => $status,
                'nominal_gaji' => $honorHarian,
                'nominal_makan' => $uangMakan,
                'total_honor' => $totalHonor,
            ]
        );
    }

    private function markAsAlpha($pegawai, $date)
    {
        return PegawaiAbsensi::updateOrCreate(
            [
                'pegawai_id' => $pegawai->id,
                'tanggal' => $date,
            ],
            [
                'jam_masuk' => null,
                'jam_pulang' => null,
                'durasi_kerja' => null,
                'status' => 'Alpha',
                'nominal_gaji' => 0,
                'nominal_makan' => 0,
                'total_honor' => 0,
            ]
        );
    }
}
