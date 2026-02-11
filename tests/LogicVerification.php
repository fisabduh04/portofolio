<?php

use App\Models\AttendanceRule;
use App\Models\Pegawai;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// 1. Setup Data
$rule = AttendanceRule::create([
    'name' => 'Test Rule',
    'jam_masuk' => '08:00',
    'jam_pulang' => '16:00',
    'scan_masuk_start' => '06:00',
    'scan_pulang_end' => '18:00',
    'toleransi_telat' => 15,
    'bantuan_makan' => 10000,
    'gaji_harian' => 50000,
]);

$pegawai = Pegawai::first(); // Use existing temporary
if (!$pegawai) {
    echo "No Pegawai found. Creating temporary.\n";
    $pegawai = Pegawai::create(['name' => 'Test Pegawai', 'aktif' => 'Aktif']);
}
$pegawai->attendance_rule_id = $rule->id;
$pegawai->save();

// 2. Scenario A: On Time
echo "--- Scenario A: On Time ---\n";
$dateA = Carbon::today()->subDays(2)->toDateString();
AttendanceLog::create(['pegawai_id' => $pegawai->id, 'scan_time' => "$dateA 07:55:00"]);
AttendanceLog::create(['pegawai_id' => $pegawai->id, 'scan_time' => "$dateA 16:05:00"]);

(new AttendanceService())->calculateDailyAttendance($pegawai, $dateA);

$resultA = $pegawai->pegawaiAbsensis()->where('tanggal', $dateA)->first();
echo "Status: " . $resultA->status . "\n"; // Hadir
echo "Masuk: " . $resultA->jam_masuk . "\n";
echo "Total Honor: " . $resultA->total_honor . "\n"; // 60000

// 3. Scenario B: Late
echo "\n--- Scenario B: Late ---\n";
$dateB = Carbon::today()->subDays(1)->toDateString();
AttendanceLog::create(['pegawai_id' => $pegawai->id, 'scan_time' => "$dateB 08:20:00"]); // > 08:15
AttendanceLog::create(['pegawai_id' => $pegawai->id, 'scan_time' => "$dateB 16:00:00"]);

(new AttendanceService())->calculateDailyAttendance($pegawai, $dateB);

$resultB = $pegawai->pegawaiAbsensis()->where('tanggal', $dateB)->first();
echo "Status: " . $resultB->status . "\n"; // Telat
echo "Masuk: " . $resultB->jam_masuk . "\n";

// Cleanup
$rule->delete();
// Keep logs for review or delete
