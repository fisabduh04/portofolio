<?php

namespace App\Http\Controllers;

use App\Exports\KelasExport;
use App\Imports\KelasImport;
use App\Models\kelas;
use Illuminate\Http\Request;
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
    public function show(kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(kelas $kelas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, kelas $kelas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(kelas $kelas)
    {
        //
    }
}
