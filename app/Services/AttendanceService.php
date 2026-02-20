<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\PegawaiAbsensi;
use App\Models\AttendanceRule;
use App\Models\PegawaiIzin;
use App\Models\SpecialEvent;
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

        $carbonDate = Carbon::parse($date);
        $dayNameIndo = $this->getIndonesianDayName($carbonDate);
        
        // 2. Tentukan Tahun Ajaran Aktif untuk rujukan aturan (Rules)
        $tahun = Tahun::where('tanggalmulai', '<=', $date)
            ->where('tanggalakhir', '>=', $date)
            ->first() ?? Tahun::aktif()->first();

        if (!$tahun) {
            Log::warning("Peringatan: Tidak ada Tahun Ajaran aktif untuk tanggal {$date}");
            return null;
        }

        // 3. PRIORITAS 1: Cek Log Fingerprint (Jika ada log, dianggap HADIR)
        if ($logs->isNotEmpty()) {
            Log::info("Fingerprint ditemukan untuk {$pegawai->name}");
            return $this->processHadirFromLogs($pegawai, $date, $logs, $tahun);
        }

        // 4. PRIORITAS 2: Cek Izin/Sakit/Cuti
        $izin = PegawaiIzin::where('pegawai_id', $pegawai->id)
            ->where('tanggal_mulai', '<=', $date)
            ->where('tanggal_akhir', '>=', $date)
            ->where('status_approval', 'Approved')
            ->first();

        if ($izin) {
            Log::info("Pegawai {$pegawai->name} memiliki Izin: {$izin->jenis_izin}");
            return $this->processIzinStatus($pegawai, $date, $izin, $tahun);
        }

        // 5. PRIORITAS 3: Cek Special Event (Kegiatan Khusus)
        $event = SpecialEvent::whereHas('participants', function($q) use ($pegawai) {
                $q->where('pegawai_id', $pegawai->id);
            })
            ->where('date', $date)
            ->first();

        if ($event) {
            Log::info("Pegawai {$pegawai->name} terdaftar di Event: {$event->name}");
            return $this->processEventStatus($pegawai, $date, $event);
        }

        // 6. PRIORITAS 4: Cek Wajib Hadir (Jika harus masuk tapi tidak ada log/izin -> ALPHA)
        return $this->processAlphaCheck($pegawai, $date, $dayNameIndo, $tahun);
    }

    /**
     * Detail Logika Hadir (Berdasarkan Fingerprint)
     */
    private function processHadirFromLogs($pegawai, $date, $logs, $tahun)
    {
        $allocation = $pegawai->ruleAllocations()->where('tahun_id', $tahun->id)->with('attendanceRule')->first();
        $rule = $allocation?->attendanceRule;

        $firstLog = $logs->first();
        $lastLog = $logs->last();
        $jamMasuk = Carbon::parse($firstLog->scan_time);
        $jamPulang = Carbon::parse($lastLog->scan_time);
        
        $status = 'Hadir';
        $honorHarian = $rule?->gaji_harian ?? 0;
        $uangMakan = $rule?->bantuan_makan ?? 0;
        $potongan = 0;

        // Cek Keterlambatan
        if ($rule) {
            $ruleMasuk = Carbon::parse($date . ' ' . $rule->jam_masuk);
            $limitLate = $ruleMasuk->copy()->addMinutes($rule->toleransi_telat);
            
            if ($jamMasuk->gt($limitLate)) {
                $status = 'Telat';
                $potongan = $rule->denda_telat ?? 0;
            }
        }

        $total = ($honorHarian + $uangMakan) - $potongan;

        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
            [
                'jam_masuk' => $jamMasuk->toTimeString(),
                'jam_pulang' => $jamPulang->toTimeString(),
                'durasi_kerja' => $jamMasuk->diff($jamPulang)->format('%H:%I:%S'),
                'status' => $status,
                'nominal_gaji' => $honorHarian,
                'nominal_makan' => $uangMakan,
                'total_honor' => max(0, $total),
            ]
        );
    }

    /**
     * Detail Logika Perizinan (Gaji tetap cair pada Sakit/Cuti/DL)
     */
    private function processIzinStatus($pegawai, $date, $izin, $tahun)
    {
        $allocation = $pegawai->ruleAllocations()->where('tahun_id', $tahun->id)->with('attendanceRule')->first();
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
                'jam_masuk' => null, 'jam_pulang' => null, 'durasi_kerja' => null,
                'status' => $izin->jenis_izin,
                'nominal_gaji' => $gaji,
                'nominal_makan' => $makan,
                'total_honor' => $gaji + $makan,
            ]
        );
    }

    /**
     * Detail Logika Special Event
     */
    private function processEventStatus($pegawai, $date, $event)
    {
        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
            [
                'jam_masuk' => $event->start_time,
                'jam_pulang' => $event->end_time,
                'status' => 'Event: ' . $event->name,
                'nominal_gaji' => $event->bantuan_hadir, // Event memberikan bantuan hadir khusus
                'nominal_makan' => 0,
                'total_honor' => $event->bantuan_hadir,
            ]
        );
    }

    /**
     * Detail Logika Alpha Check
     */
    private function processAlphaCheck($pegawai, $date, $dayName, $tahun)
    {
        $isWajib = \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawai->id)
            ->where('tahun_id', $tahun->id)
            ->where('hari', $dayName)
            ->exists();

        if (!$isWajib) {
            Log::info("Hari ini ({$dayName}) bukan jadwal wajib untuk {$pegawai->name}. Tidak ditandai Alpha.");
            return null; 
        }

        Log::warning("Pegawai {$pegawai->name} MANGKIR (Alpha) pada {$date}");

        return PegawaiAbsensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $date],
            [
                'status' => 'Alpha',
                'nominal_gaji' => 0, 'nominal_makan' => 0, 'total_honor' => 0,
            ]
        );
    }

    /**
     * Helper: Nama Hari Indonesia
     */
    private function getIndonesianDayName($carbonDate) {
        $days = [
            'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
            'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'
        ];
        return $days[$carbonDate->format('D')] ?? '';
    }
}

