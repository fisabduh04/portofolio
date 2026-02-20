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

        // [NEW] Check if there is an existing Izin/Sakit/Cuti/DL record
        // If "Izin" or "Sakit" or "Cuti" exists (that usually implies NO Presence), we should NOT overwrite it with "Alpha"
        // BUT if there are Logs (Fingerprint), does it override Izin? 
        // Typically: Presence > Izin (e.g. approved leave but came to work).
        // OR: Izin > Presence (strict). 
        // Let's assume: If Logs exist, we process as Hadir/Telat (cancel Izin effect effectively for that day, or need manual review).
        // If NO Logs, we check Izin. If Izin exists, we return (do nothing, let IzinController logic hold).
        // OR better: We query PegawaiAbsensi for this date. If status is in ['Sakit', 'Izin', 'Cuti', 'Dinas Luar'], we preserve it UNLESS logs exist?
        
        $existingAbsensi = PegawaiAbsensi::where('pegawai_id', $pegawai->id)
            ->where('tanggal', $date)
            ->first();

        if ($logs->isEmpty()) {
            // If no logs, check if we already have a specialized status (Izin/Sakit/etc)
            if ($existingAbsensi && in_array($existingAbsensi->status, ['Sakit', 'Izin', 'Cuti', 'Dinas Luar'])) {
                return $existingAbsensi; // Keep existing permit status
            }
            // Otherwise check for Alpha logic
            return $this->markAsAlpha($pegawai, $date);
        }

        // If Logs EXIST, we generally overwrite "Izin" because they grew legs and came to office.
        // However, if status is 'Dinas Luar', maybe they scanned at location? (Not relevant for simpler fingerprint).
        // Let's proceed to calculate attendance from logs.

        // 2. Get Rule
        // 2. Get Rule for the specific year of $date
        $carbonDate = Carbon::parse($date);
        $tahun = \App\Models\Tahun::where('tanggalmulai', '<=', $carbonDate->toDateString())
            ->where('tanggalakhir', '>=', $carbonDate->toDateString())
            ->first() ?? \App\Models\Tahun::where('isActive', 1)->first();

        $allocation = $pegawai->ruleAllocations()
            ->where('tahun_id', $tahun?->id)
            ->with('attendanceRule')
            ->first();

        $rule = $allocation?->attendanceRule;
        $dayName = $carbonDate->format('D'); // Mon, Tue, etc.

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

        // Logs NOT empty here

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
        // 1. Determine Day Name (Senin, Selasa...)
        $carbonDate = Carbon::parse($date);
        $dayName = $carbonDate->isoFormat('dddd'); // Requires Carbon locale set to ID, or use map.
        // Safer: English to Indo map if Locale not guaranteed
        $days = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'
        ];
        $dayIndo = $days[$carbonDate->format('D')] ?? '';

        // 2. Check Wajib Hadir Table
        // Need to know current Academic Year for that date
        $tahun = \App\Models\Tahun::where('tanggalmulai', '<=', $carbonDate->toDateString())
            ->where('tanggalakhir', '>=', $carbonDate->toDateString())
            ->first() ?? \App\Models\Tahun::where('isActive', 1)->first();

        if (!$tahun) {
             // Fallback: If no year context, maybe don't mark alpha? 
             // Or assumes default behavior. Let's assume default is NOT Alpha to be safe.
             return null;
        }

        $isWajibHadir = \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawai->id)
            ->where('tahun_id', $tahun->id)
            ->where('hari', $dayIndo)
            ->exists();

        if (!$isWajibHadir) {
            // Not required to be present -> Not Alpha.
            // Maybe clear any existing Alpha?
            // For now just return.
            return null;
        }

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
