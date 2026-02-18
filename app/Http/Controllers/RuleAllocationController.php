<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\AttendanceRule;
use App\Models\PegawaiRuleAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RuleAllocationController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Selected Year (Default to Active)
        $activeYear = Tahun::aktif()->first();
        $selectedTahunId = $request->input('tahun_id', $activeYear ? $activeYear->id : null);
        
        $selectedYear = Tahun::find($selectedTahunId);
        $tahuns = Tahun::orderBy('tanggalmulai', 'desc')->get();
        $rules = AttendanceRule::all();

        // 2. Fetch Pegawais with their allocation for the SELECTED year
        $pegawais = Pegawai::where('aktif', 'Aktif')
            ->orderBy('name')
            ->with(['ruleAllocations' => function($q) use ($selectedTahunId) {
                $q->where('tahun_id', $selectedTahunId);
            }])
            ->get();

        return view('attendance.rules.allocation', compact('pegawais', 'tahuns', 'rules', 'selectedYear'));
    }

    public function store(Request $request)
    {
        $tahunId = $request->input('tahun_id');
        $allocations = $request->input('allocations', []); // [pegawai_id => rule_id]

        if (!$tahunId) {
            return back()->with('error', 'Tahun Ajaran tidak valid.');
        }

        DB::transaction(function () use ($tahunId, $allocations) {
            foreach ($allocations as $pegawaiId => $ruleId) {
                if ($ruleId) {
                    // Update or Create Allocation
                    PegawaiRuleAllocation::updateOrCreate(
                        [
                            'pegawai_id' => $pegawaiId,
                            'tahun_id' => $tahunId,
                        ],
                        [
                            'attendance_rule_id' => $ruleId
                        ]
                    );
                } else {
                    // If rule_id is empty (e.g. "Use Default" selected), delete the allocation?
                    // Or should we enforce a rule? 
                    // To ensure historical integrity, it's safer to Keep the record.
                    // But if user wants to "Reset" to use the Master Rule (dynamic), they might want to delete.
                    // Let's allow deletion for flexibility.
                    PegawaiRuleAllocation::where('pegawai_id', $pegawaiId)
                        ->where('tahun_id', $tahunId)
                        ->delete();
                }
            }
        });

        return back()->with('success', 'Alokasi Aturan berhasil disimpan.');
    }
}
