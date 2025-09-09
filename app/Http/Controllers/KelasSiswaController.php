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
        $siswa = Siswa::all(['id', 'nipd', 'nama']);
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
        $pemetaans->load(['siswa', 'kelas', 'tahun']);

        return view('kelassiswa.index', compact('pemetaans', 'tahun', 'siswa', 'kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required',
            'tahun' => 'required',
        ]);

        $siswaIds = is_array($validated['siswa']) ? $validated['siswa'] : [$validated['siswa']];
        foreach ($siswaIds as $siswaId) {
            KelasSiswa::updateOrCreate(
                ['siswa_id' => $siswaId],
                [
                    'kelas_id' => $validated['kelas'],
                    'tahun_id' => $validated['tahun'],
                    'ket' => $request->ket,
                ]
            );
        }

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Siswa berhasil ditambahkan ke kelas.')
            ->with('type', 'success');
    }

    public function edit(Request $request, $id)
    {
        $pemetaan = KelasSiswa::findOrFail($id);
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
        $validated = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required',
            'tahun' => 'required',
        ]);

        $pemetaan = KelasSiswa::findOrFail($id);
        $pemetaan->update([
            'kelas_id' => $validated['kelas'],
            'tahun_id' => $validated['tahun'],
            'ket' => $request->ket,
        ]);

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil diupdate.')
            ->with('type', 'success');
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('id');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('kelassiswa.index')
                ->with('message', 'Data tidak valid atau tidak ada yang dipilih.')
                ->with('type', 'error');
        }

        KelasSiswa::whereIn('id', $ids)->delete();

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil dihapus.')
            ->with('type', 'success');
    }
    public function export()
    {
        return Excel::download(new KelasSiswaExport, 'KelasSiswa.xlsx');
        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil diexport.')
            ->with('type', 'success');
    }

    public function import()
    {
        Excel::import(new KelasSiswaImport, request()->file('file'));

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil diimport.')
            ->with('type', 'success');
    }
}
