<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Logbook;
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
    public function create(Request $request)
    {
        $jadwalId = $request->query('jadwal_id');
        
        if (!$jadwalId) {
            return redirect()->back()->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $jadwal = Jadwal::with(['kelas', 'mapel', 'pegawai'])->findOrFail($jadwalId);
        
        // Ambil daftar siswa berdasarkan kelas dan tahun ajaran dari jadwal
        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($jadwal) {
            $q->where('kelas_id', $jadwal->kelas_id)
              ->where('tahun_id', $jadwal->tahun_id);
        })->orderBy('nama')->get();

        return view('absensi.input', compact('jadwal', 'students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'attendance.*.keterangan' => 'nullable|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $jadwal = Jadwal::findOrFail($request->jadwal_id);

            // 1. Handle Multiple Photo Uploads
            $photos = [];
            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $photos[] = $file->store('absensi/foto', 'public');
                }
            }

            // 2. Create Logbook (Jurnal)
            $logbook = Logbook::create([
                'kategori' => 'mapel',
                'jadwal_id' => $jadwal->id,
                'kelas_id' => $jadwal->kelas_id,
                'pegawai_id' => $jadwal->pegawai_id,
                'tanggal' => now()->toDateString(),
                'materi' => $request->materi,
                'catatan' => $request->catatan,
                'foto' => json_encode($photos),
            ]);

            // 3. Create Student Attendance Entries
            foreach ($request->attendance as $siswaId => $data) {
                Absensi::create([
                    'logbook_id' => $logbook->id,
                    'siswa_id' => $siswaId,
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('jadwal.index')
                ->with('type', 'success')
                ->with('message', 'Presensi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan presensi: ' . $e->getMessage());
            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Gagal menyimpan presensi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Absensi $absensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absensi $absensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Absensi $absensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absensi $absensi)
    {
        //
    }

    public function rekap(Request $request) {
        $view = $request->input('view', 'summary');
        $kategori = $request->input('kategori', 'mapel');
        $filterKelasId = $request->input('kelas_id'); 
        $filterPegawaiId = $request->input('pegawai_id');
        $startDate = $request->input('from', now()->startOfMonth()->toDateString());
        $endDate = $request->input('to', now()->toDateString());

        $listKelas = Kelas::all();
        $listPegawai = Pegawai::all();

        if ($view == 'detail') {
            $query = Logbook::with(['jadwal.pegawai', 'jadwal.mapel', 'kelas', 'pegawai', 'absensis']);

            if ($filterKelasId) {
                $query->where('kelas_id', $filterKelasId);
            }

            if ($filterPegawaiId) {
                $query->where('pegawai_id', $filterPegawaiId);
            }

            if ($kategori) {
                // If kategori is mapel, we likely want to see subject logs
                // If kategori is piket, we want to see piket_masuk/piket_pulang
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

        // --- SUMMARY VIEW LOGIC ---
        $query = Absensi::with(['siswa.kelas', 'logbook.jadwal.pegawai', 'logbook.jadwal.mapel']);

        if ($filterKelasId) {
            $query->whereHas('siswa', function($q) use ($filterKelasId) {
                $q->where('kelas_id', $filterKelasId);
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

        // Calculate Stats
        $stats = [
            'Hadir' => $absensiData->where('status', 'Hadir')->count(),
            'Sakit' => $absensiData->where('status', 'Sakit')->count(),
            'Izin' => $absensiData->where('status', 'Izin')->count(),
            'Alpha' => $absensiData->where('status', 'Alpha')->count(),
        ];
        $totalAbsensi = $absensiData->count();

        // Daily Trend
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

         // Rekap Kelas Performance
         $rekapKelas = $absensiData->groupBy(function($item) {
             return $item->siswa->kelas->kelas ?? 'Unknown';
         })->map(function ($group, $kelasName) {
             return (object) [
                 'kelas' => $kelasName,
                 'hadir' => $group->where('status', 'Hadir')->count(),
                 'alpha' => $group->where('status', 'Alpha')->count(),
                 'total' => $group->count()
             ];
         })->values();

        return view('absensi.rekap', compact(
            'view', 'kategori', 'filterKelasId', 'filterPegawaiId', 'listKelas', 'listPegawai',
            'stats', 'totalAbsensi', 'dailyTrend', 'topAlpha', 'rekapGuru', 'rekapKelas',
            'startDate', 'endDate'
        ));
    }

    public function rekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $listKelas = Kelas::all();
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $rekapData = collect([]);

        if ($kelasId) {
             $students = Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
             $startDate = "$year-$month-01";
             $endDate = "$year-$month-$daysInMonth";

             // Get raw attendance
             $absensis = Absensi::whereHas('siswa', function($q) use($kelasId){
                 $q->where('kelas_id', $kelasId);
             })
             ->whereHas('logbook', function($q) use ($startDate, $endDate) {
                 $q->whereBetween('tanggal', [$startDate, $endDate]);
             })
             ->get();

             $rekapData = $students->map(function($student) use ($absensis, $daysInMonth, $year, $month) {
                 $studentAbsensi = $absensis->where('siswa_id', $student->id);
                 $days = [];
                 $totals = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];

                 for($d=1; $d<=$daysInMonth; $d++) {
                     $dateStr = sprintf('%s-%s-%02d', $year, $month, $d);
                     
                     $dailyRecords = $studentAbsensi->filter(function($item) use ($dateStr) {
                         return $item->logbook->tanggal == $dateStr;
                     });

                     $status = null;
                     if ($dailyRecords->count() > 0) {
                         // Priority: Alpha > Sakit > Izin > Hadir
                         if ($dailyRecords->contains('status', 'Alpha')) {
                             $status = 'Alpha';
                         } elseif ($dailyRecords->contains('status', 'Sakit')) {
                             $status = 'Sakit';
                         } elseif ($dailyRecords->contains('status', 'Izin')) {
                             $status = 'Izin';
                         } else {
                             $status = 'Hadir';
                         }
                     }

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
        $tahunId = $request->input('tahun_id'); 
        
        $listKelas = Kelas::all();
        $listTahun = Tahun::all(); 
        
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        $rekapData = collect([]);

        if ($kelasId && $tahunId) {
            $students = Siswa::where('kelas_id', $kelasId)->orderBy('nama')->get();
            
            $absensis = Absensi::whereHas('siswa', function($q) use($kelasId){
                    $q->where('kelas_id', $kelasId);
                })
                ->whereHas('logbook', function($q) use ($tahunId) {
                    $q->whereHas('jadwal', function($sq) use ($tahunId) {
                        $sq->where('tahun_id', $tahunId);
                    });
                })
                ->get();

             $rekapData = $students->map(function($student) use ($absensis) {
                 $studentRecords = $absensis->where('siswa_id', $student->id);
                 return (object) [
                     'nama' => $student->nama,
                     'nipd' => $student->nipd ?? '-',
                     'hadir' => $studentRecords->where('status', 'Hadir')->count(),
                     'sakit' => $studentRecords->where('status', 'Sakit')->count(),
                     'izin' => $studentRecords->where('status', 'Izin')->count(),
                     'alpha' => $studentRecords->where('status', 'Alpha')->count(),
                 ];
             });
        }

        return view('absensi.rekap_tahunan', compact(
            'kelasId', 'tahunId', 'listKelas', 'listTahun', 'selectedKelas', 'rekapData'
        ));
    }

    public function rekapHarian(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');
        
        $listKelas = Kelas::all();
        $query = Siswa::with(['kelas', 'absensis' => function($q) use ($date) {
            $q->whereHas('logbook', function($l) use ($date) {
                $l->where('tanggal', $date);
            });
        }]);

        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }

        $students = $query->orderBy('kelas_id')->orderBy('nama')->get();

        $rekapData = $students->map(function($student) {
            $absensis = $student->absensis;
            $status = null;
            if ($absensis->count() > 0) {
                if ($absensis->contains('status', 'Alpha')) $status = 'Alpha';
                elseif ($absensis->contains('status', 'Sakit')) $status = 'Sakit';
                elseif ($absensis->contains('status', 'Izin')) $status = 'Izin';
                else $status = 'Hadir';
            }
            return (object) [
                'nama' => $student->nama,
                'kelas' => $student->kelas->kelas ?? '-',
                'status' => $status,
                'sessions' => $absensis->count()
            ];
        });

        return view('absensi.rekap_harian', compact('date', 'kelasId', 'listKelas', 'rekapData'));
    }

    public function piket()
    {
        return view('absensi.piket');
    }

    public function piketCheckIn(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('absensi/piket', 'public');
            }

            Logbook::create([
                'kategori' => 'piket_masuk',
                'pegawai_id' => auth()->id(),
                'tanggal' => now()->toDateString(),
                'catatan' => 'Guru Piket Check-in',
                'foto' => $fotoPath ? json_encode([$fotoPath]) : null,
            ]);

            return redirect()->back()->with('type', 'success')->with('message', 'Berhasil Check-in Piket.');
        } catch (\Exception $e) {
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal Check-in: ' . $e->getMessage());
        }
    }

    public function piketCheckOut(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('absensi/piket', 'public');
            }

            Logbook::create([
                'kategori' => 'piket_pulang',
                'pegawai_id' => auth()->id(),
                'tanggal' => now()->toDateString(),
                'catatan' => 'Guru Piket Check-out',
                'foto' => $fotoPath ? json_encode([$fotoPath]) : null,
            ]);

            return redirect()->back()->with('type', 'success')->with('message', 'Berhasil Check-out Piket.');
        } catch (\Exception $e) {
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal Check-out: ' . $e->getMessage());
        }
    }
}
