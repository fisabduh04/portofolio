<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Logbook;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        if ($mode === 'piket' && ! auth()->user()->isPiketToday()) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan guru piket hari ini.');
        }

        if (! $jadwalId) {
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
            'attendance.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha,Pulang,Telat',
            'attendance.*.keterangan' => 'nullable|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'required|in:mapel,piket_sub', // Validasi kategori
            'tanggal' => 'required|date',
        ], [
            'materi.required' => 'Materi/Pembahasan wajib diisi.',
            'attendance.required' => 'Data presensi siswa wajib diisi.',
            'attendance.*.status.required' => 'Status presensi siswa wajib dipilih.',
            'attendance.*.status.in' => 'Status presensi tidak valid.',
            'foto.*.image' => 'File yang diunggah harus berupa gambar.',
            'foto.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.*.max' => 'Ukuran gambar maksimal 2MB per file.',
            'tanggal.required' => 'Tanggal wajib diisi.',
        ]);

        $user = auth()->user();

        // --- AUTO-FIX: Coba hubungkan akun Admin/Operator ke Pegawai jika belum ada ---
        if (! $user->pegawai_id && in_array($user->role, ['admin', 'operator', 'kepala'])) {
            $matchingPegawai = Pegawai::where('email', $user->email) // Prioritas 1: Email sama
                ->orWhere('name', 'LIKE', $user->name)               // Prioritas 2: Nama mirip
                ->first();

            if ($matchingPegawai) {
                $user->update(['pegawai_id' => $matchingPegawai->id]);
                $user->refresh(); // Refresh model agar pegawai_id terisi
                // Lanjut proses di bawah...
            }
        }

        if (! $user->pegawai_id) {
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

            // Tangani Penghapusan Foto Lama yang Dipilih untuk Dihapus
            if ($logbook && $request->has('deleted_photos')) {
                $deletedPhotos = $request->input('deleted_photos');
                foreach ($deletedPhotos as $delPath) {
                    if (in_array($delPath, $photos)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($delPath);
                        $photos = array_values(array_diff($photos, [$delPath]));
                    }
                }
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
            Log::error('Gagal simpan presensi: '.$e->getMessage());

            return redirect()->back()
                ->with('type', 'error')
                ->with('message', 'Terjadi kesalahan sistem saat menyimpan data presensi. Silakan periksa kembali input Anda atau hubungi admin.')
                ->withInput();
        }
    }

    // --- PRESENSI HARIAN (PIKET) ---
    public function createHarian(Request $request)
    {
        if (! auth()->user()->isPiketToday()) {
            abort(403, 'Akses ditolak. Anda bukan guru piket hari ini.');
        }

        $kelasId = $request->query('kelas_id');
        $type = $request->query('type', 'masuk'); // masuk atau pulang

        if (! $kelasId) {
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
        if (! $jadwal) {
            return redirect()->back()->with('error', 'Tidak ada jadwal pelajaran untuk kelas ini hari ini ('.now()->locale('id')->isoFormat('dddd').'). Absensi harian membutuhkan minimal 1 jadwal aktif.');
        }

        // Kategori: piket_masuk / piket_pulang
        $kategori = 'piket_'.$type;

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
        if (! auth()->user()->isPiketToday()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kategori' => 'required|in:piket_masuk,piket_pulang',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha,Pulang,Telat',
            'catatan' => 'nullable|string',
            'foto.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'attendance.required' => 'Data presensi siswa wajib diisi.',
            'attendance.*.status.required' => 'Status presensi siswa wajib dipilih.',
            'attendance.*.status.in' => 'Status presensi tidak valid.',
            'foto.*.image' => 'File yang diunggah harus berupa gambar.',
            'foto.*.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.*.max' => 'Ukuran gambar maksimal 2MB per file.',
        ]);

        $user = auth()->user();
        if (! $user->pegawai_id) {
            return redirect()->back()->with('type', 'error')->with('message', 'Akun anda tidak terhubung dengan data pegawai.');
        }

        try {
            DB::beginTransaction();

            // Cari jadwal untuk dikaitkan
            $jadwal = Jadwal::where('kelas_id', $request->kelas_id)
                ->where('hari', now()->locale('id')->isoFormat('dddd'))
                ->orderBy('mulai')
                ->first();

            if (! $jadwal) {
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

            // Tangani Penghapusan Foto Lama yang Dipilih untuk Dihapus
            if ($logbook && $request->has('deleted_photos')) {
                $deletedPhotos = $request->input('deleted_photos');
                foreach ($deletedPhotos as $delPath) {
                    if (in_array($delPath, $photos)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($delPath);
                        $photos = array_values(array_diff($photos, [$delPath]));
                    }
                }
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
            Log::error('Gagal simpan presensi harian: '.$e->getMessage());

            return back()->with('type', 'error')->with('message', 'Terjadi kesalahan sistem saat menyimpan data presensi harian. Silakan hubungi admin.');
        }
    }

    public function indexHarian()
    {
        if (! auth()->user()->isPiketToday()) {
            abort(403, 'Akses ditolak.');
        }

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

    public function piket()
    {
        if (! auth()->user()->isPiketToday()) {
            return redirect()->route('dashboard.index')->with('error', 'Akses ditolak. Anda bukan guru piket hari ini.');
        }

        return view('absensi.piket');
    }

    public function piketCheckIn(Request $request)
    {
        if (! auth()->user()->isPiketToday()) {
            abort(403);
        }

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('absensi/piket', 'public');
            }

            Logbook::create([
                'kategori' => 'piket_masuk',
                'pegawai_id' => auth()->user()->pegawai_id,
                'tanggal' => now()->toDateString(),
                'catatan' => 'Guru Piket Check-in',
                'foto' => $fotoPath ? json_encode([$fotoPath]) : null,
            ]);

            return redirect()->back()->with('type', 'success')->with('message', 'Berhasil Check-in Piket.');
        } catch (\Exception $e) {
            Log::error('Gagal piket check-in: '.$e->getMessage());

            return redirect()->back()->with('type', 'error')->with('message', 'Gagal melakukan Check-in Piket. Terjadi kesalahan pada sistem.');
        }
    }

    public function piketCheckOut(Request $request)
    {
        if (! auth()->user()->isPiketToday()) {
            abort(403);
        }

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('absensi/piket', 'public');
            }

            Logbook::create([
                'kategori' => 'piket_pulang',
                'pegawai_id' => auth()->user()->pegawai_id,
                'tanggal' => now()->toDateString(),
                'catatan' => 'Guru Piket Check-out',
                'foto' => $fotoPath ? json_encode([$fotoPath]) : null,
            ]);

            return redirect()->back()->with('type', 'success')->with('message', 'Berhasil Check-out Piket.');
        } catch (\Exception $e) {
            Log::error('Gagal piket check-out: '.$e->getMessage());

            return redirect()->back()->with('type', 'error')->with('message', 'Gagal melakukan Check-out Piket. Terjadi kesalahan pada sistem.');
        }
    }
}
