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
        $kelas = kelas::all();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */

     public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        $file=$request->file('file')->store('public/import');
        Excel::import(new KelasImport, $file);
        return redirect('/kelas')->with('success','Data berhasil di import');

    }
     public function export(){
        return Excel::download(new KelasExport, 'kelas.xlsx');

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
        // @dd($request->all());
        kelas::create($request->all());
        return redirect('kelas')->with('success', 'Data Kelas Berhasil diinput');
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
