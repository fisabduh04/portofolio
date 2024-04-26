<?php

namespace App\Http\Controllers;

use App\Models\tahun_ajaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahun = tahun_ajaran::all();
        // @dd($tahun);
        return view('tahun.index', compact('tahun'));
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
        // @dd($request->all());
        tahun_ajaran::create([
            'tahun' => $request->tahun . '- Semester ' . $request->semester,
            'tanggalmulai' => $request->tanggalmulai,
            'tanggalakhir' => $request->tanggalakhir,
            'Aktif' => $request->Aktif,
        ]);
        return redirect('tahun')->with('success', 'Data Tahun Ajaran Baru Berhasil Diinput');
    }

    /**
     * Display the specified resource.
     */
    public function show(tahun_ajaran $tahun_ajaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tahun_ajaran $tahun_ajaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tahun_ajaran $tahun_ajaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tahun_ajaran $tahun_ajaran, $id)
    {
        tahun_ajaran::destroy($id);
        return redirect('tahun')->with('error', 'Data Tahun Ajaran Baru Berhasil dihapus');

    }
}
