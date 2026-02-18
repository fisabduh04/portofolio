<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Get all Pegawai with attendance data for the month
        $pegawais = Pegawai::where('aktif', 'Aktif')
            ->with([
                'ruleAllocations' => function($query) use ($year) {
                    $query->whereHas('tahun', function($q) use ($year) {
                        $q->where('tahun', $year);
                    })->with('attendanceRule');
                }, 
                'pegawaiAbsensis' => function ($query) use ($month, $year) {
                    $query->whereMonth('tanggal', $month)
                          ->whereYear('tanggal', $year);
                }
            ])
            ->get()
            ->map(function ($pegawai) {
                // Use the eager loaded relation
                $absensi = $pegawai->pegawaiAbsensis;

                $totalGaji = $absensi->sum('nominal_gaji');
                $totalMakan = $absensi->sum('nominal_makan');
                // Total should ideally count deduction logic too if stored in total_honor directly
                // Our Service stores final total_honor which includes deduction.
                // But let's sum total_honor for accuracy.
                $grandTotal = $absensi->sum('total_honor');
                
                $hadirCount = $absensi->where('status', 'Hadir')->count();
                $telatCount = $absensi->where('status', 'Telat')->count();
                $alphaCount = $absensi->where('status', 'Alpha')->count();

                return [
                    'pegawai' => $pegawai,
                    'hadir_count' => $hadirCount,
                    'telat_count' => $telatCount,
                    'alpha_count' => $alphaCount,
                    'total_gaji' => $totalGaji,
                    'total_makan' => $totalMakan,
                    'grand_total' => $grandTotal,
                ];
            });

        return view('attendance.payroll.index', compact('pegawais', 'month', 'year'));
    }

    public function slip(Request $request, $id)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        $pegawai = Pegawai::with(['ruleAllocations' => function($query) use ($year) {
            $query->whereHas('tahun', function($q) use ($year) {
                $q->where('tahun', $year);
            })->with('attendanceRule');
        }])->findOrFail($id);
        
        $absensi = PegawaiAbsensi::where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal')
            ->get();

        $summary = [
            'hadir' => $absensi->where('status', 'Hadir')->count(),
            'telat' => $absensi->where('status', 'Telat')->count(),
            'alpha' => $absensi->where('status', 'Alpha')->count(),
            'total_gaji' => $absensi->sum('nominal_gaji'),
            'total_makan' => $absensi->sum('nominal_makan'),
            'grand_total' => $absensi->sum('total_honor'),
        ];
        
        // Calculate total deductions manually for display if needed
        // Deduction = (Gaji + Makan) - Total Honor (roughly)
        // Or we can just display what we have.

        return view('attendance.payroll.slip', compact('pegawai', 'absensi', 'summary', 'month', 'year'));
    }

    public function printSlip(Request $request, $id)
    {
         // Logic for PDF download if needed, or just print window.print()
         // For now reuse the slip view with a print layout
         return $this->slip($request, $id);
    }
}
