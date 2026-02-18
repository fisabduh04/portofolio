<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$activeYear = \App\Models\Tahun::where('isActive', 1)->first();
echo "Active Year ID: " . $activeYear->id . " - " . $activeYear->tahun . " " . $activeYear->semester . "\n";

$jadwalCount = \App\Models\Jadwal::where('tahun_id', $activeYear->id)->count();
echo "Jadwals for active year: " . $jadwalCount . "\n";

$piketCount = \App\Models\JadwalPiket::where('tahun_id', $activeYear->id)->count();
echo "Pikets for active year: " . $piketCount . "\n";

// Show sample jadwal data
$samples = \App\Models\Jadwal::where('tahun_id', $activeYear->id)->select('pegawai_id','hari')->limit(5)->get();
echo "Sample jadwals: " . json_encode($samples->toArray()) . "\n";

// Show all tahun
$tahuns = \App\Models\Tahun::all(['id','tahun','semester','isActive']);
echo "All Tahun: " . json_encode($tahuns->toArray()) . "\n";
