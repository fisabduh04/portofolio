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
use App\Models\Tahun;
use App\Models\Pegawai;
use App\Models\Mapel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Exports\RekapHarianExport;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar sumber daya.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('absensi.index', compact('users'));       
    }

    /**
     * Menampilkan formulir untuk membuat sumber daya baru.
     */
    public function create(Request $request)
    {
        $jadwalId = $request->query('jadwal_id');
        $mode = $request->query('mode', 'mapel'); // 'mapel' atau 'piket'
        $date = $request->query('date', now()->toDateString()); // Default date: Today
        
        if ($mode === 'piket' && !auth()->user()->isPiketToday()) {
             return redirect()->back()->with('error', 'Akses ditolak. Anda bukan guru piket hari ini.');
        }
        
        if (!$jadwalId) {
            return redirect()->back()->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $jadwal = Jadwal::with(['kelas', 'mapel', 'pegawai'])->findOrFail($jadwalId);
        
        // Tentukan kategori berdasarkan mode
        // Guru Mapel Normal -> 'mapel'
        // Guru Piket (Pengganti) -> 'piket_sub'
        $kategori = ($mode === 'piket') ? 'piket_sub' : 'mapel';

        // Cek apakah sudah ada logbook untuk kategori ini
        $existingLogbook = Logbook::with(['absensis', 'jadwal'])
            ->where('jadwal_id', $jadwalId)
            ->where('tanggal', $date)
            ->where('kategori', $kategori) 
            ->first();

        // Ambil daftar siswa berdasarkan kelas dan tahun ajaran dari jadwal
        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($jadwal) {
            $q->where('kelas_id', $jadwal->kelas_id)
              ->where('tahun_id', $jadwal->tahun_id);
        })->orderBy('nama')->get();

        return view('absensi.input', compact('jadwal', 'students', 'existingLogbook', 'mode', 'kategori', 'date'));
    }

    /**
     * Menyimpan sumber daya baru ke dalam penyimpanan.
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
            'kategori' => 'required|in:mapel,piket_sub', // Validasi kategori
            'tanggal' => 'required|date',
        ]);

        $user = auth()->user();
        
        // --- AUTO-FIX: Coba hubungkan akun Admin/Operator ke Pegawai jika belum ada ---
        if (!$user->pegawai_id && in_array($user->role, ['admin', 'operator', 'kepala'])) {
            $matchingPegawai = Pegawai::where('email', $user->email) // Prioritas 1: Email sama
                ->orWhere('name', 'LIKE', $user->name)               // Prioritas 2: Nama mirip
                ->first();
                
            if ($matchingPegawai) {
                $user->update(['pegawai_id' => $matchingPegawai->id]);
                $user->refresh(); // Refresh model agar pegawai_id terisi
                // Lanjut proses di bawah...
            }
        }
        
        if (!$user->pegawai_id) {
            return redirect()->back()->with('type', 'error')->with('message', 'Akun anda tidak terhubung dengan data pegawai. Mohon hubungkan akun Anda dengan data pegawai di menu Manajemen Akun atau hubungi Developer.');
        }

        try {
            DB::beginTransaction();

            $jadwal = Jadwal::findOrFail($request->jadwal_id);
            $date = $request->input('tanggal', now()->toDateString());

            // Cek Logbook yang sudah ada (Mode Edit/Update) berdasarkan Kategori dan Tanggal
            $logbook = Logbook::where('jadwal_id', $jadwal->id)
                ->where('tanggal', $date)
                ->where('kategori', $request->kategori)
                ->first();

            // 1. Tangani Unggah Foto
            $photos = [];
            // Jika update, ambil foto lama terlebih dahulu
            if ($logbook && $logbook->foto) {
                $photos = json_decode($logbook->foto, true) ?? [];
            }

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $photos[] = $file->store('absensi/foto', 'public');
                }
            }

            // 2. Buat atau Perbarui Logbook
            if ($logbook) {
                // PERBARUI
                $logbook->update([
                    'materi' => $request->materi,
                    'catatan' => $request->catatan,
                    'foto' => json_encode($photos),
                    'pegawai_id' => $user->pegawai_id, // Perbarui ke pelaksana saat ini
                ]);
            } else {
                // BUAT
                $logbook = Logbook::create([
                    'kategori' => $request->kategori, // Simpan kategori spesifik
                    'jadwal_id' => $jadwal->id,
                    'kelas_id' => $jadwal->kelas_id,
                    'pegawai_id' => $user->pegawai_id, 
                    'tanggal' => $date,
                    'materi' => $request->materi,
                    'catatan' => $request->catatan,
                    'foto' => json_encode($photos),
                ]);
            }

            // 3. Perbarui/Buat Entri Presensi Siswa
            foreach ($request->attendance as $siswaId => $data) {
                Absensi::updateOrCreate(
                    [
                        'logbook_id' => $logbook->id,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }

            DB::commit();

            $redirectTo = $request->input('redirect_to', route('jadwal.index'));

            return redirect($redirectTo)
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

    // --- PRESENSI HARIAN (PIKET) ---
    public function createHarian(Request $request)
    {
        if (!auth()->user()->isPiketToday()) abort(403, 'Akses ditolak. Anda bukan guru piket hari ini.');

        $kelasId = $request->query('kelas_id');
        $type = $request->query('type', 'masuk'); // masuk atau pulang
        
        if (!$kelasId) {
            return redirect()->back()->with('error', 'Pilih kelas terlebih dahulu.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        
        // Cari jadwal untuk dikaitkan (Syarat: jadwal_id tidak boleh null)
        // Kita ambil jadwal pertama hari ini untuk kelas tersebut
        $jadwal = Jadwal::where('kelas_id', $kelasId)
            ->where('hari', now()->locale('id')->isoFormat('dddd'))
            ->orderBy('mulai')
            ->first();

        // Jika tidak ada jadwal hari ini, kita tidak bisa membuat entri logbook tanpa modifikasi DB
        if (!$jadwal) {
             return redirect()->back()->with('error', 'Tidak ada jadwal pelajaran untuk kelas ini hari ini (' . now()->locale('id')->isoFormat('dddd') . '). Absensi harian membutuhkan minimal 1 jadwal aktif.');
        }

        // Kategori: piket_masuk / piket_pulang
        $kategori = 'piket_' . $type;

        // Cek logbook harian
        $existingLogbook = Logbook::with(['absensis'])
            ->where('kelas_id', $kelasId)
            ->where('tanggal', now()->toDateString())
            ->where('kategori', $kategori)
            ->first();

        // Ambil Siswa Aktif di Kelas
        $tahunAktif = Tahun::aktif()->first();
        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)
              ->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        return view('absensi.harian.create', compact('kelas', 'students', 'existingLogbook', 'type', 'kategori', 'jadwal'));
    }

    public function storeHarian(Request $request)
    {
        if (!auth()->user()->isPiketToday()) abort(403, 'Akses ditolak.');

         $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kategori' => 'required|in:piket_masuk,piket_pulang',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'catatan' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user->pegawai_id) {
            return redirect()->back()->with('type', 'error')->with('message', 'Akun anda tidak terhubung dengan data pegawai.');
        }

        try {
            DB::beginTransaction();

             // Cari jadwal untuk dikaitkan
            $jadwal = Jadwal::where('kelas_id', $request->kelas_id)
                ->where('hari', now()->locale('id')->isoFormat('dddd'))
                ->orderBy('mulai')
                ->first();

            if (!$jadwal) {
                 throw new \Exception('Tidak ada jadwal hari ini untuk dikaitkan.');
            }

            // Cek Logbook
            $logbook = Logbook::with('absensis')
                ->where('kelas_id', $request->kelas_id)
                ->where('tanggal', now()->toDateString())
                ->where('kategori', $request->kategori)
                ->first();

            // Tangani Unggah Foto
            $photos = [];
            if ($logbook && $logbook->foto) {
                $photos = json_decode($logbook->foto, true) ?? [];
            }

            if ($request->hasFile('foto')) {
                foreach ($request->file('foto') as $file) {
                    $photos[] = $file->store('absensi/foto_harian', 'public');
                }
            }

            if ($logbook) {
                // Perbarui
                $logbook->update([
                    'catatan' => $request->catatan,
                    'foto' => json_encode($photos),
                    'pegawai_id' => $user->pegawai_id,
                ]);
            } else {
                // Buat
                $logbook = Logbook::create([
                    'kategori' => $request->kategori,
                    'jadwal_id' => $jadwal->id,
                    'kelas_id' => $request->kelas_id,
                    'pegawai_id' => $user->pegawai_id,
                    'tanggal' => now()->toDateString(),
                    'materi' => ($request->kategori == 'piket_masuk' ? 'Absensi Masuk' : 'Absensi Pulang'),
                    'catatan' => $request->catatan,
                    'foto' => json_encode($photos),
                ]);
            }

            foreach ($request->attendance as $siswaId => $data) {
                Absensi::updateOrCreate(
                    [
                        'logbook_id' => $logbook->id,
                        'siswa_id' => $siswaId,
                    ],
                    [
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('absensi.harian.index')
                ->with('type', 'success')
                ->with('message', 'Presensi Harian Berhasil Disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Gagal: ' . $e->getMessage());
        }
    }

    public function indexHarian()
    {
        if (!auth()->user()->isPiketToday()) abort(403, 'Akses ditolak.');

        $kelas = Kelas::orderBy('kelas')->get();
        return view('absensi.harian.index', compact('kelas'));
    }

    /**
     * Menampilkan sumber daya yang ditentukan.
     */
    public function show(Absensi $absensi)
    {
        //
    }

    /**
     * Menampilkan formulir untuk mengedit sumber daya yang ditentukan.
     */
    public function edit(Absensi $absensi)
    {
        //
    }

    /**
     * Memperbarui sumber daya yang ditentukan di penyimpanan.
     */
    public function update(Request $request, Absensi $absensi)
    {
        //
    }

    /**
     * Menghapus sumber daya yang ditentukan dari penyimpanan.
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
            $summaryStats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Total' => 0]; // Reset for recalculation
             foreach ($rekapData as $student) {
                if ($student->daily_status != '-') {
                     $summaryStats[$student->daily_status]++;
                }
                $summaryStats['Total']++;
            }
        }

        return view('absensi.rekap_harian', compact('listKelas', 'listPegawai', 'rekapData', 'summaryStats', 'date', 'kelasId', 'pegawaiId', 'typeGuru', 'selectedKelas', 'viewMode'));
    }

    public function exportRekapHarian(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');
        $pegawaiId = $request->input('pegawai_id');
        $typeGuru = $request->input('type_guru');
        $format = $request->input('format', 'excel');

        if (!$kelasId) {
            return back()->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        $data = $this->getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru);
        $rekapData = $data['rekapData'];
        $kelas = Kelas::find($kelasId);
        $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

        if ($format === 'pdf') {
            return view('absensi.print_rekap_harian', [
                'rekapData' => $rekapData,
                'date' => $date,
                'kelas' => $kelasName
            ]);
        }

        return Excel::download(new RekapHarianExport($rekapData, $date, $kelasName), 'rekap_harian_' . $date . '.xlsx');
    }

    private function getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru)
    {
        $rekapData = collect([]);
        $summaryStats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Total' => 0];

        if ($kelasId) {
            $tahunAktif = Tahun::aktif()->first();
            
            // 1. Ambil Siswa
            $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })->orderBy('nama')->get();

            // 2. Ambil Absensi
            $absensis = Absensi::with(['logbook.jadwal.mapel', 'logbook.jadwal.pegawai', 'logbook.pegawai'])
                ->whereHas('siswa', function($q) use ($kelasId, $tahunAktif) {
                        $q->whereHas('KelasSiswa', function($sq) use ($kelasId, $tahunAktif) {
                            $sq->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
                        });
                })
                ->whereHas('logbook', function($q) use ($date, $pegawaiId, $typeGuru) {
                    $q->where('tanggal', $date);
                    
                    // Filter Pegawai Spesifik
                    if ($pegawaiId) {
                        $q->where('pegawai_id', $pegawaiId);
                    }

                    // Filter Tipe Guru (Mapel vs Piket)
                    if ($typeGuru === 'mapel') {
                        // Kategori 'mapel' atau 'piket_sub' (karena piket_sub adalah guru pengganti mapel)
                        $q->whereIn('kategori', ['mapel', 'piket_sub']);
                    } elseif ($typeGuru === 'piket') {
                        // Piket Harian Murni
                        $q->whereIn('kategori', ['piket_masuk', 'piket_pulang']);
                    }
                })
                ->get();

            // 3. Proses Data
            $rekapData = $students->map(function($student) use ($absensis) {
                $studentLogs = $absensis->where('siswa_id', $student->id);
                
                $stats = [
                    'Hadir' => $studentLogs->where('status', 'Hadir')->count(),
                    'Sakit' => $studentLogs->where('status', 'Sakit')->count(),
                    'Izin' =>  $studentLogs->where('status', 'Izin')->count(),
                    'Alpha' => $studentLogs->where('status', 'Alpha')->count(),
                ];

                $details = $studentLogs->map(function($log) {
                    $kategori = $log->logbook->kategori;
                    $mapel = '-';
                    $guru = $log->logbook->pegawai->name ?? '-';
                    $isPiketSub = false;

                    if ($kategori == 'mapel') {
                        $mapel = $log->logbook->jadwal->mapel->mapel ?? 'Mapel';
                    } elseif ($kategori == 'piket_sub') {
                        $mapel = ($log->logbook->jadwal->mapel->mapel ?? 'Mapel') . ' (Guru Pengganti)';
                        $isPiketSub = true;
                    } elseif ($kategori == 'piket_masuk') {
                        $mapel = 'Piket Masuk';
                    } elseif ($kategori == 'piket_pulang') {
                        $mapel = 'Piket Pulang';
                    }

                    return (object) [
                        'jam_ke' => $log->logbook->jadwal->mulai ?? '-',
                        'mapel' => $mapel,
                        'guru' => $guru,
                        'status' => $log->status,
                        'catatan' => $log->logbook->catatan,
                        'is_piket_sub' => $isPiketSub
                    ];
                })->sortBy('jam_ke');

                // Logic Verdict Strict
                $dailyStatus = 'Hadir'; 
                
                if ($stats['Alpha'] > 0) {
                    $dailyStatus = 'Alpha';
                } elseif ($stats['Sakit'] > 0) {
                    $dailyStatus = 'Sakit';
                } elseif ($stats['Izin'] > 0) {
                    $dailyStatus = 'Izin';
                } elseif ($studentLogs->count() == 0) {
                    $dailyStatus = '-';
                }

                // Collect subjects for non-present statuses
                $statDetails = [
                    'Alpha' => $details->where('status', 'Alpha')->pluck('mapel')->unique()->values()->toArray(),
                    'Sakit' => $details->where('status', 'Sakit')->pluck('mapel')->unique()->values()->toArray(),
                    'Izin' => $details->where('status', 'Izin')->pluck('mapel')->unique()->values()->toArray(),
                ];

                return (object) [
                    'id' => $student->id,
                    'nama' => $student->nama,
                    'daily_status' => $dailyStatus,
                    'stats' => $stats,
                    'stat_details' => $statDetails,
                    'details' => $details
                ];
            });
        }
        
        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats];
    }

    public function piket()
    {
        if (!auth()->user()->isPiketToday()) {
             return redirect()->route('dashboard.index')->with('error', 'Akses ditolak. Anda bukan guru piket hari ini.');
        }
        return view('absensi.piket');
    }

    public function piketCheckIn(Request $request)
    {
        if (!auth()->user()->isPiketToday()) abort(403);

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
        if (!auth()->user()->isPiketToday()) abort(403);

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

    public function exportHarian(Request $request)
    {
        return Excel::download(new \App\Exports\AbsensiExport($request->all(), 'harian'), 'rekap_harian_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportBulanan(Request $request)
    {
        return Excel::download(new \App\Exports\AbsensiExport($request->all(), 'bulanan'), 'rekap_bulanan_' . now()->format('Ymd') . '.xlsx');
    }

    public function rekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $viewMode = $request->input('view_mode', 'sederhana'); // Default simple

        $listKelas = Kelas::all();
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $years = range(date('Y'), 2020);
        
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;
        
        $data = ['rekapData' => collect([]), 'summaryStats' => ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0], 'dates' => []];

        if ($kelasId) {
             $data = $this->getPrivateRekapBulananData($month, $year, $kelasId);
        }

        $rekapData = $data['rekapData'];
        $summaryStats = $data['summaryStats'];
        $dates = $data['dates'];

        return view('absensi.rekap_bulanan', compact(
            'kelasId', 'month', 'year', 'viewMode', 'listKelas', 'months', 'years', 'selectedKelas', 'rekapData', 'summaryStats', 'dates'
        ));
    }

    public function rekapTahunan(Request $request) 
    {
        $kelasId = $request->input('kelas_id');
        $year = $request->input('year', now()->year);
        
        $listKelas = Kelas::orderBy('kelas')->get();
        $years = range(now()->year, now()->year - 5);
        $months = range(1, 12);
        
        $rekapData = collect([]);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];
        $selectedKelas = null;

        if ($kelasId) {
             $data = $this->getPrivateRekapTahunanData($year, $kelasId);
             $rekapData = $data['rekapData'];
             $summaryStats = $data['summaryStats'];
             $selectedKelas = Kelas::find($kelasId);
        }

        return view('absensi.rekap_tahunan', compact('rekapData', 'summaryStats', 'listKelas', 'years', 'year', 'kelasId', 'months', 'selectedKelas'));
    }

    public function exportRekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $format = $request->input('format', 'excel');

        if (!$kelasId) return back()->with('error', 'Pilih kelas terlebih dahulu.');

        $data = $this->getPrivateRekapBulananData($month, $year, $kelasId);
        $kelas = Kelas::find($kelasId);
        $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

        if ($format === 'pdf') {
             return view('absensi.print_rekap_bulanan', [
                'rekapData' => $data['rekapData'],
                'dates' => $data['dates'],
                'month' => $month,
                'year' => $year,
                'kelas' => $kelasName
            ]);
        }
        
        return Excel::download(new \App\Exports\RekapBulananExport($data['rekapData'], $data['dates'], $month, $year, $kelasName), 'rekap_bulanan_' . $month . '_' . $year . '.xlsx');
    }

    public function exportRekapTahunan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $year = $request->input('year', now()->year);
        $format = $request->input('format', 'excel');

        if (!$kelasId) return back()->with('error', 'Pilih kelas terlebih dahulu.');

         $data = $this->getPrivateRekapTahunanData($year, $kelasId);
         $kelas = Kelas::find($kelasId);
         $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

         if ($format === 'pdf') {
             return view('absensi.print_rekap_tahunan', [
                'rekapData' => $data['rekapData'],
                'year' => $year,
                'kelas' => $kelasName
            ]);
        }

        return Excel::download(new \App\Exports\RekapTahunanExport($data['rekapData'], $year, $kelasName), 'rekap_tahunan_' . $year . '.xlsx');
    }

    private function getPrivateRekapBulananData($month, $year, $kelasId)
    {
        $tahunAktif = Tahun::aktif()->first();
        $daysInMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dates = range(1, $daysInMonth);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];

        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        $summaryStats['Total'] = $students->count();

        // Corrected Query: Filter via Logbook relationship
        $absensis = Absensi::with('logbook')
            ->whereHas('logbook', function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            })
            ->whereHas('siswa.KelasSiswa', function($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })
            ->get()
            ->groupBy('siswa_id');

        $rekapData = $students->map(function($student) use ($absensis, $dates, $year, $month, &$summaryStats) {
                 $studentLogs = $absensis->get($student->id, collect([]));
                 $dailyStatuses = [];
                 $studentStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

                 foreach ($dates as $day) {
                     $dateStr = \Carbon\Carbon::createFromDate($year, $month, $day)->toDateString();
                     
                     // Corrected Filter: Check Logbook Date
                     $logsForDay = $studentLogs->filter(function($log) use ($dateStr) {
                        return $log->logbook && $log->logbook->tanggal === $dateStr;
                     });

                     $status = '-';
                     if ($logsForDay->isNotEmpty()) {
                         if ($logsForDay->contains('status', 'Alpha')) $status = 'Alpha';
                         elseif ($logsForDay->contains('status', 'Sakit')) $status = 'Sakit';
                         elseif ($logsForDay->contains('status', 'Izin')) $status = 'Izin';
                         elseif ($logsForDay->contains('status', 'Hadir')) $status = 'Hadir';
                     }
                     
                     $code = '-';
                     if ($status == 'Hadir') { $code = 'H'; $studentStats['H']++; $summaryStats['Hadir']++; }
                     elseif ($status == 'Sakit') { $code = 'S'; $studentStats['S']++; $summaryStats['Sakit']++; }
                     elseif ($status == 'Izin') { $code = 'I'; $studentStats['I']++; $summaryStats['Izin']++; }
                     elseif ($status == 'Alpha') { $code = 'A'; $studentStats['A']++; $summaryStats['Alpha']++; }

                     $dailyStatuses[$day] = $code;
                 }
                 return (object) [
                     'id' => $student->id, 'nama' => $student->nama,
                     'statuses' => $dailyStatuses, 'stats' => $studentStats
                 ];
        });

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats, 'dates' => $dates];
    }

    private function getPrivateRekapTahunanData($year, $kelasId)
    {
        $tahunAktif = Tahun::aktif()->first();
        $months = range(1, 12);
        $summaryStats = ['Total' => 0, 'Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];

        $students = Siswa::whereHas('KelasSiswa', function ($q) use ($kelasId, $tahunAktif) {
            $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
        })->orderBy('nama')->get();

        $summaryStats['Total'] = $students->count();

        // Corrected Query: Filter via Logbook relationship
        $absensis = Absensi::with('logbook')
            ->whereHas('logbook', function($q) use ($year) {
                $q->whereYear('tanggal', $year);
            })
            ->whereHas('siswa.KelasSiswa', function($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)->where('tahun_id', $tahunAktif->id);
            })
            ->get()
            ->groupBy('siswa_id');

        $rekapData = $students->map(function($student) use ($absensis, $months, &$summaryStats) {
            $studentLogs = $absensis->get($student->id, collect([]));
            $monthlyStats = [];
            $totalStats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

            foreach ($months as $m) {
                // Corrected Filter: Check Logbook Month
                $logsMonth = $studentLogs->filter(function($log) use ($m) {
                    return $log->logbook && \Carbon\Carbon::parse($log->logbook->tanggal)->month == $m;
                });
                $stats = [
                    'H' => $logsMonth->where('status', 'Hadir')->count(),
                    'S' => $logsMonth->where('status', 'Sakit')->count(),
                    'I' => $logsMonth->where('status', 'Izin')->count(),
                    'A' => $logsMonth->where('status', 'Alpha')->count(),
                ];
                $monthlyStats[$m] = $stats;
                $totalStats['H'] += $stats['H']; $summaryStats['Hadir'] += $stats['H'];
                $totalStats['S'] += $stats['S']; $summaryStats['Sakit'] += $stats['S'];
                $totalStats['I'] += $stats['I']; $summaryStats['Izin'] += $stats['I'];
                $totalStats['A'] += $stats['A']; $summaryStats['Alpha'] += $stats['A'];
            }
            return (object) [
                'id' => $student->id, 'nama' => $student->nama,
                'months' => $monthlyStats, 'total' => $totalStats
            ];
        });

        return ['rekapData' => $rekapData, 'summaryStats' => $summaryStats];
    }
}
