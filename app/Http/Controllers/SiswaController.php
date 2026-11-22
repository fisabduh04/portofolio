<?php

namespace App\Http\Controllers;

use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use App\Models\FaceSample;
use App\Models\Siswa;
use App\Models\Tahun;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Throwable;

class SiswaController extends Controller
{
    private const PHOTO_RULES = ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'extensions:jpg,jpeg,png,gif', 'max:2048', 'dimensions:max_width=2048,max_height=2048'];

    private const PHOTO_MESSAGES = [
        'foto.image' => 'Foto siswa harus berupa gambar yang valid.',
        'foto.mimes' => 'Format foto siswa harus JPG, PNG, atau GIF.',
        'foto.extensions' => 'Ekstensi foto siswa harus JPG, PNG, atau GIF.',
        'foto.max' => 'Ukuran foto siswa maksimal 2 MB.',
        'foto.dimensions' => 'Dimensi foto siswa maksimal 2048 × 2048 piksel.',
    ];

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

    public function bulkDelete(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['nullable', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
        ]);
        $ids = $data['ids'] ?? [];

        if ($ids === []) {
            return back()->with('message', 'Tidak ada data yang dipilih')->with('type', 'warning');
        }

        try {
            $students = DB::transaction(function () use ($ids) {
                $students = Siswa::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
                foreach ($students as $student) {
                    $student->delete();
                }

                return $students;
            });
        } catch (QueryException $exception) {
            return $this->deletionFailed($exception);
        }

        foreach ($students as $student) {
            if ($student->foto) {
                Storage::disk('public')->delete($student->foto);
            }
        }

        return back()->with('message', $students->count().' data siswa berhasil dihapus')->with('type', 'success');
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
        $request->validate([
            'nama' => 'required',
            'nipd' => 'required',
            'foto' => self::PHOTO_RULES,
        ], self::PHOTO_MESSAGES);

        $this->saveStudent($request);

        return redirect('siswa')->with('message', 'Data telah disimpan')->with('type', 'success');
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
            ->whereHas('logbook', function ($q) use ($year) {
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
            'Hadir' => [], 'Sakit' => [], 'Izin' => [], 'Alpha' => [],
        ];

        foreach ($months as $m) {
            $monthLogs = $logs->filter(function ($log) use ($m) {
                return \Carbon\Carbon::parse($log->logbook->tanggal)->month == $m;
            });

            $chartData['Hadir'][] = $monthLogs->where('status', 'Hadir')->count();
            $chartData['Sakit'][] = $monthLogs->where('status', 'Sakit')->count();
            $chartData['Izin'][] = $monthLogs->where('status', 'Izin')->count();
            $chartData['Alpha'][] = $monthLogs->where('status', 'Alpha')->count();
        }

        // 4. Riwayat Terbaru (Top 10)
        $recentLogs = $logs->sortByDesc(function ($log) {
            return $log->logbook->tanggal.' '.($log->logbook->jadwal->mulai ?? '00:00');
        })->take(10);

        // 5. Data Kelas Aktif
        $kelasSiswa = $siswa->KelasSiswa()
            ->where('tahun_id', $tahunAktif->id ?? 0)
            ->with('kelas')
            ->first();

        $kelasAktif = $kelasSiswa && $kelasSiswa->kelas ? $kelasSiswa->kelas->kelas : '-';

        // 6. Riwayat Akademik (History Kelas per Tahun/Semester)
        $history = $siswa->KelasSiswa()
            ->with(['kelas', 'tahun'])
            ->orderByDesc('id')
            ->get()
            ->map(function ($h) use ($siswa) {
                // Hitung Absensi per Periode Tahun Ajaran tersebut
                if ($h->tahun) {
                    $stats = \App\Models\Absensi::where('siswa_id', $siswa->id)
                        ->whereHas('logbook', function ($q) use ($h) {
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
                        'HadirPercent' => $total > 0 ? round((($stats['Hadir'] ?? 0) / $total) * 100, 1) : 0,
                    ];
                } else {
                    $h->stats = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0, 'Total' => 0, 'HadirPercent' => 0];
                }

                return $h;
            });

        // 7. Smart Prediction & Risk Analysis
        $prediction = [
            'status' => 'Aman',
            'color' => 'green',
            'message' => 'Tingkat kehadiran siswa sangat baik. Pertahankan!',
            'trend' => 'stabil', // naik, turun, stabil
            'predicted_score' => 100,
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
            $activeMonths = array_keys(array_filter($chartData['Hadir'], fn ($val) => $val > 0));
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

        $faceSampleCount = FaceSample::where('siswa_id', $siswa->id)->where('is_active', true)->count();

        return view('siswa.show', compact('siswa', 'summaryStats', 'chartData', 'recentLogs', 'kelasAktif', 'year', 'history', 'prediction', 'faceSampleCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $siswa = Siswa::find($siswa->id);

        return view('siswa.input-siswa', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        // Handle AJAX request specifically for real-time status updates
        if ($request->ajax() || $request->wantsJson()) {
            if ($request->has('aktif')) {
                $siswa->update(['aktif' => $request->aktif]);

                return response()->json(['success' => true, 'message' => 'Status berhasil diubah']);
            }
        }

        // Standard Form Update
        $request->validate([
            'nama' => 'required',
            'nipd' => 'required',
            'foto' => self::PHOTO_RULES,
        ], self::PHOTO_MESSAGES);

        $this->saveStudent($request, $siswa);

        return redirect()->route('siswa.index')->with('message', 'Data berhasil diperbarui')->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        try {
            DB::transaction(fn () => $siswa->delete());
        } catch (QueryException $exception) {
            return $this->deletionFailed($exception);
        }

        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        return redirect()->route('siswa.index')->with('message', 'Data siswa berhasil dihapus')->with('type', 'success');
    }

    private function deletionFailed(QueryException $exception): RedirectResponse
    {
        if (($exception->errorInfo[1] ?? null) === 1451 || str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')) {
            return back()->with('message', 'Penghapusan dibatalkan. Siswa masih memiliki data terkait. Gunakan status nonaktif untuk mempertahankan riwayat.')->with('type', 'warning');
        }

        report($exception);

        return back()->with('message', 'Data siswa gagal dihapus. Silakan coba kembali.')->with('type', 'error');
    }

    private function saveStudent(Request $request, ?Siswa $student = null): void
    {
        $data = $request->except('foto');
        $previousPhoto = $student?->foto;
        $newPhoto = $request->hasFile('foto') ? $this->storeStudentPhoto($request->file('foto')) : null;
        if ($newPhoto !== null) {
            $data['foto'] = $newPhoto;
        }

        try {
            DB::transaction(function () use ($student, $data): void {
                if ($student) {
                    $student->update($data);
                } else {
                    Siswa::create($data);
                }
            });
        } catch (Throwable $exception) {
            if ($newPhoto !== null) {
                Storage::disk('public')->delete($newPhoto);
            }

            throw $exception;
        }

        if ($newPhoto !== null && $previousPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }
    }

    private function storeStudentPhoto(UploadedFile $photo): string
    {
        $source = @imagecreatefromstring($photo->getContent());
        if ($source === false) {
            throw ValidationException::withMessages(['foto' => 'Foto siswa rusak atau tidak dapat diproses.']);
        }

        $image = false;
        $stream = false;
        try {
            $image = imagecreatetruecolor(imagesx($source), imagesy($source));
            $stream = fopen('php://temp', 'w+b');
            if ($image === false || $stream === false) {
                throw new RuntimeException('Foto siswa tidak dapat diproses.');
            }

            imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
            imagecopy($image, $source, 0, 0, 0, 0, imagesx($source), imagesy($source));
            if (! imagejpeg($image, $stream, 90)) {
                throw new RuntimeException('Foto siswa tidak dapat diproses.');
            }
            rewind($stream);

            $path = 'uploads/foto/'.Str::uuid().'.jpg';
            try {
                if (! Storage::disk('public')->put($path, $stream)) {
                    throw new RuntimeException('Foto siswa gagal disimpan.');
                }
            } catch (Throwable $exception) {
                Storage::disk('public')->delete($path);

                throw $exception;
            }

            return $path;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
            if ($image !== false) {
                imagedestroy($image);
            }
            imagedestroy($source);
        }
    }
}
