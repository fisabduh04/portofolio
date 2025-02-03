<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\absensi;
use Illuminate\Http\Request;
use App\Models\siswa;
use App\Models\jadwal;
use App\Models\kelas;
use App\Models\KelasSiswa;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = kelas::all();
        $jadwal = jadwal::all();

        $siswa = siswa::all();
        // dd($siswa['alamat']);




        // $siswa = Siswa::whereHas('kelas', function ($query) use ($idKelas) {
        //     $query->where('id', $idKelas);
        // })
        // ->get();


        return view('absensi.index', compact('siswa', 'jadwal', 'kelas'));
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
    public function show(absensi $absensi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(absensi $absensi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, absensi $absensi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(absensi $absensi)
    {
        //
    }
}
