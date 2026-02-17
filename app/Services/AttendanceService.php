<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\PegawaiAbsensi;
use App\Models\AttendanceRule;
use App\Models\SpecialEvent;
use App\Models\PegawaiScheduleOverride;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
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

        // 2. Determine Schedule (Waterfall Priority)
        $schedule = $this->getDailySchedule($pegawai, $date);

        // If no valid schedule found (e.g. Sunday with no picket/class or Teacher with no class), 
        // and no logs, we must CLEAN UP any existing "Alpha" record that might have been created previously.
        if (!$schedule['is_working_day'] && $logs->isEmpty()) {
            PegawaiAbsensi::where('pegawai_id', $pegawai->id)
                ->whereDate('tanggal', $date)
                ->delete();
            return;
        }

        if ($logs->isEmpty()) {
            return $this->markAsAlpha($pegawai, $date, $schedule);
        }

        // 3. Determine In/Out (First-In, Last-Out)
        $firstLog = $logs->first();
        $lastLog = $logs->last();

        $jamMasuk = Carbon::parse($firstLog->scan_time);
        $jamPulang = Carbon::parse($lastLog->scan_time);
        
        // 4. Calculate Status
        $status = $schedule['status_label'] ?? 'Hadir'; // Default 'Hadir' or 'Hadir (Event)'
        
        // Rule Time
        $expectedIn = $schedule['jam_masuk'] ? Carbon::parse($date . ' ' . $schedule['jam_masuk']) : null;
        
        if ($expectedIn) {
            $toleransi = $schedule['toleransi_telat'] ?? 0;
            $lateThreshold = $expectedIn->copy()->addMinutes($toleransi);

            if ($jamMasuk->gt($lateThreshold)) {
                $status = 'Telat';
            }
        }

        // 5. Calculate Duration
        $duration = $jamMasuk->diff($jamPulang);
        $formattedDuration = $duration->format('%H:%I:%S');

        // 6. Calculate Financials
        $honorHarian = $schedule['gaji_harian'] ?? 0;
        $uangMakan = $schedule['bantuan_makan'] ?? 0;
        $potongan = 0;

        // Apply Penalty if Telat
        if ($status === 'Telat') {
            $potongan = $schedule['denda_telat'] ?? 0;
        }

        // Special Case: "Diluar Jadwal" (No Schedule but Presensi exists)
        if (!$schedule['is_working_day']) {
             $status = 'Diluar Jadwal';
             $honorHarian = 0;
             $uangMakan = 0;
             $potongan = 0;
        }

        // Ensure total honor is not negative
        $totalHonor = ($honorHarian + $uangMakan) - $potongan;
        if ($totalHonor < 0) $totalHonor = 0;

        // 7. Determine Source & Creator
        $source = 'Fingerprint';
        $createdBy = null;

        foreach ($logs as $log) {
            if ($log->machine_id === 'MANUAL') {
                $source = 'Manual';
                // If we tracked who created the log in AttendanceLog, we could pull it here.
                // For now, if it's manual, we flag it.
            }
        }

        // If Event
        if ($schedule['status_label'] === 'Hadir (Event)') {
            $source = 'Event';
        }

        // 8. Store Result
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
                'attendance_source' => $source,
                'created_by' => auth()->id() ?? null, // Capture if re-processed by admin
            ]
        );
    }

    private function markAsAlpha($pegawai, $date, $schedule)
    {
        // Only mark Alpha if it WAS a working day
        if (!$schedule['is_working_day']) {
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
                'attendance_source' => 'System',
            ]
        );
    }

    /**
     * Determine the schedule/rule for a specific day.
     * Waterfall Logic: Event > Override > Piket/Jadwal > Default Rule
     */
    public function getDailySchedule($pegawai, $date)
    {
        $dayName = Carbon::parse($date)->isoFormat('dddd'); // Senin, Selasa, etc.
        
        // 1. Special Event
        $event = SpecialEvent::where('date', $date)
            ->whereHas('participants', function($q) use ($pegawai) {
                $q->where('pegawai_id', $pegawai->id);
            })
            ->first();

        if ($event) {
            return [
                'is_working_day' => true,
                'jam_masuk' => $event->start_time,
                'jam_pulang' => $event->end_time,
                'toleransi_telat' => 0,
                'gaji_harian' => $event->bantuan_hadir, // Use event allowance as base pay
                'bantuan_makan' => 0, // Usually included in event pay or separate? Assuming 0 for now
                'denda_telat' => 0,
                'status_label' => 'Hadir (Event)',
            ];
        }

        // 2. Schedule Override
        $override = PegawaiScheduleOverride::with('attendanceRule')
            ->where('pegawai_id', $pegawai->id)
            ->where('date', $date)
            ->first();

        if ($override) {
            $rule = $override->attendanceRule;
            return [
                'is_working_day' => true,
                'jam_masuk' => $rule->jam_masuk,
                'jam_pulang' => $rule->jam_pulang,
                'toleransi_telat' => $rule->toleransi_telat,
                'gaji_harian' => $rule->gaji_harian,
                'bantuan_makan' => $rule->bantuan_makan,
                'denda_telat' => $rule->denda_telat ?? 0,
                'status_label' => 'Hadir',
            ];
        }

        // 3. Auto-Detect: Jadwal Piket
        // Assuming 'hari' in JadwalPiket matches 'Senin', 'Selasa', etc.
        // We enforce Case Insensitive check just in case, or Standardize. 
        // Laravel Enum usually handles it, but let's be safe.
        $piket = JadwalPiket::where('pegawai_id', $pegawai->id)
            ->where('hari', $dayName)
            ->first();

        if ($piket) {
            // Use Employee's Default Rule values OR a specific 'Piket' rule if exists.
            // For now, we fall back to Default Rule's financial values but enforce presence.
            $rule = $pegawai->attendanceRule;
            return [
                'is_working_day' => true,
                'jam_masuk' => $rule ? $rule->jam_masuk : '07:00:00',
                'jam_pulang' => $rule ? $rule->jam_pulang : '14:00:00',
                'toleransi_telat' => $rule ? $rule->toleransi_telat : 15,
                'gaji_harian' => $rule ? $rule->gaji_harian : 0,
                'bantuan_makan' => $rule ? $rule->bantuan_makan : 0,
                'denda_telat' => $rule ? ($rule->denda_telat ?? 0) : 0,
                'status_label' => 'Piket',
            ];
        }

        // 4. Auto-Detect: Jadwal Mengajar
        $mengajar = Jadwal::where('pegawai_id', $pegawai->id)
            ->where('hari', $dayName)
            ->exists();

        if ($mengajar) {
             $rule = $pegawai->attendanceRule;
             return [
                'is_working_day' => true,
                'jam_masuk' => $rule ? $rule->jam_masuk : '07:00:00', // Could be refined to First Class Time
                'jam_pulang' => $rule ? $rule->jam_pulang : '14:00:00',
                'toleransi_telat' => $rule ? $rule->toleransi_telat : 15,
                'gaji_harian' => $rule ? $rule->gaji_harian : 0,
                'bantuan_makan' => $rule ? $rule->bantuan_makan : 0,
                'denda_telat' => $rule ? ($rule->denda_telat ?? 0) : 0,
                'status_label' => 'Mengajar',
            ];
        }

        // 5. Default Rule
        $rule = $pegawai->attendanceRule;
        
        // If no rule attached, assume not a working day
        if (!$rule) {
            return [
                'is_working_day' => false,
                'jam_masuk' => null,
                'jam_pulang' => null,
            ];
        }

        // CUSTOM LOGIC: Teacher Flexibility
        // If the rule type is "Teacher", they ONLY work if they had an Event, Override, Piket, or Jadwal (checked above).
        // Since we are here (Step 5), it means none of those were found.
        // So, for a Teacher, this is NOT a working day (Libur/Off), preventing "Alpha".
        if ($rule->rule_type === 'Teacher') {
             return [
                'is_working_day' => false,
                'jam_masuk' => null,
                'jam_pulang' => null,
            ];
        }

        // Standard Logic for Staff/Regular Employees
        $isWorkingDay = false;
        
        if (!empty($rule->hari_kerja)) {
            $hariKerja = is_string($rule->hari_kerja) ? json_decode($rule->hari_kerja, true) : $rule->hari_kerja;
            
            if (is_array($hariKerja) && in_array($dayName, $hariKerja)) {
                $isWorkingDay = true;
            }
        }

        if ($isWorkingDay) {
            return [
                'is_working_day' => true,
                'jam_masuk' => $rule->jam_masuk,
                'jam_pulang' => $rule->jam_pulang,
                'toleransi_telat' => $rule->toleransi_telat,
                'gaji_harian' => $rule->gaji_harian,
                'bantuan_makan' => $rule->bantuan_makan,
                'denda_telat' => $rule->denda_telat ?? 0,
                'status_label' => 'Hadir',
            ];
        }

        // 6. No Schedule
        return [
            'is_working_day' => false,
            'jam_masuk' => null,
            'jam_pulang' => null,
        ];
    }
}
