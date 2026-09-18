<?php

namespace App\Http\Controllers;

use App\Exports\KelasSiswaExport;
use App\Imports\KelasSiswaImport;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\Tahun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KelasSiswaController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'required' => ':attribute wajib diisi.',
        'exists' => ':attribute yang dipilih tidak tersedia. Silakan pilih kembali.',
        'array' => ':attribute harus berupa daftar isian.',
        'list' => 'Urutan isian :attribute tidak valid. Silakan periksa kembali.',
        'size' => 'Jumlah isian :attribute harus sesuai dengan jumlah siswa.',
        'min' => 'Tambahkan setidaknya satu siswa.',
        'integer' => ':attribute tidak valid. Silakan pilih kembali.',
        'in' => ':attribute tidak valid. Pilih Aktif, Berhenti, Naik Kelas, atau Tidak Naik Kelas.',
        'file.required' => 'Pilih berkas yang akan diimpor terlebih dahulu.',
        'file.mimes' => 'Berkas impor harus berformat CSV, XLS, atau XLSX.',
        'file.uploaded' => 'Berkas gagal diunggah. Periksa ukuran berkas dan koneksi Anda, lalu coba lagi.',
    ];

    private const VALIDATION_ATTRIBUTES = [
        'siswa' => 'Siswa', 'siswa_id' => 'Siswa', 'siswa_id.*' => 'Siswa',
        'kelas' => 'Kelas', 'kelas_id' => 'Kelas', 'kelas_id.*' => 'Kelas',
        'tahun' => 'Tahun ajaran', 'tahun_id' => 'Tahun ajaran', 'tahun_id.*' => 'Tahun ajaran',
        'ket' => 'Status siswa', 'ket.*' => 'Status siswa',
    ];

    public function index(Request $request): View
    {
        $kelas = Kelas::all(['id', 'kelas']);
        $tahun = Tahun::aktif()->get(['id', 'tahun', 'semester']);
        $siswa = Siswa::aktif()->get(['id', 'nipd', 'nama']);
        $filterKelas = $request->filter_kelas ?? 'all';
        $filterTahun = $request->filter_tahun ?? 'all';
        $query = KelasSiswa::with(['siswa', 'kelas', 'tahun']);

        if ($filterKelas && $filterKelas != 'all') {
            $query->whereHas('kelas', function ($q) use ($filterKelas) {
                $q->where('id', $filterKelas);
            });
        }

        if ($filterTahun && $filterTahun != 'all') {
            $query->whereHas('tahun', function ($q) use ($filterTahun) {
                $q->where('id', $filterTahun);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('siswa', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%")
                        ->orWhere('nipd', 'like', "%$search%");
                })->orWhereHas('kelas', function ($q) use ($search) {
                    $q->where('kelas', 'like', "%$search%");
                })->orWhereHas('tahun', function ($q) use ($search) {
                    $q->whereRaw('CAST(tahun AS CHAR) LIKE ?', ["%$search%"])
                        ->orWhere('semester', 'like', "%$search%");
                })->orWhere('ket', 'like', "%$search%");
            });
        }

        if ($request->filled('sort') && $request->filled('direction')) {
            $sort = $request->sort;
            $direction = $request->direction;

            switch ($sort) {
                case 'nama':
                    $query->join('siswas', 'siswas.id', '=', 'kelas_siswas.siswa_id')
                        ->orderBy('siswas.nama', $direction)
                        ->select('kelas_siswas.*');
                    break;
                case 'nipd':
                    $query->join('siswas', 'siswas.id', '=', 'kelas_siswas.siswa_id')
                        ->orderBy('siswas.nipd', $direction)
                        ->select('kelas_siswas.*');
                    break;
                case 'kelas':
                    $query->join('kelas', 'kelas.id', '=', 'kelas_siswas.kelas_id')
                        ->orderBy('kelas.kelas', $direction)
                        ->select('kelas_siswas.*');
                    break;
                case 'tahun':
                    $query->join('tahuns', 'tahuns.id', '=', 'kelas_siswas.tahun_id')
                        ->orderBy('tahuns.tahun', $direction)
                        ->select('kelas_siswas.*');
                    break;
                case 'ket':
                    $query->orderBy('kelas_siswas.ket', $direction);
                    break;
            }
        }

        $perPage = $request->input('per_page', 10);
        $pemetaans = $query->paginate($perPage);

        return view('kelassiswa.index', compact('pemetaans', 'tahun', 'siswa', 'kelas'));
    }

    /** @return array<string, string> */
    private function assignmentRules(): array
    {
        return [
            'siswa_id' => 'required|integer|exists:siswas,id',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'tahun_id' => 'required|integer|exists:tahuns,id',
            'ket' => 'required|in:aktif,do,naik,tinggal',
        ];
    }

    /**
     * Accept field names from forms opened before the input names were standardized.
     */
    private function normalizeLegacyInput(Request $request): void
    {
        foreach (['siswa', 'kelas', 'tahun'] as $field) {
            if (! $request->has($field.'_id') && $request->has($field)) {
                $request->merge([$field.'_id' => $request->input($field)]);
            }
        }
    }

    /** @return array<string, mixed> */
    private function validateAssignments(Request $request): array
    {
        $this->normalizeLegacyInput($request);
        $rules = $this->assignmentRules();
        $students = $request->input('siswa_id');

        if (is_array($students)) {
            $rowCount = count($students);
            foreach ($this->assignmentRules() as $field => $fieldRules) {
                if (is_array($request->input($field))) {
                    $rules[$field] = 'required|array|list|min:1|size:'.$rowCount;
                    $rules[$field.'.*'] = $fieldRules;
                }
            }
        }

        return $request->validate($rules, self::VALIDATION_MESSAGES, self::VALIDATION_ATTRIBUTES);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $this->validateAssignments($request);
        $studentIds = (array) $validated['siswa_id'];

        DB::transaction(function () use ($validated, $studentIds): void {
            foreach ($studentIds as $index => $studentId) {
                $assignment = ['siswa_id' => $studentId];
                foreach (['kelas_id', 'tahun_id', 'ket'] as $field) {
                    $assignment[$field] = is_array($validated[$field]) ? $validated[$field][$index] : $validated[$field];
                }

                KelasSiswa::updateOrCreate(
                    ['siswa_id' => $studentId, 'tahun_id' => $assignment['tahun_id']],
                    $assignment
                );
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Data berhasil disimpan.']);
        }

        return redirect()->back(fallback: route('kelassiswa.index'))
            ->with('message', 'Data rombongan belajar berhasil disimpan.')
            ->with('type', 'success');
    }

    public function edit(Request $request, int $id): View
    {
        return $this->index($request)
            ->with('pemetaan', KelasSiswa::findOrFail($id))
            ->with('siswa', Siswa::all(['id', 'nama', 'nipd']));
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $pemetaan = KelasSiswa::findOrFail($id);
        $rules = $this->assignmentRules();
        $isInlineUpdate = $request->ajax() || $request->wantsJson();

        if ($isInlineUpdate) {
            unset($rules['siswa_id']);
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = 'sometimes|'.$fieldRules;
            }
        } else {
            $request->merge(['_kelassiswa_edit_id' => $id]);
            $this->normalizeLegacyInput($request);
        }

        $validated = $request->validate($rules, self::VALIDATION_MESSAGES, self::VALIDATION_ATTRIBUTES);
        $pemetaan->update($validated);

        if ($isInlineUpdate) {
            return response()->json(['message' => 'Status berhasil diperbarui.', 'data' => $pemetaan]);
        }

        return redirect()->back(fallback: route('kelassiswa.index'))
            ->with('message', 'Data berhasil diperbarui.')
            ->with('type', 'success');
    }

    public function destroy(Request $request, ?int $id = null): RedirectResponse
    {
        // 1. Cek apakah ada input 'id' (array) dari Body (Bulk Delete)
        // Kita prioritaskan ini karena form bulk delete mungkin mengirim ke URL dengan ID sembarang (misal: ID baris pertama)
        if ($request->has('id') && is_array($request->input('id'))) {
            $ids = $request->input('id');
        }
        // 2. Jika tidak ada di body, cek apakah ada ID dari Route Parameter (Single Delete)
        elseif ($id) {
            $ids = [$id];
        }
        // 3. Fallback: Cek input 'id' lagi kalau-kalau bukan array (meski jarang)
        else {
            $ids = $request->input('id');
        }

        // Validasi
        if (empty($ids)) {
            return redirect()->back(fallback: route('kelassiswa.index'))
                ->with('message', 'Data tidak valid atau tidak ada yang dipilih.')
                ->with('type', 'error');
        }

        // Pastikan ids adalah array jika belum (untuk safety)
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        KelasSiswa::whereIn('id', $ids)->delete();

        return redirect()->back(fallback: route('kelassiswa.index'))
            ->with('message', 'Data berhasil dihapus.')
            ->with('type', 'success');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $ids = $request->input('ids');
        if ($ids) {
            $ids = explode(',', $ids);
        }

        $activeYearId = null;
        if (empty($ids)) {
            $activeYear = Tahun::aktif()->first();
            if ($activeYear) {
                $activeYearId = $activeYear->id;
            }
        }

        return Excel::download(new KelasSiswaExport($ids, $activeYearId), 'KelasSiswa.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|mimes:csv,xls,xlsx'], self::VALIDATION_MESSAGES);
        Excel::import(new KelasSiswaImport, $request->file('file'));

        return redirect()->back(fallback: route('kelassiswa.index'))
            ->with('message', 'Data berhasil diimport.')
            ->with('type', 'success');
    }
}
