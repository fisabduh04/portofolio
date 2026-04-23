<?php

namespace App\Http\Controllers;

use App\Models\KelasSiswa;
use Illuminate\Http\Request;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Tahun;
use App\Models\Siswa;
use App\Exports\KelasSiswaExport;
use App\Imports\KelasSiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasSiswaController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        $kelas = Kelas::all(['id', 'kelas']);
        $tahun = Tahun::aktif()->get(['id', 'tahun', 'semester']);
        $siswa = Siswa::aktif()->get(['id', 'nipd', 'nama']);
        $filterKelas = $request->filter_kelas ?? 'all';
        $filtertahun = $request->filter_tahun ?? 'all';
        $query = KelasSiswa::with(['siswa', 'kelas', 'tahun']);

        if ($filterKelas && $filterKelas != 'all') {
            $query->whereHas('kelas', function ($q) use ($filterKelas) {
                $q->where('id', $filterKelas);
            });
        }

        if ($filtertahun && $filtertahun != 'all') {
            $query->whereHas('tahun', function ($q) use ($filtertahun) {
                $q->where('id', $filtertahun);
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
                    $q->whereRaw("CAST(tahun AS CHAR) LIKE ?", ["%$search%"])
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


        

        $perpage = $request->input('per_page', 10);
        $pemetaans = $query->paginate($perpage);

        return view('kelassiswa.index', compact('pemetaans', 'tahun', 'siswa', 'kelas'));
    }

    public function store(Request $request)
    {
        // Handle Bulk Input (Arrays for Students, Single for Class/Year)
        if ($request->has('siswa_id') && is_array($request->siswa_id)) {
            $request->validate([
                'siswa_id.*' => 'required|exists:siswas,id',
                'ket.*' => 'required|in:aktif,do,naik,tinggal'
            ]);
            
            // Check if kelas_id and tahun_id are arrays or single values
            // If they are arrays, validate each item. If single, validate once.
            if (is_array($request->kelas_id)) {
                 $request->validate(['kelas_id.*' => 'required|exists:kelas,id']);
            } else {
                 $request->validate(['kelas_id' => 'required|exists:kelas,id']);
            }

            if (is_array($request->tahun_id)) {
                 $request->validate(['tahun_id.*' => 'required|exists:tahuns,id']);
            } else {
                 $request->validate(['tahun_id' => 'required|exists:tahuns,id']);
            }

            $count = count($request->siswa_id);
            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->siswa_id[$i])) {
                    // Use array index if array, else use single value
                    $kelasId = is_array($request->kelas_id) ? $request->kelas_id[$i] : $request->kelas_id;
                    $tahunId = is_array($request->tahun_id) ? $request->tahun_id[$i] : $request->tahun_id;

                    KelasSiswa::updateOrCreate(
                        [
                            'siswa_id' => $request->siswa_id[$i],
                            'tahun_id' => $tahunId
                        ],
                        [
                            'kelas_id' => $kelasId,
                            'ket' => $request->ket[$i] ?? 'aktif' // Default safe value
                        ]
                    );
                }
            }

            if ($request->ajax()) {
                return response()->json(['message' => 'Data berhasil disimpan.']);
            }

            return redirect()->route('kelassiswa.index')->with('success', 'Data Massal Berhasil Disimpan');
        }

        // Handle Single Input (Old Way / Fallback / Edit)
        $validated = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required',
            'tahun' => 'required',
            'ket' => 'required'
        ]);

        KelasSiswa::updateOrCreate(
            [
                'siswa_id' => $validated['siswa'],
                'tahun_id' => $validated['tahun']
            ],
            [
                'kelas_id' => $validated['kelas'],
                'ket' => $validated['ket']
            ]
        );

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Siswa berhasil ditambahkan ke kelas.')
            ->with('type', 'success');
    }

    public function edit(Request $request, $id)
    {
        // ... (Keep existing edit logic if strictly needed, though we moved to inline/bulk)
        $pemetaan = KelasSiswa::findOrFail($id);
        // Redirect to index because we use inline edit now, or keep separate page?
        // Let's keep it simply redirecting or showing index with modal info if needed.
        // For now, let's just return the view as before to not break functionality
             $kelas = Kelas::all(['id', 'kelas']);
        $tahun = Tahun::aktif()->get();
        $siswa = Siswa::all(['id', 'nama', 'nipd']);

        $query = KelasSiswa::with(['siswa', 'kelas', 'tahun']);

        if ($request->filled('sort') && $request->filled('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perpage = $request->input('per_page', 10);
        $pemetaans = $query->paginate($perpage);

        return view('kelassiswa.index', compact('pemetaans', 'pemetaan', 'kelas', 'siswa', 'tahun'));
    }

    public function update(Request $request, $id)
    {
        $pemetaan = KelasSiswa::findOrFail($id);

        // Handle Inline Update via AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $validated = $request->validate([
                'kelas_id' => 'sometimes|exists:kelas,id',
                'tahun_id' => 'sometimes|exists:tahuns,id',
                'ket' => 'sometimes|in:aktif,do,naik,tinggal',
                // 'siswa_id' => 'sometimes' // Usually we don't change student ID inline
            ]);

            $pemetaan->update($validated);

            return response()->json(['message' => 'Update berhasil', 'data' => $pemetaan]);
        }

        // Standard Form Update
        $validated = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required',
            'tahun' => 'required',
            'ket' => 'required'
        ]);

        $pemetaan->update([
            'kelas_id' => $validated['kelas'],
            'siswa_id' => $validated['siswa'],
            'tahun_id' => $validated['tahun'],
            'ket' => $validated['ket'],
        ]);

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil diupdate.')
            ->with('type', 'success');
    }

    public function destroy(Request $request, $id = null)
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
            return redirect()->route('kelassiswa.index')
                ->with('message', 'Data tidak valid atau tidak ada yang dipilih.')
                ->with('type', 'error');
        }

        // Pastikan ids adalah array jika belum (untuk safety)
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        KelasSiswa::whereIn('id', $ids)->delete();

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil dihapus.')
            ->with('type', 'success');
    }
    public function export(Request $request)
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

    public function import()
    {
        Excel::import(new KelasSiswaImport, request()->file('file'));

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil diimport.')
            ->with('type', 'success');
    }
}
