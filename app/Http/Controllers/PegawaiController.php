<?php

namespace App\Http\Controllers;

use App\Exports\PegawaiExport;
use App\Imports\PegawaiImport;
use App\Models\pegawai;
use App\Models\AttendanceRule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $search = $request->input('search');

        $query = pegawai::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        // Whitelist kolom sorting untuk keamanan
        $allowedSorts = ['name', 'nuptk', 'email', 'status', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pegawai = $query->paginate($perPage)->withQueryString();
        
        return view('pegawai.index', compact('pegawai'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pegawai.input-pegawai');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nuptk' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('uploads/pegawai', 'public');
        }

        pegawai::create($data);
        return redirect()->route('pegawai.index')->with('message', 'Data Pegawai berhasil disimpan')->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(pegawai $pegawai)
    {
        $year = now()->year;

        // 1. Statistik Tahunan (Summary - Tahun Ini)
        $summaryStats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0, 'Total' => 0];
        
        // Query Absensi Filter Tahun Ini
        $logs = \App\Models\PegawaiAbsensi::where('pegawai_id', $pegawai->id)
            ->whereYear('tanggal', $year)
            ->get();

        foreach ($logs as $log) {
            $status = $log->status;
            // Normalize status strings if necessary (e.g. "Hadir " -> "Hadir")
            $status = trim($status);
            
            if ($status === 'Telat') {
                $summaryStats['Hadir']++;
            } elseif (isset($summaryStats[$status])) {
                $summaryStats[$status]++;
            } else {
                 // Fallback for unexpected status, treat as Hadir or separate? 
                 // For now, let's just log it or ignore
            }
            $summaryStats['Total']++;
        }

        // 2. Data Grafik Bulanan (Monthly Trend - Tahun Ini)
        $months = range(1, 12);
        $chartData = [
            'Hadir' => [], 'Sakit' => [], 'Izin' => [], 'Alpha' => []
        ];

        foreach ($months as $m) {
            $monthLogs = $logs->filter(function($log) use ($m) {
                return $log->tanggal->month == $m;
            });

            $chartData['Hadir'][] = $monthLogs->whereIn('status', ['Hadir', 'Telat'])->count();
            $chartData['Sakit'][] = $monthLogs->where('status', 'Sakit')->count();
            $chartData['Izin'][] = $monthLogs->where('status', 'Izin')->count();
            $chartData['Alpha'][] = $monthLogs->where('status', 'Alpha')->count();
        }

        // 3. Riwayat Terbaru (Top 10)
        $recentLogs = $logs->sortByDesc('tanggal')->take(10);

        // 4. Smart Prediction (Optional/Simple Version)
        $prediction = [
            'status' => 'Aman',
            'color' => 'green',
            'message' => 'Tingkat kehadiran pegawai sangat baik.',
            'predicted_score' => 100
        ];

        if ($summaryStats['Total'] > 0) {
            $attendanceRate = ($summaryStats['Hadir'] / $summaryStats['Total']) * 100;
            $prediction['predicted_score'] = round($attendanceRate, 1);

            if ($attendanceRate < 80) {
                $prediction['status'] = 'Perlu Evaluasi';
                $prediction['color'] = 'red';
                $prediction['message'] = 'Tingkat kehadiran di bawah 80%.';
            } elseif ($attendanceRate < 90) {
                $prediction['status'] = 'Waspada';
                $prediction['color'] = 'yellow';
                $prediction['message'] = 'Tingkat kehadiran perlu ditingkatkan.';
            }
        }

        return view('pegawai.show', compact('pegawai', 'summaryStats', 'chartData', 'recentLogs', 'year', 'prediction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pegawai = pegawai::findOrFail($id);
        return view('pegawai.input-pegawai', compact('pegawai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pegawai = pegawai::findOrFail($id); // Ensure model is loaded

        $request->validate([
            'name' => 'required',
            'nuptk' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($pegawai->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($pegawai->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('uploads/pegawai', 'public');
        }

        $pegawai->update($data);
        return redirect()->route('pegawai.index')->with('message', 'Data Pegawai berhasil diperbarui')->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {        
        $peg=pegawai::findOrFail($id);
        $peg->delete();

return redirect('pegawai')->with('message', 'Data Berhasil Dihapus')->with('type', 'error');
    }
    public function Addpegawai()
    {
        return view('pegawai.Addpegawai');
    }
    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        $file=$request->file('file')->store('public/import');
        Excel::import(new PegawaiImport, $file);
        // return redirect('/pegawai')->with('success','Data berhasil di import');
        return redirect('/pegawai')->with('message','Data berhasil di import')->with('type','success');

    }
     public function export(){
        return Excel::download(new PegawaiExport, 'pegawai.xlsx');

     }
}
