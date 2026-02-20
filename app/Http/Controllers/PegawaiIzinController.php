<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PegawaiIzin;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PegawaiIzinController extends Controller
{
    protected $attendanceService;

    /**
     * Dependency Injection: Memasukkan otak (Service) ke dalam tangan (Controller)
     */
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $izins = PegawaiIzin::with('pegawai')->latest()->paginate(10);
        return view('attendance.izin.index', compact('izins'));
    }

    public function create()
    {
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->pluck('name', 'id');
        return view('attendance.izin.create', compact('pegawais'));
    }

    /**
     * Menyimpan data Izin dan memicu penghitungan ulang absensi
     */
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
                'status_approval' => 'Approved',
            ]);

            // Clean Code: Panggil Service untuk hitung ulang absensi dalam rentang tanggal izin
            $this->recomputeAttendance($izin);

            DB::commit();
            return redirect()->route('attendance.izin.index')->with('success', 'Data izin berhasil disimpan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan Izin: " . $e->getMessage());
            if ($path) Storage::disk('public')->delete($path);
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy(PegawaiIzin $izin)
    {
        DB::beginTransaction();
        try {
            // Sebelum dihapus, ambil data pegawai dan rentang tanggalnya
            $pegawai = $izin->pegawai;
            $start = $izin->tanggal_mulai;
            $end = $izin->tanggal_akhir;

            if ($izin->bukti_dokumen) {
                Storage::disk('public')->delete($izin->bukti_dokumen);
            }
            
            $izin->delete();

            // Picu hitung ulang: Sekarang sistem akan melihat tidak ada izin lagi,
            // dan akan mengembalikan status ke Alpha (jika wajib hadir) atau Kosong.
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $this->attendanceService->calculateDailyAttendance($pegawai, $date->format('Y-m-d'));
            }

            DB::commit();
            return redirect()->route('attendance.izin.index')->with('success', 'Data izin berhasil dihapus & absensi dihitung ulang.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus Izin: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk menghitung ulang absensi dalam rentang waktu tertentu
     */
    private function recomputeAttendance(PegawaiIzin $izin)
    {
        $start = $izin->tanggal_mulai;
        $end = $izin->tanggal_akhir;
        $pegawai = $izin->pegawai;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $this->attendanceService->calculateDailyAttendance($pegawai, $date->format('Y-m-d'));
        }
    }
}

