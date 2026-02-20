<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Data Master Counts
        $totalSiswa = \App\Models\Siswa::count();
        $totalPegawai = \App\Models\Pegawai::count();
        $totalKelas = \App\Models\Kelas::count();
        $totalJurusan = \App\Models\Jurusan::count();

        // 2. Statistik Presensi Hari Ini
        $today = now()->toDateString();
        $todayStats = [
            'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0
        ];
        
        // Ambil data absensi hari ini lewat Logbook
        $todayAbsensi = \App\Models\Absensi::whereHas('logbook', function($q) use ($today) {
            $q->where('tanggal', $today);
        })->get();

        foreach ($todayAbsensi as $ab) {
            if (isset($todayStats[$ab->status])) {
                $todayStats[$ab->status]++;
            }
        }
        
        $totalHadirToday = $todayStats['Hadir'];
        $attendanceRate = $totalSiswa > 0 ? round(($totalHadirToday / $totalSiswa) * 100, 1) : 0;

        // 3. Tren Kehadiran 7 Hari Terakhir
        $endDate = now();
        $startDate = now()->subDays(6);
        $trendData = [];
        $dates = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dates[] = $date->format('d M'); // Label sumbu X
            
            // Hitung hadir pada tanggal tersebut
            $countHadir = \App\Models\Absensi::whereHas('logbook', function($q) use ($currentDate) {
                $q->where('tanggal', $currentDate);
            })->where('status', 'Hadir')->count();
            
            $trendData[] = $countHadir;
        }

        // 4. Statistik Kepegawaian (Employee Stats)
        $totalPegawaiAktif = \App\Models\Pegawai::where('aktif', 'Aktif')->count();
        
        // Today's Employee Attendance
        $todayPegawaiStats = [
            'Hadir' => 0, 'Telat' => 0, 'Izin' => 0, 'Alpha' => 0, 'Sakit' => 0
        ];

        $todayPegawaiAbsensi = \App\Models\PegawaiAbsensi::with('pegawai')
            ->where('tanggal', $today)
            ->get();

        foreach ($todayPegawaiAbsensi as $ab) {
            $status = $ab->status === 'Hadir (Event)' ? 'Hadir' : $ab->status; // Group Event as Hadir
            if (isset($todayPegawaiStats[$status])) {
                $todayPegawaiStats[$status]++;
            } else {
                 // Handle unexpected status
                 if (!isset($todayPegawaiStats['Lainnya'])) $todayPegawaiStats['Lainnya'] = 0;
                 $todayPegawaiStats['Lainnya']++;
            }
        }
        
        // Calculate Rates
        $pegawaiHadirCount = $todayPegawaiStats['Hadir'] + $todayPegawaiStats['Telat'];
        $pegawaiAttendanceRate = $totalPegawaiAktif > 0 ? round(($pegawaiHadirCount / $totalPegawaiAktif) * 100, 1) : 0;

        // Leaderboards
        $lateEmployees = $todayPegawaiAbsensi->where('status', 'Telat')->sortByDesc('jam_masuk')->take(5);
        $earlyBirds = $todayPegawaiAbsensi->where('status', 'Hadir')->sortBy('jam_masuk')->take(5);

        // Employee Monthly Trend (Last 7 Days for consistency with Student Chart)
        $pegawaiTrendData = [];
        // Re-use $dates from Student section or create new if needed. 
        // We can reuse the loop structure but query PegawaiAbsensi.
        
        foreach ($dates as $index => $dateLabel) {
            // Need the actual date object from previous loop or reconstruct it.
            // Simpler: Just query based on the date string if we stored it, 
            // but $dates only has labels "d M".
            // Let's reconstruct the date loop to be safe and efficient.
             $loopDate = $startDate->copy()->addDays($index)->toDateString();
             
             $countHadirPegawai = \App\Models\PegawaiAbsensi::where('tanggal', $loopDate)
                ->whereIn('status', ['Hadir', 'Telat', 'Hadir (Event)'])
                ->count();
            
            $pegawaiTrendData[] = $countHadirPegawai;
        }

        return view('dashboard.index', compact(
            'totalSiswa', 'totalPegawai', 'totalKelas', 'totalJurusan',
            'todayStats', 'attendanceRate', 'dates', 'trendData',
            'totalPegawaiAktif', 'todayPegawaiStats', 'pegawaiAttendanceRate', 
            'lateEmployees', 'earlyBirds', 'pegawaiTrendData'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
