<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class MandatoryScheduleController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Fetch Automatic Schedules (Teaching & Picket)
        // Grouped by Pegawai ID => Array of Days
        $automaticDays = [];

        foreach ($pegawais as $pegawai) {
            $auto = [];
            
            // 1. Get Teaching Schedule (Jadwal)
            // Assuming relationship exists or we query directly
            $jadwals = \App\Models\Jadwal::where('pegawai_id', $pegawai->id)->pluck('hari')->toArray();
            foreach($jadwals as $hari) {
                // Ensure Proper Case (Senin, not senin) just in case
                $auto[] = ucfirst(strtolower($hari));
            }

            // 2. Get Picket Schedule (JadwalPicket)
            $pikets = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id)->pluck('hari')->toArray();
            foreach($pikets as $hari) {
                $auto[] = ucfirst(strtolower($hari));
            }

            $automaticDays[$pegawai->id] = array_unique($auto);
        }

        return view('attendance.mandatory.index', compact('pegawais', 'days', 'automaticDays'));
    }

    public function store(Request $request)
    {
        // Data structure: wajib_hadir[pegawai_id][] = "Senin"
        $data = $request->input('wajib_hadir', []);

        // Get all active employees to ensure we clear those who have NO days checked
        $pegawais = Pegawai::where('aktif', 'Aktif')->get();

        foreach ($pegawais as $pegawai) {
            // If the employee ID exists in the input array, use their selected days.
            // If not (no days checked), use an empty array.
            $days = isset($data[$pegawai->id]) ? $data[$pegawai->id] : [];
            
            $pegawai->update(['wajib_hadir' => $days]);
        }

        return redirect()->route('attendance.mandatory.index')->with('success', 'Jadwal Wajib berhasil diperbarui.');
    }
}
