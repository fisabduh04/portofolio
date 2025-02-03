<?php

namespace App\Http\Controllers;

use App\Exports\PegawaiExport;
use App\Imports\PegawaiImport;
use App\Models\pegawai;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pegawai = pegawai::orderBy('name','asc')->get();
        return view('pegawai.index', compact('pegawai'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pegawai.input-pegawai');
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
        return redirect('pegawai')->with('message', 'Data Pegawai telah diinput')->with('type', 'success');
        // session()->flash('message', 'Data berhasil disimpan!');
        // session()->flash('type', 'success');
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
        return view('pegawai.input-pegawai', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, pegawai $pegawai)
        {
            // dd($pegawai);
            $request->validate([
                'name' => 'required',
                'nuptk' => 'required',
            ]);

            $pegawai->update($request->all());
            return redirect('/pegawai')->with('message', 'Data Berhasil Diupdate')->with('type', 'success');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // @dd($id);
        // pegawai::destroy($id);
        $peg=pegawai::findOrFail($id);
        $peg->delete();

        // return redirect('pegawai')->with('error', 'Data Berhasil Dihapus');
        // session()->flash('message', 'Data berhasil dihapus!');
        // session()->flash('type', 'error');
        // return redirect('pegawai');
        // Di controller
return redirect('pegawai')->with('message', 'Data Berhasil Dihapus')->with('type', 'error');
    }
    public function Addpegawai()
    {
        return view('pegawai.Addpegawai');
    }
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        $file=$request->file('file')->store('public/import');
        Excel::import(new PegawaiImport, $file);
        // return redirect('/pegawai')->with('success','Data berhasil di import');
        return redirect('/pegawai')->with('message','Data berhasil di import')->with('type','success');

    }
     public function export(){
        return Excel::download(new PegawaiExport, 'pegawai.xlsx');

     }
}
