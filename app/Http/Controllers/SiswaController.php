<?php

namespace App\Http\Controllers;

use App\Models\siswa;
use Illuminate\Http\Request;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa=siswa::all();
        return view('siswa.index',compact('siswa'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function export()
    {
        return Excel::download(new SiswaExport, 'siswa.xlsx');
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dieksport');
    } 
    public function import()
    {
        Excel::import(new SiswaImport, request()->file('file'));
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diimport');

    }
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
    public function show(siswa $siswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(siswa $siswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, siswa $siswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(siswa $siswa)
    {
        //
    }
}
