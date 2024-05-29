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
    public function index()
    {
        $kelas = Kelas::all();
        $pemetaans = siswa::with(['kelas','tahun']);
        $tahun = Tahun::all();
        $pemetaans = KelasSiswa::with(['kelas', 'siswa', 'tahun'])->get();

        return view('kelassiswa.index', compact( 'pemetaans'));
    }

    public function store(Request $request)
    {
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

    public function edit($id)
    {
        $pemetaan = KelasSiswa::findOrFail($id);
        $kelas = Kelas::all();
        // $siswa = Siswa::all();
        $tahun = Tahun::all();

        return view('kelassiswa.index', compact('pemetaan', 'kelas', 'siswa', 'tahun'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kelas' => 'required',
            'siswa' => 'required|array',
            'siswa.*' => 'exists:siswa,id',
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

        return redirect()->route('siswa_kelas.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pemetaan = KelasSiswa::findOrFail($id);
        $pemetaan->delete();

        return redirect()->route('siswa_kelas.index')->with('success', 'Data berhasil dihapus.');
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
        return redirect()->route('kelassiswa.index')->with('success', 'Data Kelas-Siswa berhasil dieksport');
    }
    public function import()
    {
        Excel::import(new KelasSiswaImport, request()->file('file'));
        return redirect()->route('kelassiswa.index')->with('success', 'Data Kelas-Siswa berhasil diimport');

    }
}
