<?php

namespace App\Http\Controllers;

use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalPiketController extends Controller
{
    public function index()
    {
        $tahunAktif = tahun::aktif()->first();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        $piketSchedule = [];
        if ($tahunAktif) {
            $piketSchedule = JadwalPiket::with('pegawai')
                ->where('tahun_id', $tahunAktif->id)
                ->get()
                ->groupBy('hari');
        }

        $allPegawai = Pegawai::orderBy('name')->get();

        return view('jadwal_piket.index', compact('piketSchedule', 'days', 'allPegawai', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        ]);

        $tahunAktif = tahun::aktif()->first();
        if (!$tahunAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // Prevent duplicate for same teacher on same day
        $exists = JadwalPiket::where('pegawai_id', $request->pegawai_id)
            ->where('hari', $request->hari)
            ->where('tahun_id', $tahunAktif->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Guru sudah dijadwalkan piket pada hari tersebut.');
        }

        JadwalPiket::create([
            'pegawai_id' => $request->pegawai_id,
            'hari' => $request->hari,
            'tahun_id' => $tahunAktif->id
        ]);

        return back()->with('success', 'Jadwal piket berhasil ditambahkan');
    }

    public function destroy($id)
    {
        JadwalPiket::destroy($id);
        return back()->with('success', 'Jadwal piket dihapus');
    }
}
