<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\absensi;
use Illuminate\Http\Request;
use App\Models\siswa;
use App\Models\jadwal;
use App\Models\kelas;
use App\Models\KelasSiswa;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('absensi.index',compact('users'));       
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
    public function show(absensi $absensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(absensi $absensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, absensi $absensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(absensi $absensi)
    {
        //
    }

    public function rekap(Request $request) {
        $kategori = $request->input('kategori', 'mapel');
        $filterKelasId = $request->input('kelas_id'); 
        $filterPegawaiId = $request->input('pegawai_id');
        $startDate = $request->input('from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('to', now()->toDateString());

        // Call the trait method (which we will ensure is used by the controller)
        // Since we are not actually using a Trait file yet, let's put the logic inline for now to restore functionality quickly as per user request.
        // Ideally this should be in a Trait or Service class.

        $query = \App\Models\absensi::with(['siswa.kelas', 'jadwal.pegawai', 'jadwal.mapel']);

        if ($filterKelasId) {
            $query->whereHas('siswa', function($q) use ($filterKelasId) {
                $q->where('kelas_id', $filterKelasId);
            });
        }

        if ($filterPegawaiId && $kategori == 'mapel') {
             $query->whereHas('jadwal', function($q) use ($filterPegawaiId) {
                $q->where('pegawai_id', $filterPegawaiId);
            });
        }
        
        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $absensiData = $query->get();

        // Calculate Stats
        $stats = [
            'Hadir' => $absensiData->where('status', 'H')->count(),
            'Sakit' => $absensiData->where('status', 'S')->count(),
            'Izin' => $absensiData->where('status', 'I')->count(),
            'Alpha' => $absensiData->where('status', 'A')->count(),
        ];
        $totalAbsensi = $absensiData->count();

        // Daily Trend
        $dailyTrend = $absensiData->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d');
        })->map(function ($dayGroup, $date) {
             $total = $dayGroup->count();
             $present = $dayGroup->where('status', 'H')->count();
             return (object) [
                'tanggal' => $date, // \Carbon\Carbon::parse($date)->format('d M'),
                'total' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
             ];
        })->values(); // Reset keys for chart usage

        // Top Alpha
        $topAlpha = $absensiData->where('status', 'A')
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
        $rekapGuru = \App\Models\jadwal::with('pegawai')
            ->get()
            ->groupBy('pegawai_id')
            ->map(function ($group) {
                 return (object) [
                    'name' => $group->first()->pegawai->name,
                    'total_jurnal' => $group->count() // Logic simplified for demo; ideally count matched absensi sessions
                 ];
            })
            ->sortByDesc('total_jurnal')
            ->values();

         // Rekap Kelas Performance
         $rekapKelas = $absensiData->groupBy(function($item) {
             return $item->siswa->kelas->kelas ?? 'Unknown';
         })->map(function ($group, $kelasName) {
             return (object) [
                 'kelas' => $kelasName,
                 'hadir' => $group->where('status', 'H')->count(),
                 'alpha' => $group->where('status', 'A')->count(),
                 'total' => $group->count()
             ];
         })->values();

        $listKelas = \App\Models\kelas::all();
        $listPegawai = \App\Models\pegawai::all();

        return view('absensi.rekap', compact(
            'kategori', 'filterKelasId', 'filterPegawaiId', 'listKelas', 'listPegawai',
            'stats', 'totalAbsensi', 'dailyTrend', 'topAlpha', 'rekapGuru', 'rekapKelas'
        ));
    }

    public function rekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $listKelas = \App\Models\kelas::all();
        $selectedKelas = $kelasId ? \App\Models\kelas::find($kelasId) : null;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $rekapData = collect([]);

        if ($kelasId) {
             $students = \App\Models\siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
             $startDate = "$year-$month-01";
             $endDate = "$year-$month-$daysInMonth";

             // Get raw attendance
             $absensis = \App\Models\absensi::whereHas('siswa', function($q) use($kelasId){
                 $q->where('kelas_id', $kelasId);
             })
             ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
             ->get();

             $rekapData = $students->map(function($student) use ($absensis, $daysInMonth, $year, $month) {
                 $studentAbsensi = $absensis->where('siswa_id', $student->id);
                 $days = [];
                 $totals = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

                 for($d=1; $d<=$daysInMonth; $d++) {
                     $dateStr = sprintf('%s-%s-%02d', $year, $month, $d);
                     // Find records for this date
                     $dailyRecord = $studentAbsensi->filter(function($item) use ($dateStr) {
                         return \Carbon\Carbon::parse($item->created_at)->toDateString() == $dateStr;
                     })->first(); // Assuming one status per day for simplicity in matrix, or take latest

                     $status = $dailyRecord ? $dailyRecord->status : null;
                     if($status && isset($totals[$status])) {
                         $totals[$status]++;
                     }

                     $days[$d] = [
                         'main_status' => $status
                     ];
                 }

                 return [
                     'id' => $student->id,
                     'nama' => $student->nama,
                     'days' => $days,
                     'daily_totals' => $totals
                 ];
             });
        }

        return view('absensi.rekap_bulanan', compact(
            'kelasId', 'month', 'year', 'listKelas', 'selectedKelas', 'rekapData', 'daysInMonth'
        ));
    }

    public function rekapTahunan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $tahunId = $request->input('tahun_id'); // Filter by Tahun Ajaran ID
        
        $listKelas = \App\Models\kelas::all();
        $listTahun = \App\Models\tahun::all(); // Assuming you have a Tahun model
        
        $selectedKelas = $kelasId ? \App\Models\kelas::find($kelasId) : null;
        $rekapData = collect([]);

        if ($kelasId && $tahunId) {
            $students = \App\Models\siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
            
            // Assuming Absensi has a relationship to Jadwal, and Jadwal belongs to Tahun.
            // Or strictly filtered by date range of the academic year if that's how it's stored.
            // For now, let's assume we filter by filtering Jadwal's tahun_id via the relationship
            
            $absensis = \App\Models\absensi::whereHas('siswa', function($q) use($kelasId){
                    $q->where('kelas_id', $kelasId);
                })
                ->whereHas('jadwal', function($q) use ($tahunId) {
                    $q->where('tahun_id', $tahunId);
                })
                ->get();

             $rekapData = $students->map(function($student) use ($absensis) {
                 $studentRecords = $absensis->where('siswa_id', $student->id);
                 return (object) [
                     'nama' => $student->nama,
                     'nipd' => $student->nipd ?? '-',
                     'hadir' => $studentRecords->where('status', 'H')->count(),
                     'sakit' => $studentRecords->where('status', 'S')->count(),
                     'izin' => $studentRecords->where('status', 'I')->count(),
                     'alpha' => $studentRecords->where('status', 'A')->count(),
                 ];
             });
        }

        return view('absensi.rekap_tahunan', compact(
            'kelasId', 'tahunId', 'listKelas', 'listTahun', 'selectedKelas', 'rekapData'
        ));
    }
}
