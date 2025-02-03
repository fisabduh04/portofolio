<?php

namespace App\Http\Controllers;

use App\Models\KelasSiswa;
use Illuminate\Http\Request;
use App\Models\jurusan;
use App\Models\kelas;
use App\Models\tahun;
use App\Models\siswa;
use App\Exports\KelasSiswaExport;
use App\Imports\KelasSiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class KelasSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kelas = kelas::all();
        $tahun = tahun::aktif()->get();
        $siswa=siswa::all(['id','nipd','nama']);
        $query = KelasSiswa::with(['siswa','kelas','tahun']);

        // Sorting
        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perpage=$request->input('per_page',10);
        $pemetaans = $query->paginate($perpage);

        return view('kelassiswa.index', compact('pemetaans','tahun','siswa','kelas'));
    }

    public function store(Request $request)
    {
        @dd($request);
        $pemetaan=KelasSiswa::all();
        $validatedData = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required|array',
            'tahun' => 'required'
        ]);

        foreach ($validatedData['siswa'] as $siswaId) {
            KelasSiswa::UpdateOrcreate([
                'siswa_id' => $siswaId],[
                'kelas_id' => $validatedData['kelas'],
                'tahun_id' => $validatedData['tahun'],
                'ket' => $request->ket,
            ]);
        }


        return redirect()->route('siswa_kelas.index')->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function edit(Request $request,$id)
    {
        $pemetaan = KelasSiswa::findOrFail($id);
        $kelas = Kelas::all(['id','kelas']);
        $tahun = Tahun::aktif()->get();
        $siswa=siswa::all(['id','nama','nipd']);
        $pemetaans=KelasSiswa::all();

        $query = KelasSiswa::with(['siswa','kelas','tahun']);


        // Sorting
        if ($request->has('sort') && $request->has('direction')) {
            $query->orderBy($request->sort, $request->direction);
        }

        $perpage=$request->input('per_page',10);
        $pemetaans = $query->paginate($perpage);

        return view('kelassiswa.index', compact('pemetaans','pemetaan', 'kelas', 'siswa', 'tahun'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required|array',
            // 'siswa.*' => 'exists:siswa,id',
            'tahun' => 'required'
        ]);


        $pemetaan = KelasSiswa::findOrFail($id);
        $pemetaan->update([
            'kelas_id' => $validatedData['kelas'],
            'tahun_id' => $validatedData['tahun'],
            'ket' => $request->ket
        ]);

        // Update siswa
        $pemetaan->siswa()->sync($validatedData['siswa']);

        return redirect()->route('siswa_kelas.index')->with('message', 'Data berhasil diupdate.')->with('type','success');
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('id');

        if (empty($ids)) {
            return redirect()->route('siswa_kelas.index')
                ->with('message', 'Tidak Ada Data yang Dipilih')
                ->with('type', 'error');
        }

        if (!is_array($ids)) {
            return redirect()->route('kelassiswa.index')
                ->with('message', 'Data tidak valid')
                ->with('type', 'error');
        }

        KelasSiswa::whereIn('id', $ids)->delete();

        return redirect()->route('kelassiswa.index')
            ->with('message', 'Data berhasil dihapus')
            ->with('type', 'success');
    }

    public function filtered(Request $request)
    {
        $query = KelasSiswa::query();

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('tahun_id')) {
            $query->where('tahun_id', $request->tahun_id);
        }

        $pemetaans = $query->with(['kelas', 'siswa', 'tahun_ajaran'])->get();
        $kelas = Kelas::all();
        $tahun = Tahun::all();

        return view('kelassiswa.index', compact('pemetaans', 'kelas', 'tahun'));
    }
    public function export()
    {
        return Excel::download(new KelasSiswaExport, 'KelasSiswa.xlsx');
        return redirect()->route('kelassiswa.index')->with('message', 'Data Kelas-Siswa berhasil dieksport')->with('type','success');
    }
    public function import()
    {
        Excel::import(new KelasSiswaImport, request()->file('file'));
        return redirect()->route('kelassiswa.index')->with('message', 'Data Kelas-Siswa berhasil diimport')->with('type','success');

    }
}
