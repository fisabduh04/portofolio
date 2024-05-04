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
        tahun_ajaran::updateOrCreate([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
        ], [


            'tanggalmulai' => $request->tanggalmulai,
            'tanggalakhir' => $request->tanggalakhir,
            'isActive' => $request->isActive,
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
    public function update(Request $request, $id)
    {
        $tahun_ajaran = tahun_ajaran::find($id);

        if ($tahun_ajaran) {
            $tahun_ajaran->tahun = $request->tahun ?? $tahun_ajaran->tahun;
            $tahun_ajaran->semester = $request->semester ?? $tahun_ajaran->semester;
            $tahun_ajaran->tanggalmulai = $request->tanggalmulai ?? $tahun_ajaran->tanggalmulai;
            $tahun_ajaran->tanggalakhir = $request->tanggalakhir ?? $tahun_ajaran->tanggalakhir;
            $tahun_ajaran->isActive = $request->isActive ?? $tahun_ajaran->isActive;

            $tahun_ajaran->save();

            return redirect('tahun')->with('success', 'Data Tahun Ajaran Berhasil Diperbarui');
        } else {
            return redirect('tahun')->with('error', 'Tidak dapat menemukan data Tahun Ajaran yang sesuai');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tahun_ajaran $tahun_ajaran, $id)
    {
        tahun_ajaran::destroy($id);
        return redirect('tahun')->with('error', 'Data Tahun Ajaran Berhasil dihapus');
    }
}
