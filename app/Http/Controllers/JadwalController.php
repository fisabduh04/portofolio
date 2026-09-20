<?php

namespace App\Http\Controllers;

use App\Exports\JadwalExport;
use App\Imports\JadwalImport;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class JadwalController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'tahun_id.required' => 'Pilih tahun ajaran terlebih dahulu.',
        'tahun_id.exists' => 'Tahun ajaran yang dipilih tidak tersedia. Silakan pilih kembali.',
        'kelas_id.required' => 'Pilih kelas terlebih dahulu.',
        'kelas_id.exists' => 'Kelas yang dipilih tidak tersedia. Silakan pilih kembali.',
        'mapel_id.required' => 'Pilih mata pelajaran terlebih dahulu.',
        'mapel_id.exists' => 'Mata pelajaran yang dipilih tidak tersedia. Silakan pilih kembali.',
        'pegawai_id.required' => 'Pilih guru terlebih dahulu.',
        'pegawai_id.exists' => 'Guru yang dipilih tidak tersedia. Silakan pilih kembali.',
        'hari.required' => 'Pilih hari terlebih dahulu.',
        'hari.string' => 'Isian hari tidak valid. Silakan pilih kembali.',
        'jam.required' => 'Isi jam pelajaran ke berapa.',
        'jam.string' => 'Isian jam pelajaran tidak valid. Silakan isi kembali.',
        'mulai.required' => 'Isi jam mulai terlebih dahulu.',
        'mulai.date_format' => 'Format jam mulai tidak valid. Gunakan format 24 jam, misalnya 07:30.',
        'akhir.required' => 'Isi jam selesai terlebih dahulu.',
        'akhir.date_format' => 'Format jam selesai tidak valid. Gunakan format 24 jam, misalnya 08:30.',
        'akhir.after' => 'Jam selesai harus lebih dari jam mulai.',
        'ket.string' => 'Keterangan harus berupa teks.',
        'id.required' => 'Tambahkan setidaknya satu baris jadwal sebelum menyimpan.',
        'id.array' => 'Format baris jadwal tidak valid. Silakan buka kembali halaman jadwal.',
        'id.min' => 'Tambahkan setidaknya satu baris jadwal sebelum menyimpan.',
        'id.*.integer' => 'Identitas salah satu baris jadwal tidak valid. Silakan buka kembali halaman jadwal.',
        'id.*.exists' => 'Salah satu jadwal sudah tidak tersedia. Silakan buka kembali halaman jadwal.',
        'file.required' => 'Pilih berkas jadwal yang akan diimpor terlebih dahulu.',
        'file.mimes' => 'Berkas impor harus berformat CSV, XLS, atau XLSX.',
        'file.uploaded' => 'Berkas gagal diunggah. Periksa ukuran berkas dan koneksi Anda, lalu coba lagi.',
    ];

    /** @return array<string, string> */
    private function jadwalRules(): array
    {
        return [
            'tahun_id' => 'required|exists:tahuns,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'pegawai_id' => 'required|exists:pegawais,id',
            'hari' => 'required|string',
            'jam' => 'required|string',
            'mulai' => 'required|date_format:H:i',
            'akhir' => 'required|date_format:H:i|after:mulai',
            'ket' => 'nullable|string',
        ];
    }

    public function index(Request $request)
    {
        try {
            $sort = $request->input('sort');
            if (empty($sort)) {
                $sort = 'hari';
            }
            $direction = $request->input('direction');
            if (empty($direction) || ! in_array(strtolower($direction), ['asc', 'desc'])) {
                $direction = 'asc'; // Default to asc for easier reading, or desc based on preference
            }
            $perpage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $filter_tahun = $request->input('filter_tahun', null);
            $filter_kelas = $request->input('filter_kelas', null);
            $filter_hari = $request->input('filter_hari', null);

            $tahun = Tahun::aktif()->select('id', 'tahun', 'semester')
                ->orderByDesc('tanggalmulai')->orderByDesc('id')->get();

            $kelas = Kelas::select('id', 'kelas')->get();

            $mapel = Mapel::select('id', 'mapel')->get();

            $pegawai = Pegawai::select('id', 'name')->get();
            $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            // --- 1. LOGIKA PENENTUAN TAHUN ID YANG BERLAKU ---

            $tahunIdsUntukProses = $tahun->pluck('id');
            if (! empty($filter_tahun) && $filter_tahun !== 'all') {
                $tahunIdsUntukProses = $tahunIdsUntukProses->filter(
                    fn ($id) => (string) $id === (string) $filter_tahun
                );
            }

            $query = Jadwal::with(['kelas', 'mapel', 'pegawai'])
                ->whereIn('jadwals.tahun_id', $tahunIdsUntukProses);

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('kelas', fn ($sub) => $sub->where('kelas', 'like', "%{$search}%"))
                        ->orWhereHas('mapel', fn ($sub) => $sub->where('mapel', 'like', "%{$search}%"))
                        ->orWhereHas('pegawai', fn ($sub) => $sub->where('name', 'like', "%{$search}%"));
                });
            }

            if (! empty($filter_kelas) && $filter_kelas !== 'all') {
                $query->where('kelas_id', $filter_kelas);
            }

            if (! empty($filter_hari) && $filter_hari !== 'all') {
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

            if ($tahunIdsUntukProses->isNotEmpty()) {
                // 1. Ambil SEMUA ID untuk highlight tabel (Query sangat ringan)
                $jadwalBentrokIds = Jadwal::whereIn('tahun_id', $tahunIdsUntukProses)
                    ->bentrokSaatIni()->pluck('id')->toArray();
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
                'tahun', 'kelas', 'filter_kelas', 'filter_tahun', 'filter_hari',
                'mapel', 'hari', 'pegawai', 'jadwals',
                'sort', 'direction', 'perpage', 'search',
                'jadwalBentrokIds',
                'bentrokJadwalList',
                'totalBentrok'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading jadwal index: '.$e->getMessage());

            return redirect()->back()->with('type', 'error')->with('message', 'Jadwal belum dapat dimuat. Silakan coba lagi. Jika masalah berlanjut, hubungi operator.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->jadwalRules(), self::VALIDATION_MESSAGES);
        try {

            if (Jadwal::conflict($validated)->exists()) {
                return redirect()->back()->withInput()
                    ->with('type', 'error')
                    ->with('message', 'Jadwal bentrok! Guru atau Kelas sudah memiliki jadwal lain pada waktu tersebut.');
            }

            Jadwal::create($validated);

            return redirect()->back(fallback: route('jadwal.index'))
                ->with('type', 'success')
                ->with('message', 'Jadwal berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error storing jadwal: '.$e->getMessage());

            return redirect()->back()->withInput()->with('type', 'error')->with('message', 'Jadwal belum dapat ditambahkan. Isian Anda tetap tersedia. Silakan coba lagi atau hubungi operator.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge(['_jadwal_edit_id' => $id]);
        $validated = $request->validate($this->jadwalRules(), self::VALIDATION_MESSAGES);
        try {
            $jadwal = Jadwal::findOrFail($id);

            if (Jadwal::conflict($validated, $jadwal->id)->exists()) {
                return redirect()->back()->withInput()
                    ->with('type', 'error')
                    ->with('message', 'Jadwal bentrok! Guru atau Kelas sudah memiliki jadwal lain pada waktu tersebut.');
            }

            $jadwal->update($validated);

            return redirect()->back(fallback: route('jadwal.index', $request->query()))
                ->with('type', 'success')
                ->with('message', 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating jadwal: '.$e->getMessage());

            return redirect()->back()->withInput()->with('type', 'error')->with('message', 'Perubahan jadwal belum dapat disimpan. Isian Anda tetap tersedia. Silakan coba lagi atau hubungi operator.');
        }
    }

    public function destroy($id)
    {
        try {
            $jadwal = Jadwal::findOrFail($id);
            $jadwal->delete();

            return redirect()->back(fallback: route('jadwal.index', request()->query()))
                ->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting jadwal: '.$e->getMessage());

            return redirect()->back()->with('error', 'Jadwal belum dapat dihapus. Silakan coba lagi atau hubungi operator.');
        }
    }

    public function getJadwalJson()
    {
        try {
            $jadwal = Jadwal::with(['mapel', 'kelas', 'pegawai'])->get();

            return response()->json(['data' => $jadwal], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jadwal JSON: '.$e->getMessage());

            return response()->json(['error' => 'Data jadwal belum dapat dimuat. Silakan coba lagi atau hubungi operator.'], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih setidaknya satu jadwal yang akan dihapus.');
        }

        try {
            Jadwal::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error bulk deleting jadwal: '.$e->getMessage());

            return redirect()->back()->with('error', 'Jadwal belum dapat dihapus. Silakan coba lagi atau hubungi operator.');
        }
    }

    public function updateAll(Request $request)
    {
        $request->merge(['_jadwal_bulk' => true]);
        $request->validate([
            'id' => 'required|array|min:1',
            'id.*' => 'nullable|integer|exists:jadwals,id',
            'tahun_id' => 'required|exists:tahuns,id',
        ], self::VALIDATION_MESSAGES);

        try {
            DB::transaction(function () use ($request): void {
                foreach ($request->input('id') as $index => $id) {
                    $data = ['tahun_id' => $request->input('tahun_id')];
                    foreach (['kelas_id', 'hari', 'mapel_id', 'pegawai_id', 'jam', 'mulai', 'akhir', 'ket'] as $field) {
                        $data[$field] = $request->input($field.'.'.$index);
                    }

                    $validator = Validator::make($data, $this->jadwalRules(), self::VALIDATION_MESSAGES);
                    if ($validator->fails()) {
                        $errors = [];
                        foreach ($validator->errors()->messages() as $field => $messages) {
                            $errors[$field.'.'.$index] = $messages;
                        }
                        throw ValidationException::withMessages($errors);
                    }

                    if (Jadwal::conflict($data, $id)->exists()) {
                        throw ValidationException::withMessages([
                            'id.'.$index => 'Baris '.($index + 1).': guru atau kelas bentrok pada waktu tersebut. Belum ada baris yang disimpan.',
                        ]);
                    }

                    if ($id) {
                        Jadwal::findOrFail($id)->update($data);
                    } else {
                        Jadwal::create($data);
                    }
                }
            });

            return redirect()->back()
                ->with('type', 'success')
                ->with('message', 'Data jadwal berhasil diperbarui.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error bulk updating jadwal: '.$e->getMessage());

            return redirect()->back()->withInput()
                ->with('type', 'error')
                ->with('message', 'Data jadwal belum dapat disimpan. Isian Anda tetap tersedia. Silakan coba lagi atau hubungi operator.');
        }
    }

    public function export(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('type');

        if ($type === 'selected' && ! empty($ids)) {
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
            'file' => 'required|mimes:csv,xls,xlsx',
        ], self::VALIDATION_MESSAGES);

        try {
            $import = new JadwalImport;
            DB::transaction(fn () => Excel::import($import, $request->file('file')));

            return redirect()->back(fallback: route('jadwal.index'))->with('success', "Impor selesai: {$import->created} jadwal ditambahkan, {$import->updated} jadwal diperbarui.");
        } catch (ValidationException $e) {
            return redirect()->back(fallback: route('jadwal.index'))->withErrors($e->errors())
                ->with('type', 'error')->with('message', $e->validator->errors()->first());
        } catch (\Exception $e) {
            Log::error('Import error: '.$e->getMessage());

            return redirect()->back()->with('type', 'error')->with('message', 'Data jadwal gagal diimpor. Periksa isi dan format berkas, lalu coba lagi. Jika masalah berlanjut, hubungi operator.');
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
            if (! empty($filter_kelas)) {
                $q->where('kelas_id', $filter_kelas);
            }
            $q->with(['mapel', 'kelas']);
        }]);

        // 3. Apply Search on Pegawai
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        // 4. Optimization: Calculate total hours directly in Database
        $query->withSum(['jadwals as total_menit' => function ($q) use ($tahunIDUntukProses, $filter_kelas) {
            $q->where('tahun_id', $tahunIDUntukProses);
            if (! empty($filter_kelas)) {
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

            foreach ($details as $k => $v) {
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
        $activeYear = Tahun::aktif()->first();

        if (! $activeYear) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // 3. Query Jadwal
        $query = Jadwal::with(['kelas', 'mapel', 'pegawai'])
            ->where('tahun_id', $activeYear->id)
            ->where('hari', $dayName)
            ->orderBy('jam');

        // Filter Kelas Logic
        $filter_kelas = $request->input('filter_kelas');
        if ($filter_kelas) {
            $query->where('kelas_id', $filter_kelas);
        }

        // 4. Role & Piket Check
        $user = auth()->user();
        $isPiket = false;
        $viewMode = $request->input('view_mode', 'all');

        if ($user->role === 'guru') {
            if (! $user->pegawai_id) {
                return redirect()->back()->with('error', 'Akun anda tidak terhubung dengan data pegawai.');
            }

            // Cek apakah guru ini sedang PIKET pada hari tersebut?
            $isPiket = \App\Models\JadwalPiket::where('pegawai_id', $user->pegawai_id)
                ->where('hari', $dayName)
                ->where('tahun_id', $activeYear->id)
                ->exists();

            // Jika BUKAN piket, maka hanya bisa lihat jadwal sendiri
            if (! $isPiket) {
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
