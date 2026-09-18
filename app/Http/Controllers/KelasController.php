<?php

namespace App\Http\Controllers;

use App\Exports\KelasExport;
use App\Imports\KelasImport;
use App\Models\Kelas;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kelas.index');
    }

    public function export()
    {
        return Excel::download(new KelasExport, 'kelas.xlsx');

        return redirect()->route('kelas.index')->with('success', 'Data Kelas berhasil dieksport');
    }

    public function import()
    {
        Excel::import(new KelasImport, request()->file('file'));

        return redirect()->route('kelas.index')->with('success', 'Data Kelas berhasil diimport');

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $kelas = Kelas::findOrFail($id);

        try {
            DB::transaction(fn () => $kelas->delete());
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451 || str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')) {
                return back()->with('message', 'Kelas tidak dapat dihapus karena masih digunakan oleh penempatan siswa, jadwal, jurnal mengajar, atau wali kelas.')->with('type', 'warning');
            }

            report($exception);

            return back()->with('message', 'Data kelas gagal dihapus. Silakan coba kembali.')->with('type', 'error');
        }

        return back()->with('message', 'Data kelas berhasil dihapus.')->with('type', 'success');
    }
}
