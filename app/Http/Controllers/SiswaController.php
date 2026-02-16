<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\Jurusan;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');

        $query = Siswa::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nipd', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        // Whitelist safe sort columns
        $allowedSorts = ['nama', 'nipd', 'nisn', 'status', 'created_at', 'kelas'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $siswa = $query->paginate($perPage)->withQueryString();

        return view('siswa.index', compact('siswa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function export(Request $request)
    {
        $ids = $request->input('ids');
        if ($ids) {
            $ids = explode(',', $ids); // If IDs are passed as comma specific string
        }
        return Excel::download(new SiswaExport($ids), 'siswa.xlsx');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');
        
        if ($ids) {
            $siswa = Siswa::whereIn('id', $ids)->get();
            
            foreach ($siswa as $s) {
                if ($s->foto) {
                    Storage::disk('public')->delete($s->foto);
                }
                $s->delete();
            }
            
            return redirect()->back()->with('message', count($ids) . ' data siswa berhasil dihapus')->with('type', 'success');
        }
        
        return redirect()->back()->with('message', 'Tidak ada data yang dipilih')->with('type', 'warning');
    }

    public function import()
    {
        Excel::import(new SiswaImport, request()->file('file'));
        return redirect()->route('siswa.index')->with('message', 'Data siswa berhasil diimport')->with('type', 'success');
    }
     public function create()
    {
        return view('siswa.input-siswa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // @dd($request->all());
        $request->validate([
            'nama'=>'required',
            'nipd'=>'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,png|max:2048',
        ]);
        $data=$request->all();

        if($request->hasFile('foto')){
            $data['foto']=$request->file('foto')->store('uploads/foto','public');
        }
        Siswa::create($data);
        return redirect('siswa')->with('message','Data telah disimpan')->with('type','success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        // 1. Ambil Tahun Aktif
        $tahunAktif = \App\Models\Tahun::aktif()->first();
        $year = now()->year;

        // 2. Statistik Tahunan (Summary - Tahun Aktif)
        $summaryStats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Total' => 0];
        
        // Query Absensi Filter Tahun Ini
        $logs = \App\Models\Absensi::with(['logbook.jadwal.mapel', 'logbook.pegawai'])
            ->where('siswa_id', $siswa->id)
            ->whereHas('logbook', function($q) use ($year) {
                $q->whereYear('tanggal', $year);
            })
            ->get();

        foreach ($logs as $log) {
            $status = $log->status;
            if (isset($summaryStats[$status])) {
                $summaryStats[$status]++;
            }
            $summaryStats['Total']++;
        }

        // 3. Data Grafik Bulanan (Monthly Trend - Tahun Aktif)
        $months = range(1, 12);
        $chartData = [
            'Hadir' => [], 'Sakit' => [], 'Izin' => [], 'Alpha' => []
        ];

        foreach ($months as $m) {
            $monthLogs = $logs->filter(function($log) use ($m) {
                return \Carbon\Carbon::parse($log->logbook->tanggal)->month == $m;
            });

            $chartData['Hadir'][] = $monthLogs->where('status', 'Hadir')->count();
            $chartData['Sakit'][] = $monthLogs->where('status', 'Sakit')->count();
            $chartData['Izin'][] = $monthLogs->where('status', 'Izin')->count();
            $chartData['Alpha'][] = $monthLogs->where('status', 'Alpha')->count();
        }

        // 4. Riwayat Terbaru (Top 10)
        $recentLogs = $logs->sortByDesc(function($log) {
            return $log->logbook->tanggal . ' ' . ($log->logbook->jadwal->mulai ?? '00:00');
        })->take(10);

        // 5. Data Kelas Aktif
        $kelasAktif = $siswa->KelasSiswa()
            ->where('tahun_id', $tahunAktif->id ?? 0)
            ->with('kelas')
            ->first()
            ->kelas ?? '-';

        // 6. Riwayat Akademik (History Kelas per Tahun/Semester)
        $history = $siswa->KelasSiswa()
            ->with(['kelas', 'tahun'])
            ->orderByDesc('id') 
            ->get()
            ->map(function ($h) use ($siswa) {
                // Hitung Absensi per Periode Tahun Ajaran tersebut
                if ($h->tahun) {
                    $stats = \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->whereHas('logbook', function($q) use ($h) {
                            $q->whereBetween('tanggal', [$h->tahun->tanggalmulai, $h->tahun->tanggalakhir]);
                        })
                        ->selectRaw('status, count(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status')
                        ->toArray();
                    
                    $total = array_sum($stats);
                    $h->stats = [
                        'H' => $stats['Hadir'] ?? 0,
                        'S' => $stats['Sakit'] ?? 0,
                        'I' => $stats['Izin'] ?? 0,
                        'A' => $stats['Alpha'] ?? 0,
                        'Total' => $total,
                        'HadirPercent' => $total > 0 ? round((($stats['Hadir'] ?? 0) / $total) * 100, 1) : 0
                    ];
                } else {
                     $h->stats = ['H'=>0, 'S'=>0, 'I'=>0, 'A'=>0, 'Total'=>0, 'HadirPercent'=>0];
                }
                return $h;
            });

        // 7. Smart Prediction & Risk Analysis
        $prediction = [
            'status' => 'Aman',
            'color' => 'green',
            'message' => 'Tingkat kehadiran siswa sangat baik. Pertahankan!',
            'trend' => 'stabil', // naik, turun, stabil
            'predicted_score' => 100
        ];

        if ($summaryStats['Total'] > 0) {
            $attendanceRate = ($summaryStats['Hadir'] / $summaryStats['Total']) * 100;
            $alphaRate = ($summaryStats['Alpha'] / $summaryStats['Total']) * 100;
            
            $prediction['predicted_score'] = round($attendanceRate, 1);

            // Risk Logic
            if ($attendanceRate < 80 || $summaryStats['Alpha'] > 10) {
                $prediction['status'] = 'Bahaya';
                $prediction['color'] = 'red';
                $prediction['message'] = 'Siswa berisiko tidak naik kelas! Kehadiran di bawah 80% atau Alpha berlebih.';
            } elseif ($attendanceRate < 90 || $summaryStats['Alpha'] > 5) {
                $prediction['status'] = 'Waspada';
                $prediction['color'] = 'yellow';
                $prediction['message'] = 'Perlu perhatian. Tingkatkan kehadiran untuk hasil maksimal.';
            }

            // Trend Logic (Compare last 2 active months)
            $activeMonths = array_keys(array_filter($chartData['Hadir'], fn($val) => $val > 0));
            if (count($activeMonths) >= 2) {
                $lastMonthIdx = end($activeMonths);
                $prevMonthIdx = prev($activeMonths);
                
                $lastMonthHadir = $chartData['Hadir'][$lastMonthIdx];
                $prevMonthHadir = $chartData['Hadir'][$prevMonthIdx];

                if ($lastMonthHadir < $prevMonthHadir) {
                    $prediction['trend'] = 'turun';
                    $prediction['message'] .= ' Tren kehadiran menurun bulan ini.';
                } elseif ($lastMonthHadir > $prevMonthHadir) {
                    $prediction['trend'] = 'naik';
                }
            }
        }

        return view('siswa.show', compact('siswa', 'summaryStats', 'chartData', 'recentLogs', 'kelasAktif', 'year', 'history', 'prediction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $siswa=Siswa::find($siswa->id);
        return view('siswa.input-siswa',compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        // dd($request->all());
        $request->validate([
            'nama'=>'required',
            'nipd'=>'required',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('uploads/foto', 'public');
        }

        $siswa->update($data);

        return redirect()->route('siswa.index')->with('message', 'Data berhasil diperbarui')->with('type', 'success');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        // dd($siswa);
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        $siswa->delete();
        return redirect('siswa')->with('message','Data Berhasil Dihapus')->with('type','error');

    }

}
