<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HariLiburController extends Controller
{
    public function index()
    {
        $tahun = Tahun::aktif()->first();
        $liburMingguan = HariLibur::active()
            ->forYear($tahun->id ?? null)
            ->whereNotNull('hari')
            ->get()
            ->keyBy('hari');
            
        $liburKhusus = HariLibur::active()
            ->forYear($tahun->id ?? null)
            ->whereNull('hari')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('jadwal.libur', compact('tahun', 'liburMingguan', 'liburKhusus'));
    }

    public function updateWeekly(Request $request)
    {
        try {
            $selectedDays = $request->input('days', []); // Array of day names e.g., ['Minggu']
            $tahunId = $request->input('tahun_id');
            // Allow setting a validity period for this weekly rule
            $tanggalMulai = $request->input('tanggal_mulai');
            $tanggalAkhir = $request->input('tanggal_akhir');

            $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

            foreach ($allDays as $day) {
                $isActive = in_array($day, $selectedDays);
                
                HariLibur::updateOrCreate(
                    [
                        'tahun_id' => $tahunId,
                        'hari' => $day
                    ],
                    [
                        'keterangan' => 'Libur Mingguan',
                        'is_active' => $isActive,
                        // Update validity dates (or set to null if empty, effectively "forever")
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_akhir' => $tanggalAkhir,
                    ]
                );
            }

            return redirect()->back()->with('success', 'Pengaturan libur mingguan berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error updating weekly holidays: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'tahun_id' => 'nullable|exists:tahuns,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            HariLibur::create([
                'keterangan' => $request->keterangan,
                'tahun_id' => $request->tahun_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir ?? $request->tanggal_mulai, // Default to single day if no end date
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error adding holiday: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan hari libur: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
            'tahun_id' => 'nullable|exists:tahuns,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        try {
            $libur = HariLibur::findOrFail($id);
            $libur->update([
                'keterangan' => $request->keterangan,
                'tahun_id' => $request->tahun_id,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir ?? $request->tanggal_mulai,
            ]);

            return redirect()->back()->with('success', 'Hari libur berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating holiday: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui hari libur: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $libur = HariLibur::findOrFail($id);
            $libur->delete();
            return redirect()->back()->with('success', 'Hari libur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
