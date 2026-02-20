<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\PegawaiWajibHadir;
use Carbon\Carbon;

echo "--- DEBUG START ---\n";

// 1. Check Active Year
$tahunAjaran = Tahun::aktif()->first();
echo "Active Year: " . ($tahunAjaran ? $tahunAjaran->tahun . " (ID: {$tahunAjaran->id})" : "NONE") . "\n";

// 2. Pick a random employee
$pegai = Pegawai::orderBy('name')->first();
if (!$pegai) {
    echo "No employees found.\n";
    exit;
}
echo "Checking Logic for Pegawai: {$pegai->name} (ID: {$pegai->id})\n";

// 3. Check Schedule for this employee
$wajibHadirDays = [];
if ($tahunAjaran) {
    $wajibHadirDays = PegawaiWajibHadir::where('pegawai_id', $pegai->id)
        ->where('tahun_id', $tahunAjaran->id)
        ->pluck('hari')
        ->toArray();
}

echo "Wajib Hadir Days from DB: [" . implode(', ', $wajibHadirDays) . "]\n";

if (empty($wajibHadirDays)) {
    echo "WARNING: No schedule found for this employee in the active year.\n";
} else {
    // Check Day Name Matching
    $date = Carbon::now()->startOfWeek(); // Monday
    $dayName = $date->locale('id')->isoFormat('dddd');
    echo "Testing Date: {$date->format('Y-m-d')} ({$dayName})\n";
    
    if (in_array($dayName, $wajibHadirDays)) {
        echo "MATCH: $dayName is in schedule.\n";
    } else {
        echo "NO MATCH: $dayName is NOT in schedule. (Checked against: " . implode(', ', $wajibHadirDays) . ")\n";
    }
}
echo "--- DEBUG END ---\n";
