<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PegawaiIzin;
use App\Models\PegawaiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PegawaiIzinController extends Controller
{
    public function index()
    {
        $izins = PegawaiIzin::with('pegawai')->latest()->paginate(10);
        return view('attendance.izin.index', compact('izins'));
    }

    public function create()
    {
        $pegawais = Pegawai::orderBy('nama')->get(); // Assumption: 'nama' column
        return view('attendance.izin.create', compact('pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_izin' => 'required|in:Sakit,Izin,Cuti,Dinas Luar',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'bukti_dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $path = null;
            if ($request->hasFile('bukti_dokumen')) {
                $path = $request->file('bukti_dokumen')->store('izin_docs', 'public');
            }

            $izin = PegawaiIzin::create([
                'pegawai_id' => $request->pegawai_id,
                'jenis_izin' => $request->jenis_izin,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'keterangan' => $request->keterangan,
                'bukti_dokumen' => $path,
                'status_approval' => 'Approved', // Auto-approved for admin input
            ]);

            // Sync with PegawaiAbsensi
            $this->syncToAttendance($izin);

            DB::commit();
            return redirect()->route('attendance.izin.index')->with('success', 'Data izin berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($path) Storage::disk('public')->delete($path);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy(PegawaiIzin $izin)
    {
        DB::beginTransaction();
        try {
            // Restore attendance status to null or recalculate?
            // For simplicity, we delete the attendance records for these dates so they can be recalculated or stay empty
            // Or better, set status back to null/Alpha if past?
            // Let's delete the specific records created by this Izin logic. 
            // BUT, what if there was attendance log? Real attendance > Izin?
            // Strategy: Loop dates and delete/update.
            
            $start = $izin->tanggal_mulai;
            $end = $izin->tanggal_akhir;
            
            PegawaiAbsensi::where('pegawai_id', $izin->pegawai_id)
                ->whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                ->whereIn('status', ['Sakit', 'Izin', 'Cuti', 'Dinas Luar']) // Only delete if status matches permission types
                ->delete();

            if ($izin->bukti_dokumen) {
                Storage::disk('public')->delete($izin->bukti_dokumen);
            }
            
            $izin->delete();

            DB::commit();
            return redirect()->route('attendance.izin.index')->with('success', 'Data izin berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function syncToAttendance(PegawaiIzin $izin)
    {
        $start = $izin->tanggal_mulai;
        $end = $izin->tanggal_akhir;
        $pegawai = $izin->pegawai;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            
            // Financial Logic based on Proposal
            $nominalGaji = 0; // Default 0 for Izin
            $nominalMakan = 0; // Default 0 for Sakit, Izin, Cuti
            
            // Fetch Rule for Salary Info (to get base salary)
            // Need simplified way to get daily salary. AttendanceService has this logic.
            // For now, we fetch rule allocation manually or check if we store salary in Pegawai.
            // Assuming Rule has the salary.
            
            $allocation = $pegawai->ruleAllocations()
                ->whereHas('tahun', function($q) use ($date) {
                    $q->where('tanggalmulai', '<=', $date->toDateString())
                      ->where('tanggalakhir', '>=', $date->toDateString());
                })
                ->orWhereHas('tahun', function($q) {
                    $q->where('isActive', 1);
                })
                ->with('attendanceRule')
                ->first();

            $gajiHarian = $allocation?->attendanceRule?->gaji_harian ?? 0;
            $uangMakan = $allocation?->attendanceRule?->bantuan_makan ?? 0;

            if ($izin->jenis_izin === 'Sakit') {
                $nominalGaji = $gajiHarian;
                $nominalMakan = 0;
            } elseif ($izin->jenis_izin === 'Cuti') {
                $nominalGaji = $gajiHarian;
                $nominalMakan = 0;
            } elseif ($izin->jenis_izin === 'Dinas Luar') {
                $nominalGaji = $gajiHarian;
                $nominalMakan = $uangMakan;
            } elseif ($izin->jenis_izin === 'Izin') {
                $nominalGaji = 0;
                $nominalMakan = 0;
            }

            PegawaiAbsensi::updateOrCreate(
                [
                    'pegawai_id' => $izin->pegawai_id,
                    'tanggal' => $date->format('Y-m-d'),
                ],
                [
                    'jam_masuk' => null, // No scan
                    'jam_pulang' => null,
                    'durasi_kerja' => null,
                    'status' => $izin->jenis_izin,
                    'nominal_gaji' => $nominalGaji,
                    'nominal_makan' => $nominalMakan,
                    'total_honor' => $nominalGaji + $nominalMakan, // No penalty
                ]
            );
        }
    }
}
