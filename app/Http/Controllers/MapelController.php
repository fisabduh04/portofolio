<?php

namespace App\Http\Controllers;

use App\Exports\MapelExport;
use App\Imports\ImportMapel;
use App\Models\Mapel;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('mapel.index');
    }

    public function export()
    {
        return Excel::download(new MapelExport, 'mapel.xlsx');

        return redirect()->route('mapel.index')->with('message', 'Data Mata Pelajaran berhasil dieksport')->with('type', 'success');
    }

    public function import()
    {
        Excel::import(new ImportMapel, request()->file('file'));

        return redirect()->route('mapel.index')->with('message', 'Data Mata Pelajaran berhasil diimport')->with('type', 'success');
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
    public function show(mapel $mapel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(mapel $mapel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, mapel $mapel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $mapel = Mapel::findOrFail($id);

        try {
            DB::transaction(fn () => $mapel->delete());
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451 || str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')) {
                return back()->with('message', 'Mapel tidak dapat dihapus karena masih digunakan dalam jadwal atau jurnal mengajar.')->with('type', 'warning');
            }

            report($exception);

            return back()->with('message', 'Data mata pelajaran gagal dihapus. Silakan coba kembali.')->with('type', 'error');
        }

        return back()->with('message', 'Data mata pelajaran berhasil dihapus.')->with('type', 'success');
    }
}
