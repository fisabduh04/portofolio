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
        $pegawais = Pegawai::orderBy('nama')->get(); // Assumption: 'nama' column exists
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
                        $status = 'Libur / Tidak Jadwal';
                        $rowClass = 'text-gray-400';
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
}
