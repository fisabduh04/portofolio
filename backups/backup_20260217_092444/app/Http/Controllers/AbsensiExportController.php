<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Exports\RekapBulananExport;
use App\Exports\RekapHarianExport;
use App\Exports\RekapTahunanExport;
use App\Models\Kelas;
use App\Traits\AbsensiRekapTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiExportController extends Controller
{
    use AbsensiRekapTrait;

    public function exportHarian(Request $request)
    {
        return Excel::download(new AbsensiExport($request->all(), 'harian'), 'rekap_harian_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportBulanan(Request $request)
    {
        return Excel::download(new AbsensiExport($request->all(), 'bulanan'), 'rekap_bulanan_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportRekapHarian(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $kelasId = $request->input('kelas_id');
        $pegawaiId = $request->input('pegawai_id');
        $typeGuru = $request->input('type_guru');
        $format = $request->input('format', 'excel');

        if (!$kelasId) {
            return back()->with('error', 'Silakan pilih kelas terlebih dahulu.');
        }

        $data = $this->getPrivateRekapData($date, $kelasId, $pegawaiId, $typeGuru);
        $rekapData = $data['rekapData'];
        $kelas = Kelas::find($kelasId);
        $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

        if ($format === 'pdf') {
            return view('absensi.print_rekap_harian', [
                'rekapData' => $rekapData,
                'date' => $date,
                'kelas' => $kelasName,
                'typeGuru' => $typeGuru
            ]);
        }

        return Excel::download(new RekapHarianExport($rekapData, $date, $kelasName, $typeGuru), 'rekap_harian_' . $date . '.xlsx');
    }

    public function exportRekapBulanan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $format = $request->input('format', 'excel');
        $typeGuru = $request->input('type_guru', 'mapel');

        if (!$kelasId) return back()->with('error', 'Pilih kelas terlebih dahulu.');

        $data = $this->getPrivateRekapBulananData($month, $year, $kelasId, $typeGuru);
        $kelas = Kelas::find($kelasId);
        $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

        if ($format === 'pdf') {
             return view('absensi.print_rekap_bulanan', [
                'rekapData' => $data['rekapData'],
                'dates' => $data['dates'],
                'month' => $month,
                'year' => $year,
                'kelas' => $kelasName
            ]);
        }
        
        return Excel::download(new RekapBulananExport($data['rekapData'], $data['dates'], $month, $year, $kelasName, $typeGuru), 'rekap_bulanan_' . $month . '_' . $year . '.xlsx');
    }

    public function exportRekapTahunan(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $year = $request->input('year', now()->year);
        $format = $request->input('format', 'excel');
        $typeGuru = $request->input('type_guru', 'mapel');

        if (!$kelasId) return back()->with('error', 'Pilih kelas terlebih dahulu.');

         $data = $this->getPrivateRekapTahunanData($year, $kelasId, $typeGuru);
         $kelas = Kelas::find($kelasId);
         $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

         if ($format === 'pdf') {
             return view('absensi.print_rekap_tahunan', [
                'rekapData' => $data['rekapData'],
                'year' => $year,
                'kelas' => $kelasName
            ]);
        }

        return Excel::download(new RekapTahunanExport($data['rekapData'], $year, $kelasName, $typeGuru), 'rekap_tahunan_' . $year . '.xlsx');
    }

    public function exportRekapPeriode(Request $request)
    {
        $kelasId = $request->input('kelas_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $typeGuru = $request->input('type_guru', 'mapel');
        
        if (!$kelasId) return back()->with('error', 'Pilih kelas terlebih dahulu.');

        $kelas = Kelas::find($kelasId);
        $kelasName = $kelas ? $kelas->kelas : 'Semua Kelas';

        return Excel::download(new AbsensiExport(['kelas_id' => $kelasId, 'start_date' => $startDate, 'end_date' => $endDate, 'type_guru' => $typeGuru], 'periode'), 'rekap_periode_' . $startDate . '_to_' . $endDate . '.xlsx');
    }
}
