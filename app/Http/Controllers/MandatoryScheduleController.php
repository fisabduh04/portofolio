<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class MandatoryScheduleController extends Controller
{
    public function index()
    {
        $activeYear = \App\Models\Tahun::aktif()->first();
        
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        $pegawais = Pegawai::where('aktif', 'Aktif')
            ->with(['wajibHadirs' => function($query) use ($activeYear) {
                $query->where('tahun_id', $activeYear->id);
            }])
            ->orderBy('name')
            ->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Fetch Automatic Schedules (Teaching & Picket)
        $automaticDays = [];

        foreach ($pegawais as $pegawai) {
            $auto = [];
            
            // 1. Get Teaching Schedule (Jadwal)
            $jadwals = \App\Models\Jadwal::where('pegawai_id', $pegawai->id)->pluck('hari')->toArray();
            foreach($jadwals as $hari) {
                $auto[] = ucfirst(strtolower($hari));
            }

            // 2. Get Picket Schedule (JadwalPiket)
            $pikets = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)->pluck('hari')->toArray();
            foreach($pikets as $hari) {
                $auto[] = ucfirst(strtolower($hari));
            }

            $automaticDays[$pegawai->id] = array_unique($auto);
        }

        return view('attendance.mandatory.index', compact('pegawais', 'days', 'automaticDays', 'activeYear'));
    }

    public function store(Request $request)
    {
        $activeYear = \App\Models\Tahun::aktif()->first();
        
        if (!$activeYear) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        // Data structure from form: wajib_hadir[pegawai_id][] = "Senin"
        $wajibHadirInput = $request->input('wajib_hadir', []);

        // Get all active employees to sync
        $pegawais = Pegawai::where('aktif', 'Aktif')->get();

        \Illuminate\Support\Facades\DB::transaction(function() use ($pegawais, $wajibHadirInput, $activeYear) {
            foreach ($pegawais as $pegawai) {
                // 1. Get days selected for this employee
                $selectedDays = $wajibHadirInput[$pegawai->id] ?? [];

                // 2. Delete existing records for this employee in the active year
                \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawai->id)
                    ->where('tahun_id', $activeYear->id)
                    ->delete();

                // 3. Create new records
                foreach ($selectedDays as $day) {
                    \App\Models\PegawaiWajibHadir::create([
                        'pegawai_id' => $pegawai->id,
                        'tahun_id' => $activeYear->id,
                        'hari' => $day
                    ]);
                }
            }
        });

        return redirect()->route('attendance.mandatory.index')->with('success', 'Jadwal Wajib berhasil diperbarui.');
    }
}
