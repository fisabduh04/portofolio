<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use App\Models\SpecialEvent;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiPegawaiReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Data
        $pegawaiId = $request->input('pegawai_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // 2. Data Pegawai untuk Dropdown
        $pegawais = Pegawai::orderBy('name')->get();
        $years = range(date('Y') - 1, date('Y') + 1);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $attendanceData = [];
        $selectedPegawai = null;

        // 3. Jika Filter Dipilih
        if ($pegawaiId) {
            $selectedPegawai = Pegawai::find($pegawaiId);
            
            if ($selectedPegawai) {
                $startDate = Carbon::createFromDate($year, $month, 1);
                $endDate = $startDate->copy()->endOfMonth();

                // Ambil data Absensi
                $absensis = PegawaiAbsensi::where('pegawai_id', $pegawaiId)
                    ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get()
                    ->keyBy('tanggal');

                // Ambil Special Events untuk Pegawai ini di bulan ini
                $events = SpecialEvent::whereHas('participants', function($q) use ($pegawaiId) {
                        $q->where('pegawai_id', $pegawaiId);
                    })
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->get()
                    ->keyBy(function($item) {
                        return $item->date->format('Y-m-d'); // Access as Carbon object if casted, or parse it
                    });
                
                // Ambil Wajib Hadir Data (Optimized: Get all mandatory days for this employee in this year)
                // Assuming Logic: We need to know if a specific DATE was mandatory. 
                // Currently markAsAlpha logic checks PegawaiWajibHadir based on Day Name (Senin, dll).
                // We'll replicate that logic here loop-by-loop or pre-fetch.
                $tahunAjaran = Tahun::aktif()->first();
                $wajibHadirDays = [];
                if($tahunAjaran) {
                    $wajibHadirDays = \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawaiId)
                        ->where('tahun_id', $tahunAjaran->id)
                        ->pluck('hari')
                        ->toArray(); // ['Senin', 'Selasa', ...]
                }

                // Loop setiap hari dalam bulan
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    $dateString = $date->format('Y-m-d');
                    $dayName = $date->locale('id')->isoFormat('dddd');
                    
                    $status = '-'; // Default
                    $jamMasuk = '-';
                    $jamPulang = '-';
                    $durasi = '-';
                    $keterangan = '';
                    $rowClass = '';

                    // 1. Cek Absensi Real (Prioritas Utama)
                    if (isset($absensis[$dateString])) {
                        $log = $absensis[$dateString];
                        $status = $log->status;
                        $jamMasuk = $log->jam_masuk ?? '-';
                        $jamPulang = $log->jam_pulang ?? '-';
                        $durasi = $log->durasi_kerja ?? '-';
                        
                        // Style based on status
                        if ($status == 'Hadir') $rowClass = 'bg-green-50 dark:bg-green-900/20';
                        elseif ($status == 'Telat') $rowClass = 'bg-yellow-50 dark:bg-yellow-900/20';
                        elseif ($status == 'Alpha') $rowClass = 'bg-red-50 dark:bg-red-900/20';
                    } 
                    // 2. Jika Tidak Ada Absensi Real, Cek Special Event
                    elseif (isset($events[$dateString])) {
                        $event = $events[$dateString];
                        $status = $event->name; // Tampilkan Nama Event
                        $keterangan = 'Kegiatan Khusus';
                        $rowClass = 'bg-blue-50 dark:bg-blue-900/20';
                        
                        // Jika event wajib hadir & lewat hari, mungkin bisa ditandai 'Alpha Event' jika logic menuntut, 
                        // tapi user minta "status berupa nama eventnya".
                    }
                    // 3. Jika Tidak Ada Event, Cek Jadwal Wajib Hadir
                    elseif (in_array($dayName, $wajibHadirDays)) {
                         if ($date->isPast()) {
                             $status = 'Alpha'; // Terlewat
                             $rowClass = 'bg-red-50 dark:bg-red-900/20';
                         } else {
                             $status = 'Terjadwal'; // Belum lewar
                         }
                    } 
                    // 4. Hari Libur / Tidak Wajib
                    else {
                        // Cek apakah benar-benar hari libur / minggu
                        // Mengikuti data dari halaman Hari Libur (DB) + Minggu
                        $isLibur = \App\Models\HariLibur::isLibur($dateString, $tahunAjaran->id ?? null) || $date->isSunday();
                        
                        if ($isLibur) {
                            $status = 'Libur';
                            $rowClass = 'text-gray-400';
                        } else {
                            // Bukan libur, tapi tidak ada jadwal (Kosong)
                            $status = ''; 
                            $rowClass = '';
                        }
                    }

                    $attendanceData[] = [
                        'date' => $date->locale('id')->isoFormat('dddd, D MMMM Y'),
                        'raw_date' => $dateString,
                        'jam_masuk' => $jamMasuk,
                        'jam_pulang' => $jamPulang,
                        'durasi' => $durasi,
                        'status' => $status,
                        'keterangan' => $keterangan,
                        'row_class' => $rowClass
                    ];
                }
            }
        }

        return view('attendance.report.employee', compact(
            'pegawais', 'years', 'months', 
            'attendanceData', 
            'selectedPegawai', 'month', 'year', 'pegawaiId'
        ));
    }
    public function export(Request $request)
    {
        $pegawaiId = $request->input('pegawai_id');
        $month = $request->input('month');
        $year = $request->input('year');
        $type = $request->input('type', 'excel');

        $sekolah = \App\Models\Sekolah::first();
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $dates = \Carbon\CarbonPeriod::create($startDate, $endDate);
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // --- MATRIKS REKAP SEMUA PEGAWAI ---
        if ($pegawaiId === 'all') {
             $rekapData = Pegawai::orderBy('name')
                ->with(['pegawaiAbsensis' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                }])
                ->get()
                ->map(function($p) {
                    return (object) [
                        'name' => $p->name,
                        'attendance' => $p->pegawaiAbsensis->keyBy('tanggal')
                    ];
                });

            if ($type === 'pdf') {
                return view('attendance.report.print_rekap_bulanan_pegawai', compact(
                    'rekapData', 'dates', 'month', 'year', 'sekolah'
                ));
            } else {
                 // For now, redirect Excel 'all' to PDF or implement generic matrix export later if asked
                 // User asked for "desain halaman rekap export PDF" matching student.
                 return view('attendance.report.print_rekap_bulanan_pegawai', compact(
                    'rekapData', 'dates', 'month', 'year', 'sekolah'
                ));
            }
        }

        // --- LAPORAN INDIVIDU ---
        if (!$pegawaiId || !$month || !$year) {
            return back()->with('error', 'Filter tidak lengkap.');
        }

        $pegawai = Pegawai::findOrFail($pegawaiId);
        $fileName = 'Laporan_Absensi_' . str_replace(' ', '_', $pegawai->name) . '_' . $month . '-' . $year . '.xlsx';

        // Prepare Data Logic (Individual)
        $absensis = PegawaiAbsensi::where('pegawai_id', $pegawaiId)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tanggal');

        $events = SpecialEvent::whereHas('participants', function($q) use ($pegawaiId) {
                $q->where('pegawai_id', $pegawaiId);
            })
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) { return $item->date->format('Y-m-d'); });

        $tahunAjaran = Tahun::aktif()->first();
        $wajibHadirDays = [];
        if($tahunAjaran) {
            $wajibHadirDays = \App\Models\PegawaiWajibHadir::where('pegawai_id', $pegawaiId)
                ->where('tahun_id', $tahunAjaran->id)
                ->pluck('hari')
                ->toArray();
        }

        $attendanceData = collect();

        foreach ($dates as $date) {
            $dateString = $date->format('Y-m-d');
            $dayName = $date->locale('id')->isoFormat('dddd');
             
            $status = '-';
            $jamMasuk = '-';
            $jamPulang = '-';
            $durasi = '-';
            $notes = '-';
            
            // Logic Priority: Real > Event > Schedule > Holiday
            if (isset($absensis[$dateString])) {
                $log = $absensis[$dateString];
                $status = $log->status;
                $jamMasuk = $log->jam_masuk ?? '-';
                $jamPulang = $log->jam_pulang ?? '-';
                $durasi = $log->durasi_kerja ?? '-';
                $notes = $log->catatan ?? '-';
            } elseif (isset($events[$dateString])) {
                $status = $events[$dateString]->name;
                $notes = 'Kegiatan Khusus';
            } elseif (in_array($dayName, $wajibHadirDays)) {
                if ($date->isPast()) {
                    $status = 'Alpha';
                } else {
                    $status = 'Terjadwal';
                }
            } else {
                // Cek Libur / Minggu
                $isLibur = \App\Models\HariLibur::isLibur($dateString, $tahunAjaran->id ?? null) || $date->isSunday();
                if ($isLibur) {
                    $status = 'Libur';
                } else {
                    $status = ''; // Kosong jika tidak ada jadwal dan bukan libur
                }
            }

            $attendanceData->push((object)[
                'date' => $dateString,
                'clock_in' => $jamMasuk,
                'clock_out' => $jamPulang,
                'status' => $status,
                'notes' => $notes
            ]);
        }

        if ($type === 'pdf') {
            return view('attendance.report.print_employee', compact(
                'attendanceData', 'pegawai', 'dates', 'month', 'year', 'sekolah', 'months'
            ));
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\AbsensiPegawaiExport($attendanceData, $pegawai, $dates, $month, $year, $sekolah, $months), 
            $fileName
        );
    }
}
