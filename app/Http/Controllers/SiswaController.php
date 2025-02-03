<?php

namespace App\Http\Controllers;

use App\Models\siswa;
use App\Models\tahun;
use App\Models\jurusan;
use App\Exports\Siswaexport;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa=siswa::all();
        // dd($siswa);
        return view('siswa.index',compact('siswa'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function export()
    {
        return Excel::download(new SiswaExport, 'siswa.xlsx');
        return redirect()->route('siswa.index')->with('message', 'Data siswa berhasil dieksport')->with('type', 'success');
    }
    public function import()
    {
        Excel::import(new SiswaImport, request()->file('file'));
        return redirect()->route('siswa.index')->with('message', 'Data siswa berhasil diimport')->with('type', 'success');

    }
     public function create()
    {
        return view('siswa.input-siswa');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // @dd($request->all());
        $request->validate([
            'nama'=>'required',
            'nipd'=>'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,png|max:2048',
        ]);
        $data=$request->all();

        if($request->hasFile('foto')){
            $data['foto']=$request->file('foto')->store('uploads/foto','public');
        }
        Siswa::create($data);
        return redirect('siswa')->with('message','Data telah disimpan')->with('type','success');
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
        $siswa=siswa::find($siswa->id);
        return view('siswa.input-siswa',compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, siswa $siswa)
    {
        // dd($request->all());
        $request->validate([
            'nama'=>'required',
            'nipd'=>'required',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('uploads/foto', 'public');
        }

        $siswa->update($data);

        return redirect()->route('siswa.index')->with('message', 'Data berhasil diperbarui')->with('type', 'success');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(siswa $siswa)
    {
        // dd($siswa);
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }
        $siswa->delete();
        return redirect('siswa')->with('message','Data Berhasil Dihapus')->with('type','error');

    }

}
