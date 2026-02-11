<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Logbook;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\HariLibur;
use App\Traits\AbsensiRekapTrait;
use Illuminate\Http\Request;

class AbsensiReportController extends Controller
{
    use AbsensiRekapTrait;

    public function rekap(Request $request) {
        $view = $request->input('view', 'summary');
        $kategori = $request->input('kategori', 'mapel');
        $filterKelasId = $request->input('kelas_id'); 
        $filterPegawaiId = $request->input('pegawai_id');
        $startDate = $request->input('from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('to', now()->toDateString());

        $listKelas = Kelas::all();
        $listPegawai = Pegawai::all();
        $listMapel = Mapel::all();

        if ($view == 'detail') {
            $query = Logbook::with(['jadwal.pegawai', 'jadwal.mapel', 'kelas', 'pegawai', 'absensis']);

            if ($filterKelasId) {
                $query->where('kelas_id', $filterKelasId);
            }

            if ($filterPegawaiId) {
                $query->where('pegawai_id', $filterPegawaiId);
            }

            if ($kategori) {
                // Jika kategori adalah mapel, kita ingin melihat log mata pelajaran
                // Jika kategori adalah piket, kita ingin melihat piket_masuk/piket_pulang
                if ($kategori == 'mapel') {
                    $query->where('kategori', 'mapel');
                } else {
                    $query->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
                }
            }

            $query->whereBetween('tanggal', [$startDate, $endDate]);
            $logs = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15);

            return view('absensi.rekap-detail', compact(
                'view', 'kategori', 'filterKelasId', 'filterPegawaiId', 'startDate', 'endDate',
                'listKelas', 'listPegawai', 'logs'
            ));
        }

        // --- LOGIKA TAMPILAN RINGKASAN ---
        $query = Absensi::with(['siswa.kelas', 'logbook.jadwal.pegawai', 'logbook.jadwal.mapel']);

        if ($filterKelasId) {
            $query->whereHas('siswa', function($q) use ($filterKelasId) {
                $q->whereHas('KelasSiswa', function($kq) use ($filterKelasId) {
                    $kq->where('kelas_id', $filterKelasId);
                });
            });
        }

        if ($filterPegawaiId) {
             $query->whereHas('logbook', function($q) use ($filterPegawaiId) {
                $q->where('pegawai_id', $filterPegawaiId);
            });
        }
        
        $query->whereHas('logbook', function($q) use ($startDate, $endDate, $kategori) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
            if ($kategori == 'mapel') {
                $q->where('kategori', 'mapel');
            } else {
                $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
            }
        });

        $absensiData = $query->get();

        // Hitung Statistik
        $stats = [
            'Hadir' => $absensiData->where('status', 'Hadir')->count(),
            'Sakit' => $absensiData->where('status', 'Sakit')->count(),
            'Izin' => $absensiData->where('status', 'Izin')->count(),
            'Alpha' => $absensiData->where('status', 'Alpha')->count(),
        ];
        $totalAbsensi = $absensiData->count();

        // Tren Harian
        $dailyTrend = $absensiData->groupBy(function($item) {
            return $item->logbook->tanggal;
        })->map(function ($dayGroup, $date) {
             $total = $dayGroup->count();
             $present = $dayGroup->where('status', 'Hadir')->count();
             return (object) [
                'tanggal' => $date,
                'total' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
             ];
        })->sortBy('tanggal')->values();

        // Top Alpha
        $topAlpha = $absensiData->where('status', 'Alpha')
            ->groupBy('siswa_id')
            ->map(function ($group) {
                return (object) [
                    'siswa' => $group->first()->siswa,
                    'total' => $group->count()
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Rekap Guru (Log Pengisian)
        $rekapGuru = Logbook::with('pegawai')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy('pegawai_id')
            ->map(function ($group) {
                 return (object) [
                    'name' => $group->first()->pegawai->name ?? 'Unknown',
                    'total_jurnal' => $group->count()
                 ];
            })
            ->sortByDesc('total_jurnal')
            ->values();

         // Rekap Performa Kelas
         $rekapKelas = $absensiData->groupBy(function($item) {
             return $item->siswa->kelas->first()->kelas ?? 'Unknown';
         })->map(function ($group, $kelasName) {
             return (object) [
                 'kelas' => $kelasName,
                 'hadir' => $group->where('status', 'Hadir')->count(),
                 'alpha' => $group->where('status', 'Alpha')->count(),
                 'total' => $group->count()
             ];
         })->values();

        return view('absensi.rekap', compact(
            'view', 'kategori', 'filterKelasId', 'filterPegawaiId', 'listKelas', 'listPegawai', 'listMapel',
            'stats', 'totalAbsensi', 'dailyTrend', 'topAlpha', 'rekapGuru', 'rekapKelas',
            'startDate', 'endDate'
        ));
    }

    public function rekapHarian(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');
        $pegawaiId = $request->input('pegawai_id');
        $typeGuru = $request->input('type_guru'); // 'mapel' or 'piket'
        $viewMode = $request->input('view_mode', 'sederhana');
        
        $listKelas = Kelas::orderBy('kelas')->get();
        $listPegawai = Pegawai::orderBy('name')->get();
        
        // Professional Fix: Fetch Holiday Info
        $activeYear = Tahun::aktif()->first();
        $holiday = $activeYear ? HariLibur::getHoliday($date, $activeYear->id) : null;

        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        
        $rekapData = collect([]);
        $summaryStats = [
            'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Total' => 0
        ];

        if ($request->has('kelas_id')) {
            $data = $this->getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru);
            $rekapData = $data['rekapData'];
            $summaryStats = $data['summaryStats'];
            
            // Re-calculate Summary Stats from the processed collection
            // Because extract method returns collection, but we need aggregated stats
            // Actually, let's just recalculate them from the collection to be safe and simple
            
            // Professional Fix: Initialize ALL possible keys including 'Libur' to prevent Undefined Array Key error
            $summaryStats = [
                'Hadir' => 0, 
                'Sakit' => 0, 
                'Izin' => 0, 
                'Alpha' => 0, 
                'Libur' => 0, // Explicitly handle 'Libur' status
                'Total' => 0
            ]; 

             foreach ($rekapData as $student) {
                // Defensive Coding: Only count if status exists in our summary array
                if ($student->daily_status != '-' && array_key_exists($student->daily_status, $summaryStats)) {
                     $summaryStats[$student->daily_status]++;
                }
                $summaryStats['Total']++;
            }
        }

        return view('absensi.rekap_harian', compact('listKelas', 'listPegawai', 'rekapData', 'summaryStats', 'date', 'kelasId', 'pegawaiId', 'typeGuru', 'selectedKelas', 'viewMode', 'holiday'));
    }

    public function rekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $viewMode = $request->input('view_mode', 'sederhana'); // Default simple
        $typeGuru = $request->input('type_guru', 'mapel'); // Default mapel

        $listKelas = Kelas::all();
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(date('Y'), 2020);
        
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        
        $data = ['rekapData' => collect([]), 'summaryStats' => ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0], 'dates' => []];

        if ($kelasId) {
             $data = $this->getPrivateRekapBulananData($month, $year, $kelasId, $typeGuru);
        }

        $rekapData = $data['rekapData'];
        $summaryStats = $data['summaryStats'];
        $dates = $data['dates'];

        return view('absensi.rekap_bulanan', compact(
            'kelasId', 'month', 'year', 'viewMode', 'listKelas', 'months', 'years', 'selectedKelas', 'rekapData', 'summaryStats', 'dates', 'typeGuru'
        ));
    }

    public function rekapTahunan(Request $request) 
    {
        $kelasId = $request->input('kelas_id');
        $year = $request->input('year', now()->year);
        $viewMode = $request->input('view_mode', 'detail_harian');
        $typeGuru = $request->input('type_guru', 'mapel');
        
        $listKelas = Kelas::orderBy('kelas')->get();
        $years = range(now()->year, now()->year - 5);
        $months = range(1, 12);
        
        $rekapData = collect([]);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];
        $selectedKelas = null;

        if ($kelasId) {
             $data = $this->getPrivateRekapTahunanData($year, $kelasId, $typeGuru);
             $rekapData = $data['rekapData'];
             $summaryStats = $data['summaryStats'];
             $selectedKelas = Kelas::find($kelasId);
        }

        return view('absensi.rekap_tahunan', compact('rekapData', 'summaryStats', 'listKelas', 'years', 'year', 'kelasId', 'months', 'selectedKelas', 'viewMode', 'typeGuru'));
    }

    public function rekapPeriode(Request $request) 
    {
        $kelasId = $request->input('kelas_id');
        $startDate = $request->input('start_date', now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $viewMode = $request->input('view_mode', 'ringkasan');
        $typeGuru = $request->input('type_guru', 'mapel');
        
        $listKelas = Kelas::orderBy('kelas')->get();
        
        $rekapData = collect([]);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];
        $selectedKelas = null;
        $dates = [];

        if ($kelasId) {
             $data = $this->getPrivateRekapPeriodeData($startDate, $endDate, $kelasId, $typeGuru);
             $rekapData = $data['rekapData'];
             $summaryStats = $data['summaryStats'];
             $dates = $data['dates'];
             $selectedKelas = Kelas::find($kelasId);
        }

        return view('absensi.rekap_periode', compact('rekapData', 'summaryStats', 'listKelas', 'startDate', 'endDate', 'kelasId', 'selectedKelas', 'viewMode', 'dates', 'typeGuru'));
    }
}
