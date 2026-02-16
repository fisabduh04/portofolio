<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Tahun;
use App\Models\Mapel;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalExport;
use App\Imports\JadwalImport;

class JadwalController extends Controller
{
    private function validateJadwal(Request $request)
    {
        return $request->validate([
            'tahun_id'   => 'required|exists:tahuns,id',
            'kelas_id'   => 'required|exists:kelas,id',
            'mapel_id'   => 'required|exists:mapels,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'hari'       => 'required|string',
            'jam'        => 'required|string',
            'mulai'      => 'required|date_format:H:i',
            'akhir'      => 'required|date_format:H:i|after:mulai',
            'ket'        => 'nullable|string',
        ]);
    }

    public function index(Request $request)
    {
        try {
            $sort = $request->input('sort');
            if (empty($sort)) {
                $sort = 'hari';
            }
            $direction = $request->input('direction');
            if (empty($direction) || !in_array(strtolower($direction), ['asc', 'desc'])) {
                $direction = 'asc'; // Default to asc for easier reading, or desc based on preference
            }
            $perpage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $filter_tahun = $request->input('filter_tahun', null);
            $filter_kelas = $request->input('filter_kelas', null);
            $filter_hari = $request->input('filter_hari', null);

            $tahun = Cache::remember('dropdown_tahun', 3600, function () {
                return Tahun::aktif()->select('id', 'tahun', 'semester')->get();
            });

            $kelas = Cache::remember('dropdown_kelas', 3600, function () {
                return Kelas::select('id', 'kelas')->get();
            });

            $mapel = Cache::remember('dropdown_mapel', 3600, function () {
                return Mapel::select('id', 'mapel')->get();
            });

            $pegawai = Cache::remember('dropdown_pegawai', 3600, function () {
                return Pegawai::select('id', 'name')->get();
            });
            $hari    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
            // --- 1. LOGIKA PENENTUAN TAHUN ID YANG BERLAKU ---
            
            $tahunIDUntukProses = $filter_tahun;

            // Jika tidak ada filter_tahun dari request, gunakan tahun aktif yang pertama sebagai default.
            if (empty($tahunIDUntukProses)) {
                // Karena $tahun sudah hanya berisi tahun aktif, kita ambil yang pertama.
                $tahunIDUntukProses = $tahun->first()->id ?? null;
                
                // Perbarui $filter_tahun untuk memastikan dropdown menampilkan default
                $filter_tahun = $tahunIDUntukProses;
            }


            $query = Jadwal::with(['kelas', 'mapel', 'pegawai']);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('kelas', fn($sub) => $sub->where('kelas', 'like', "%{$search}%"))
                      ->orWhereHas('mapel', fn($sub) => $sub->where('mapel', 'like', "%{$search}%"))
                      ->orWhereHas('pegawai', fn($sub) => $sub->where('name', 'like', "%{$search}%"));
                });
            }

            if (!empty($filter_tahun) && $filter_tahun !== 'all') {
                $query->where('tahun_id', $filter_tahun);
            }

            if (!empty($filter_kelas) && $filter_kelas !== 'all') {
                $query->where('kelas_id', $filter_kelas);
            }

            if (!empty($filter_hari) && $filter_hari !== 'all') {
                $query->where('hari', $filter_hari);
            }

            if ($sort === 'kelas') {
                $query->join('kelas', 'kelas.id', '=', 'jadwals.kelas_id')
                      ->orderBy('kelas.kelas', $direction)
                      ->select('jadwals.*');
            } elseif ($sort === 'pegawai') {
                $query->join('pegawais', 'pegawais.id', '=', 'jadwals.pegawai_id')
                      ->orderBy('pegawais.name', $direction)
                      ->select('jadwals.*');
            } elseif ($sort === 'mapel') {
                $query->join('mapels', 'mapels.id', '=', 'jadwals.mapel_id')
                      ->orderBy('mapels.mapel', $direction)
                      ->select('jadwals.*');
            } elseif ($sort === 'hari') {
                // Custom sort for Days of Week (Chronological)
                $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') $direction");
            } else {
                $query->orderBy($sort, $direction);
            }

            $jadwals = $query->paginate($perpage)->appends($request->query());

            // --- 2. DETEKSI BENTROKAN MASSAL (Hanya pada tahun yang sedang ditampilkan) ---
            $jadwalBentrokIds = [];
            $bentrokJadwalList = collect();
            $jadwalBentrokIds = [];
            $totalBentrok = 0;

            if (!empty($tahunIDUntukProses)) {
                // 1. Ambil SEMUA ID untuk highlight tabel (Query sangat ringan)
                $jadwalBentrokIds = Jadwal::bentrokSaatIni($tahunIDUntukProses)->pluck('id')->toArray();
                $totalBentrok = count($jadwalBentrokIds);

                // 2. Ambil Detail (Hanya untuk 10 data pertama agar Alert tidak berat)
                if ($totalBentrok > 0) {
                    // Ambil detail lengkap hanya untuk 10 ID pertama
                    $topIds = array_slice($jadwalBentrokIds, 0, 10);
                    $bentrokJadwalList = Jadwal::whereIn('id', $topIds)
                        ->with(['kelas', 'mapel', 'pegawai'])
                        ->get();
                }
            }

        // --------------------------------------------------

        return view('jadwal.index', compact(
            'tahun','kelas','filter_kelas','filter_tahun','filter_hari',
            'mapel','hari','pegawai','jadwals',
            'sort','direction','perpage','search',
            'jadwalBentrokIds', 
            'bentrokJadwalList',
            'totalBentrok'
        ));
        } catch (\Exception $e) {
            Log::error('Error loading jadwal index: ' . $e->getMessage());
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal memuat jadwal: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateJadwal($request);

            if (Jadwal::conflict($validated)->exists()) {
                return redirect()->back()
                    ->with('type', 'error')
                    ->with('message', 'Jadwal bentrok! Guru atau Kelas sudah memiliki jadwal lain pada waktu tersebut.');
            }

            Jadwal::create($validated);

            return redirect()->route('jadwal.index')
                ->with('type', 'success')
                ->with('message', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing jadwal: ' . $e->getMessage());
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $jadwal = Jadwal::findOrFail($id);
            $validated = $this->validateJadwal($request);

            if (Jadwal::conflict($validated, $jadwal->id)->exists()) {
                return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Jadwal bentrok! Guru atau Kelas sudah memiliki jadwal lain pada waktu tersebut.');
            }

            $jadwal->update($validated);

            return redirect()->route('jadwal.index', $request->query())
                ->with('type', 'success')
                ->with('message', 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating jadwal: ' . $e->getMessage());
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            return redirect()->route('jadwal.index', request()->query())
                ->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function getJadwalJson()
    {
        try {
            $jadwal = Jadwal::with(['mapel','kelas','pegawai'])->get();
            return response()->json(['data' => $jadwal], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jadwal JSON: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada ID yang dikirim');
        }

        try {
            Jadwal::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error bulk deleting jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function updateAll(Request $request)
{
    try {
        $ids         = $request->input('id', []);
        $kelas_ids   = $request->input('kelas_id', []);
        $haris       = $request->input('hari', []);
        $mapel_ids   = $request->input('mapel_id', []);
        $pegawai_ids = $request->input('pegawai_id', []);
        $jams        = $request->input('jam', []);
        $mulais      = $request->input('mulai', []);
        $akhirs      = $request->input('akhir', []);
        $kets        = $request->input('ket', []);

        $bentrokList = [];

        foreach ($ids as $index => $id) {
            $data = [
                'kelas_id'   => $kelas_ids[$index] ?? null,
                'hari'       => $haris[$index] ?? null,
                'mapel_id'   => $mapel_ids[$index] ?? null,
                'pegawai_id' => $pegawai_ids[$index] ?? null,
                'jam'        => $jams[$index] ?? null,
                'mulai'      => $mulais[$index] ?? null,
                'akhir'      => $akhirs[$index] ?? null,
                'ket'        => $kets[$index] ?? null,
                'tahun_id'   => $request->input('tahun_id'), // pastikan tahun_id tersedia
            ];

            // Validasi minimal data wajib
            if (!$data['kelas_id'] || !$data['mapel_id'] || !$data['pegawai_id'] || !$data['hari']) {
                Log::warning("Data tidak lengkap untuk index $index: " . json_encode($data));
                continue;
            }

            // Validasi bentrok jadwal
            if (Jadwal::conflict($data, $id)->exists()) {
                $bentrokList[] = $data;
                continue;
            }

            if (!empty($id)) {
                Jadwal::where('id', $id)->update($data);
            } else {
                Jadwal::create($data);
            }
        }

        if (count($bentrokList) > 0) {
            $pesanBentrok = "Beberapa jadwal gagal diperbarui karena bentrok:\n";

            foreach ($bentrokList as $jadwal) {
                // Ambil info nama agar lebih manusiawi
                $pName = Pegawai::find($jadwal['pegawai_id'])->name ?? 'N/A';
                $kName = Kelas::find($jadwal['kelas_id'])->kelas ?? 'N/A';
                $pesanBentrok .= "• $pName ($kName) pada {$jadwal['hari']} ({$jadwal['mulai']}–{$jadwal['akhir']})\n";
            }

            return redirect()->back()
                ->with('type', 'warning')
                ->with('message', $pesanBentrok);
        }

        return redirect()->back()
            ->with('type', 'success')
            ->with('message', 'Data jadwal berhasil diperbarui.');
    } catch (\Exception $e) {
        Log::error('Error bulk updating jadwal: ' . $e->getMessage());
        return redirect()->back()
            ->with('type', 'error')
            ->with('message', 'Gagal memperbarui data jadwal: ' . $e->getMessage());
    }
}

    public function export(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('type');

        if ($type === 'selected' && !empty($ids)) {
            // IDs come as "1,2,3" string from the hidden input
            return Excel::download(new JadwalExport($ids), 'jadwal_selected.xlsx');
        }

        // Export all (or filtered, if we passed filters to Export class, but for now typical implementation exports all or selection)
        // If we want to support filtered export, we'd accept filter params in JadwalExport.
        // For now, let's just export all since 'ids' will be empty if 'all' is selected usually.
        return Excel::download(new JadwalExport(null), 'jadwal_all.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        try {
            Excel::import(new JadwalImport, $request->file('file'));
            return redirect()->route('jadwal.index')->with('success', 'Data Jadwal berhasil diimport');
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('type', 'error')->with('message', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function rekap(Request $request)
    {
        $filter_tahun = $request->input('filter_tahun');
        $filter_kelas = $request->input('filter_kelas');
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search');

        $tahun = \App\Models\Tahun::aktif()->get();
        $kelas = \App\Models\Kelas::all();

        // 1. Determine active year (same logic as index)
        $tahunIDUntukProses = $filter_tahun;
        if (empty($tahunIDUntukProses)) {
            $firstActive = $tahun->first();
            $tahunIDUntukProses = $firstActive ? $firstActive->id : null;
            // Update filter_tahun for the view to show the default selected
            $filter_tahun = $tahunIDUntukProses;
        }

        // 2. Query Pegawai with Schedules
        $query = \App\Models\Pegawai::query();

        // Eager load jadwals with filters
        $query->with(['jadwals' => function ($q) use ($tahunIDUntukProses, $filter_kelas) {
            $q->where('tahun_id', $tahunIDUntukProses);
            if (!empty($filter_kelas)) {
                $q->where('kelas_id', $filter_kelas);
            }
            $q->with(['mapel', 'kelas']);
        }]);

        // 3. Apply Search on Pegawai
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // 4. Optimization: Calculate total hours directly in Database
        $query->withSum(['jadwals as total_menit' => function ($q) use ($tahunIDUntukProses, $filter_kelas) {
            $q->where('tahun_id', $tahunIDUntukProses);
            if (!empty($filter_kelas)) {
                $q->where('kelas_id', $filter_kelas);
            }
            // Logic: SUM(TIMESTAMPDIFF(MINUTE, mulai, akhir))
            $q->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, mulai, akhir))'));
        }], 'total_menit');
        
        $pegawais = $query->paginate($perpage)->appends($request->query());

        // 5. Build presentation details
        $pegawais->through(function ($guru) {
            $details = [];

            // We still loop through eager-loaded jadwals ONLY to build the detail strings
            foreach ($guru->jadwals as $jadwal) {
                $start = strtotime($jadwal->mulai);
                $end = strtotime($jadwal->akhir);
                
                if ($end > $start) {
                    $hours = ($end - $start) / 3600;
                    
                    $mapel = $jadwal->mapel->mapel ?? 'Unknown';
                    $kelas = $jadwal->kelas->kelas ?? 'Unknown';
                    $key = "{$mapel} - {$kelas}";

                    $details[$key] = ($details[$key] ?? 0) + $hours;
                }
            }

            // The total is now taken from database result (total_menit / 60)
            $guru->total_jam_mengajar = round(($guru->total_menit ?? 0) / 60, 1);
            
            foreach($details as $k => $v) {
                $details[$k] = round($v, 1);
            }
            $guru->detail_mengajar = $details;

            return $guru;
        });

        return view('jadwal.rekap', compact(
            'tahun',
            'kelas',
            'filter_tahun',
            'filter_kelas',
            'search',
            'perpage',
            'pegawais'
        ));
    }
    public function presensiHarianGuru(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        
        // 1. Tentukan Hari (Bahasa Indonesia)
        $dayName = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd');
        
        // 2. Ambil Tahun Aktif (Default logic similar to index)
        $activeYear = Cache::remember('active_year_default', 3600, function () {
            return Tahun::aktif()->first();
        });
        
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // 3. Query Jadwal
        $query = Jadwal::with(['kelas', 'mapel', 'pegawai'])
            ->where('tahun_id', $activeYear->id)
            ->where('hari', $dayName)
            ->orderBy('jam');

        // Filter Kelas Logic
        $filter_kelas = $request->input('filter_kelas');
        if($filter_kelas) {
            $query->where('kelas_id', $filter_kelas);
        }

        // 4. Role & Piket Check
        $user = auth()->user();
        $isPiket = false;
        $viewMode = $request->input('view_mode', 'all');

        if ($user->role === 'guru') {
            if (!$user->pegawai_id) {
                return redirect()->back()->with('error', 'Akun anda tidak terhubung dengan data pegawai.');
            }

            // Cek apakah guru ini sedang PIKET pada hari tersebut?
            $isPiket = \App\Models\JadwalPiket::where('pegawai_id', $user->pegawai_id)
                ->where('hari', $dayName)
                ->where('tahun_id', $activeYear->id)
                ->exists();

            // Jika BUKAN piket, maka hanya bisa lihat jadwal sendiri
            if (!$isPiket) {
                $query->where('pegawai_id', $user->pegawai_id);
                $viewMode = 'mine';
            } else {
                // Guru Piket
                if ($viewMode === 'mine') {
                   $query->where('pegawai_id', $user->pegawai_id);
                }
            }
        } else {
             // Admin/Operator/Kepala
             if ($viewMode === 'mine' && $user->pegawai_id) {
                 $query->where('pegawai_id', $user->pegawai_id);
             }
        }

        $jadwals = $query->get();

        // 5. Cek Status Presensi (Apakah sudah ada Logbook untuk jadwal ini di tanggal ini?)
        // Kita butuh Logbook model untuk cek
        $logbooks = \App\Models\Logbook::whereIn('jadwal_id', $jadwals->pluck('id'))
            ->where('tanggal', $date)
            ->get()
            ->keyBy('jadwal_id');

        // Tambahkan properti status ke collection jadwal
        $jadwals->transform(function ($jadwal) use ($logbooks) {
            $jadwal->logbook = $logbooks->get($jadwal->id);
            $jadwal->status_presensi = $jadwal->logbook ? 'sudah' : 'belum';
            return $jadwal;
        });

        // Dropdown Data
        $kelas = \App\Models\Kelas::orderBy('kelas')->get();

        return view('jadwal.presensiHarianGuru', compact('jadwals', 'date', 'dayName', 'activeYear', 'isPiket', 'viewMode', 'kelas', 'filter_kelas'));
    }

}
