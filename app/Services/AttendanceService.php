<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\PegawaiAbsensi;
use App\Models\AttendanceRule;
use App\Models\SpecialEvent;
use App\Models\PegawaiScheduleOverride;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\PegawaiIzin;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * AttendanceService - Pusat Logika Absensi Pegawai
 * Kelas ini bertanggung jawab menghitung status kehadiran, durasi kerja,
 * dan nominal honor harian berdasarkan berbagai sumber data.
 */
class AttendanceService
{
    /**
     * Hitung dan simpan absensi harian untuk satu pegawai pada tanggal tertentu.
     * 
     * @param \App\Models\Pegawai $pegawai
     * @param string $date (format: Y-m-d)
     * @return \App\Models\PegawaiAbsensi|null
     */
    public function calculateDailyAttendance($pegawai, $date)
    {
        Log::info("--- Menghitung Absensi: {$pegawai->name} pada {$date} ---");

        // 1. Ambil data pendukung
        $logs = AttendanceLog::where('pegawai_id', $pegawai->id)
            ->whereDate('scan_time', $date)
            ->orderBy('scan_time')
            ->get();

        // 2. PRIORITAS 1: Cek Izin/Sakit/Cuti
        $izin = PegawaiIzin::where('pegawai_id', $pegawai->id)
            ->where('tanggal_mulai', '<=', $date)
            ->where('tanggal_akhir', '>=', $date)
            ->where('status_approval', 'Approved')
            ->first();

        if ($izin) {
            Log::info("Pegawai {$pegawai->name} memiliki Izin: {$izin->jenis_izin}");
            return $this->processIzinStatus($pegawai, $date, $izin);
        }

        // 3. PRIORITAS 2: Cek Log Fingerprint
        if ($logs->isNotEmpty()) {
            Log::info("Fingerprint ditemukan untuk {$pegawai->name}");
            return $this->processHadirFromLogs($pegawai, $date, $logs);
        }

        // 4. PRIORITAS 3: Cek Schedule (Event, Override, Piket, Mengajar, Wajib Hadir)
        $schedule = $this->getDailySchedule($pegawai, $date);

        if ($schedule['is_working_day']) {
            return $this->markAsAlpha($pegawai, $date, $schedule);
        }

        // 5. Bukan Hari Kerja & Tidak Ada Log -> Hapus rekaman jika ada
        PegawaiAbsensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $date)
            ->delete();
            
        return null;
    }

    private function processIzinStatus($pegawai, $date, $izin)
    {
        $tahun = Tahun::where('tanggalmulai', '<=', $date)
            ->where('tanggalakhir', '>=', $date)
            ->first() ?? Tahun::aktif()->first();

        $allocation = $pegawai->ruleAllocations()->where('tahun_id', $tahun?->id)->with('attendanceRule')->first();
        $rule = $allocation?->attendanceRule;
        
        $gaji = 0;
        $makan = 0;

        // Aturan: Sakit, Cuti, DL dapat Gaji Pokok. Izin biasa (Pribadi) tidak dapat.
        if (in_array($izin->jenis_izin, ['Sakit', 'Cuti', 'Dinas Luar'])) {
            $gaji = $rule?->gaji_harian ?? 0;
        }

        // Hanya Dinas Luar yang dapat uang makan
        if ($izin->jenis_izin === 'Dinas Luar') {
            $makan = $rule?->bantuan_makan ?? 0;
        }

        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
            [
                'jam_masuk' => null, 
                'jam_pulang' => null, 
                'durasi_kerja' => null,
                'status' => $izin->jenis_izin,
                'nominal_gaji' => $gaji,
                'nominal_makan' => $makan,
                'total_honor' => $gaji + $makan,
                'attendance_source' => 'Manual (Izin)',
            ]
        );
    }

    private function processHadirFromLogs($pegawai, $date, $logs)
    {
        $schedule = $this->getDailySchedule($pegawai, $date);
        
        $firstLog = $logs->first();
        $lastLog = $logs->last();
        $jamMasuk = Carbon::parse($firstLog->scan_time);
        $jamPulang = Carbon::parse($lastLog->scan_time);
        
        $status = $schedule['status_label'] ?? 'Hadir';
        $honorHarian = $schedule['gaji_harian'] ?? 0;
        $uangMakan = $schedule['bantuan_makan'] ?? 0;
        $potongan = 0;

        if (!empty($schedule['jam_masuk'])) {
            $expectedIn = Carbon::parse($date . ' ' . $schedule['jam_masuk']);
            $toleransi = $schedule['toleransi_telat'] ?? 0;
            $lateThreshold = $expectedIn->copy()->addMinutes($toleransi);

            if ($jamMasuk->gt($lateThreshold)) {
                $status = 'Telat';
                $potongan = $schedule['denda_telat'] ?? 0;
            }
        }

        if (!$schedule['is_working_day']) {
            $status = 'Diluar Jadwal';
            $honorHarian = 0;
            $uangMakan = 0;
            $potongan = 0;
        }

        $totalHonor = max(0, ($honorHarian + $uangMakan) - $potongan);

        $source = 'Fingerprint';
        foreach ($logs as $log) {
            if ($log->machine_id === 'MANUAL') $source = 'Manual';
        }
        if ($status === 'Hadir (Event)') $source = 'Event';

        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
            [
                'jam_masuk' => $jamMasuk->toTimeString(),
                'jam_pulang' => $jamPulang->toTimeString(),
                'durasi_kerja' => $jamMasuk->diff($jamPulang)->format('%H:%I:%S'),
                'status' => $status,
                'nominal_gaji' => $honorHarian,
                'nominal_makan' => $uangMakan,
                'total_honor' => $totalHonor,
                'attendance_source' => $source,
                'created_by' => auth()->id() ?? null,
            ]
        );
    }

    private function markAsAlpha($pegawai, $date, $schedule)
    {
        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
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
        $carbonDate = Carbon::parse($date);
        
        $days = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'
        ];
        $dayName = $days[$carbonDate->format('D')] ?? '';

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
                'gaji_harian' => $event->bantuan_hadir, 
                'bantuan_makan' => 0, 
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
        $piket = JadwalPiket::where('pegawai_id', $pegawai->id)
            ->where('hari', $dayName)
            ->first();

        if ($piket) {
            $tahun = Tahun::where('tanggalmulai', '<=', $date)
                ->where('tanggalakhir', '>=', $date)
                ->first() ?? Tahun::aktif()->first();
            $allocation = $pegawai->ruleAllocations()->where('tahun_id', $tahun?->id)->with('attendanceRule')->first();
            $rule = $allocation?->attendanceRule;

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
             $tahun = Tahun::where('tanggalmulai', '<=', $date)
                ->where('tanggalakhir', '>=', $date)
                ->first() ?? Tahun::aktif()->first();
             $allocation = $pegawai->ruleAllocations()->where('tahun_id', $tahun?->id)->with('attendanceRule')->first();
             $rule = $allocation?->attendanceRule;

             return [
                'is_working_day' => true,
                'jam_masuk' => $rule ? $rule->jam_masuk : '07:00:00',
                'jam_pulang' => $rule ? $rule->jam_pulang : '14:00:00',
                'toleransi_telat' => $rule ? $rule->toleransi_telat : 15,
                'gaji_harian' => $rule ? $rule->gaji_harian : 0,
                'bantuan_makan' => $rule ? $rule->bantuan_makan : 0,
                'denda_telat' => $rule ? ($rule->denda_telat ?? 0) : 0,
                'status_label' => 'Mengajar',
            ];
        }

        // 5. Default Rule
        $tahun = Tahun::where('tanggalmulai', '<=', $date)
            ->where('tanggalakhir', '>=', $date)
            ->first() ?? Tahun::aktif()->first();

        $allocation = $pegawai->ruleAllocations()
            ->where('tahun_id', $tahun?->id)
            ->with('attendanceRule')
            ->first();

        $rule = $allocation?->attendanceRule;
        
        if (!$rule) {
            return [
                'is_working_day' => false,
                'jam_masuk' => null,
                'jam_pulang' => null,
                'status_label' => 'Tanpa Aturan',
            ];
        }

        // 6. CHECK MANDATORY DAYS
        $isMandatory = \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawai->id)
            ->where('tahun_id', $tahun?->id)
            ->where('hari', $dayName)
            ->exists();

        if ($isMandatory) {
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

        return [
            'is_working_day' => false,
            'jam_masuk' => null,
            'jam_pulang' => null,
            'status_label' => 'Libur',
        ];
    }
}
