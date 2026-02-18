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
        $pegawais = Pegawai::where('aktif', 'Aktif')
            ->orderBy('name')
            ->with(['mandatorySchedules' => function($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('tahun_id', $activeYear->id);
                }
            }])
            ->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Fetch Automatic Schedules (Teaching & Picket)
        // Grouped by Pegawai ID => Array of Days
        $automaticDays = [];

        foreach ($pegawais as $pegawai) {
            $auto = [];
            
            // 1. Get Teaching Schedule (Jadwal)
            $jadwalQuery = \App\Models\Jadwal::where('pegawai_id', $pegawai->id);
            if ($activeYear) {
                $jadwalQuery->where('tahun_id', $activeYear->id);
            }
            $jadwals = $jadwalQuery->pluck('hari')->toArray();
            foreach($jadwals as $hari) {
                $auto[] = ucfirst(strtolower($hari));
            }

            // 2. Get Picket Schedule (JadwalPiket)
            $piketQuery = \App\Models\JadwalPiket::where('pegawai_id', $pegawai->id);
            if ($activeYear) {
                $piketQuery->where('tahun_id', $activeYear->id);
            }
            $pikets = $piketQuery->pluck('hari')->toArray();
            foreach($pikets as $hari) {
                $auto[] = ucfirst(strtolower($hari));
            }

            $automaticDays[$pegawai->id] = array_unique($auto);
        }

        return view('attendance.mandatory.index', compact('pegawais', 'days', 'automaticDays', 'activeYear'));
    }

    public function store(Request $request)
    {
        $data = $request->input('wajib_hadir', []);
        
        // Get Active Year
        $activeYear = \App\Models\Tahun::aktif()->first();
        if (!$activeYear) {
            return redirect()->route('attendance.mandatory.index')->with('error', 'Tidak ada Tahun Ajaran aktif.');
        }

        \DB::transaction(function () use ($data, $activeYear) {
            // Get all active employees (to handle unchecking all days)
            $allPegawaiIds = Pegawai::where('aktif', 'Aktif')->pluck('id');

            foreach ($allPegawaiIds as $pegawaiId) {
                // Determine days selected for this employee
                $days = isset($data[$pegawaiId]) ? $data[$pegawaiId] : [];

                // 1. Delete existing mandatory schedule for this employee AND this year
                \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawaiId)
                    ->where('tahun_id', $activeYear->id)
                    ->delete();

                // 2. Insert new days
                foreach ($days as $day) {
                    \App\Models\PegawaiWajibHadir::create([
                        'pegawai_id' => $pegawaiId,
                        'tahun_id' => $activeYear->id,
                        'hari' => $day
                    ]);
                }
            }
        });

        return redirect()->route('attendance.mandatory.index')->with('success', 'Jadwal wajib hadir berhasil disimpan (Tahun Ajaran: ' . $activeYear->tahun . ' ' . $activeYear->semester . ').');
    }
}
