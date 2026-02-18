<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PegawaiWajibHadirController extends Controller
{
    public function index()
    {
        $activeYear = \App\Models\Tahun::where('isActive', 1)->first();
        if (!$activeYear) {
            return redirect()->back()->with('type', 'error')->with('message', 'Tahun ajaran aktif tidak ditemukan.');
        }

        $pegawais = \App\Models\Pegawai::where('aktif', 'Aktif')
            ->orderBy('name')
            ->with(['wajibHadirs' => function($q) use ($activeYear) {
                $q->where('tahun_id', $activeYear->id);
            }])
            ->get();

        // Fetch Schedules (Jadwal & Piket) for Auto-Check
        // Use flat string keys "pegawai_id-hari" for reliable lookup in Blade
        $autoSchedules = [];

        $jadwals = \App\Models\Jadwal::where('tahun_id', $activeYear->id)->get(['pegawai_id', 'hari']);
        foreach ($jadwals as $jadwal) {
            $autoSchedules[$jadwal->pegawai_id . '-' . $jadwal->hari] = true;
        }

        $pikets = \App\Models\JadwalPiket::where('tahun_id', $activeYear->id)->get(['pegawai_id', 'hari']);
        foreach ($pikets as $piket) {
            $autoSchedules[$piket->pegawai_id . '-' . $piket->hari] = true;
        }

        return view('attendance.wajib-hadir.index', compact('pegawais', 'autoSchedules', 'activeYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'manual_days' => 'array', // [pegawai_id => [days...]]
        ]);

        $activeYear = \App\Models\Tahun::where('isActive', 1)->first();
        if (!$activeYear) return back();

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Logic:
            // 1. We ONLY touch manual entries. 
            // 2. But wait, the table stores mixed data (Auto + Manual)?
            //    Or does the table only store "Checklist Result"?
            //    Best approach: The table stores THE TRUTH (Official Wajib Hadir).
            //    So we must merge Auto + Manual and save ALL as 'Wajib Hadir'.
            //    Why? Because AttendanceService only checks this table. It doesn't check Jadwal/Piket again (performance).
            
            // However, the UI sends "manual_days" (checkboxes).
            // Auto days are disabled checkboxes, so they might POST or might NOT depending on implementation.
            // If disabled, they don't post. We must Re-Calculate Auto Days here to be safe.

            // A. Get Assignments from DB (Jadwal/Piket)
            $autoSchedules = [];
            $jadwals = \App\Models\Jadwal::where('tahun_id', $activeYear->id)->get(['pegawai_id', 'hari']);
            foreach ($jadwals as $jadwal) {
                $autoSchedules[$jadwal->pegawai_id][] = $jadwal->hari;
            }
            $pikets = \App\Models\JadwalPiket::where('tahun_id', $activeYear->id)->get(['pegawai_id', 'hari']);
            foreach ($pikets as $piket) {
                $autoSchedules[$piket->pegawai_id][] = $piket->hari;
            }

            // B. Prepare Data to Sync
            // Strategy: Clear existing for this year, then Insert distinct merged days.
            // Note: This is heavy if many employees. Optimizing by looping inputs is better?
            // But we need to handle "Uncheck" behavior (deletion).
            // So Deleting all for active year and Re-inserting is safest for bulk update.
            
            \App\Models\PegawaiWajibHadir::where('tahun_id', $activeYear->id)->delete();

            $pegawais = \App\Models\Pegawai::where('aktif', 'Aktif')->pluck('id');
            $inserts = [];
            $now = now();

            foreach ($pegawais as $pegawaiId) {
                $days = [];

                // 1. Add Auto Days
                if (isset($autoSchedules[$pegawaiId])) {
                    $days = array_merge($days, $autoSchedules[$pegawaiId]);
                }

                // 2. Add Manual Days (from Form)
                if ($request->has("manual_days.$pegawaiId")) {
                    $manual = $request->input("manual_days.$pegawaiId");
                    if (is_array($manual)) {
                        $days = array_merge($days, $manual);
                    }
                }

                // 3. Unique & Insert
                $days = array_unique($days);
                foreach ($days as $day) {
                    $inserts[] = [
                        'pegawai_id' => $pegawaiId,
                        'tahun_id' => $activeYear->id,
                        'hari' => $day,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            // Bulk Insert (Chunk if necessary, but assuming fits in memory for now)
            foreach (array_chunk($inserts, 1000) as $chunk) {
                \App\Models\PegawaiWajibHadir::insert($chunk);
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('type', 'success')->with('message', 'Jadwal wajib hadir berhasil diperbarui.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
