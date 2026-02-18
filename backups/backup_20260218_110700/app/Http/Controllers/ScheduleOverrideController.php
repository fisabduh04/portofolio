<?php

namespace App\Http\Controllers;

use App\Models\PegawaiScheduleOverride;
use App\Models\Pegawai;
use App\Models\AttendanceRule;
use Illuminate\Http\Request;

class ScheduleOverrideController extends Controller
{
    public function index()
    {
        $overrides = PegawaiScheduleOverride::with(['pegawai', 'attendanceRule'])
            ->latest('date')
            ->paginate(15);
            
        return view('attendance.overrides.index', compact('overrides'));
    }

    public function create()
    {
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->get();
        $rules = AttendanceRule::all();
        return view('attendance.overrides.create', compact('pegawais', 'rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'attendance_rule_id' => 'required|exists:attendance_rules,id',
            'date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        // Check uniqueness
        $exists = PegawaiScheduleOverride::where('pegawai_id', $request->pegawai_id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->withErrors(['date' => 'Pegawai ini sudah memiliki override jadwal pada tanggal tersebut.']);
        }

        PegawaiScheduleOverride::create($validated);

        return redirect()->route('attendance.overrides.index')
            ->with('type', 'success')
            ->with('message', 'Jadwal khusus berhasil disimpan.');
    }

    public function destroy(PegawaiScheduleOverride $scheduleOverride)
    {
        $scheduleOverride->delete();
        return redirect()->route('attendance.overrides.index')
            ->with('type', 'success')
            ->with('message', 'Jadwal khusus berhasil dihapus.');
    }
}
