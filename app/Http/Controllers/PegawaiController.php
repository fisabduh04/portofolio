<?php

namespace App\Http\Controllers;

use App\Exports\PegawaiExport;
use App\Imports\PegawaiImport;
use App\Models\pegawai;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = pegawai::all();
        return view('pegawai.index', compact('pegawai'));
    }
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        $file=$request->file('file')->store('public/import');
        Excel::import(new PegawaiImport, $file);
        return redirect('/pegawai')->with('success','Data berhasil di import');

    }
     public function export(){
        return Excel::download(new PegawaiExport, 'pegawai.xlsx');

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
        // dd($request->all());

        $request->validate([
            'name' => 'required',
            'nuptk' => 'required',
        ]);

        pegawai::create($request->all());
        return redirect('pegawai')->with('success', 'Data Pegawai telah diinput');
    }

    /**
     * Display the specified resource.
     */
    public function show(pegawai $pegawai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // dd($id);
        $pegawai = pegawai::find($id);
        return view('pegawai.editpegawai');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pegawai $pegawai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // @dd($id);
        pegawai::destroy($id);

        return redirect('pegawai')->with('error', 'Data Berhasil Dihapus');
    }
    public function Addpegawai()
    {
        return view('pegawai.Addpegawai');
    }
}
