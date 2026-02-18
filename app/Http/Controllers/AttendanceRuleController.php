<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRule;
use Illuminate\Http\Request;

class AttendanceRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeYear = \App\Models\Tahun::where('isActive', 1)->first();
        
        $rules = AttendanceRule::withCount(['pegawais' => function ($query) use ($activeYear) {
            if ($activeYear) {
                // Filter relation count strictly by the active year allocation
                $query->where('pegawai_rule_allocations.tahun_id', $activeYear->id);
            }
        }])->paginate(10);

        return view('attendance.rules.index', compact('rules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attendance.rules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'scan_masuk_start' => 'required|date_format:H:i',
            'scan_pulang_end' => 'required|date_format:H:i',
            'toleransi_telat' => 'required|integer|min:0',
            'bantuan_makan' => 'required|numeric|min:0',
            'gaji_harian' => 'required|numeric|min:0',
            'gaji_per_jam' => 'nullable|numeric|min:0',
            'denda_telat' => 'required|numeric|min:0',
            'hari_kerja' => 'required|array',
            'hari_kerja.*' => 'string',
        ]);

        AttendanceRule::create($validated);

        return redirect()->route('attendance.rules.index')
            ->with('type', 'success')
            ->with('message', 'Aturan kehadiran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRule $attendanceRule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRule $attendanceRule)
    {
        // Route binding handles retrieval
        return view('attendance.rules.edit', compact('attendanceRule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceRule $attendanceRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'scan_masuk_start' => 'required|date_format:H:i',
            'scan_pulang_end' => 'required|date_format:H:i',
            'toleransi_telat' => 'required|integer|min:0',
            'bantuan_makan' => 'required|numeric|min:0',
            'gaji_harian' => 'required|numeric|min:0',
            'gaji_per_jam' => 'nullable|numeric|min:0',
            'denda_telat' => 'required|numeric|min:0',
            'hari_kerja' => 'required|array',
            'hari_kerja.*' => 'string',
        ]);

        $attendanceRule->update($validated);

        return redirect()->route('attendance.rules.index')
            ->with('type', 'success')
            ->with('message', 'Aturan kehadiran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceRule $attendanceRule)
    {
        if ($attendanceRule->pegawais()->count() > 0) {
            return back()->with('type', 'error')->with('message', 'Gagal hapus: Masih ada pegawai yang menggunakan aturan ini.');
        }

        $attendanceRule->delete();

        return redirect()->route('attendance.rules.index')
            ->with('type', 'success')
            ->with('message', 'Aturan kehadiran berhasil dihapus.');
    }
}
