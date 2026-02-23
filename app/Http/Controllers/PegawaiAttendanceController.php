<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PegawaiAttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        // Stats for the day
        // Stats for the day
        $totalPegawai = Pegawai::where('aktif', 'Aktif')->count();
        $absensiStats = PegawaiAbsensi::where('tanggal', $date)
            ->selectRaw('
                count(CASE WHEN status = "Hadir" THEN 1 END) as hadir,
                count(CASE WHEN status = "Telat" THEN 1 END) as telat,
                count(CASE WHEN status = "Alpha" THEN 1 END) as alpha
            ')
            ->first();

        $stats = [
            'total_pegawai' => $totalPegawai,
            'hadir' => $absensiStats->hadir ?? 0,
            'telat' => $absensiStats->telat ?? 0,
            'alpha' => $absensiStats->alpha ?? 0,
        ];

        $attendance = PegawaiAbsensi::with('pegawai')
            ->where('tanggal', $date)
            ->get();

        return view('attendance.index', compact('stats', 'attendance', 'date'));
    }

    public function create()
    {
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->get();
        return view('attendance.create', compact('pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'scan_time' => 'required|date',
        ]);

        AttendanceLog::create([
            'pegawai_id' => $request->pegawai_id,
            'scan_time' => $request->scan_time,
            'machine_id' => 'MANUAL', // Marker for manual input
        ]);

        // Auto-process for that day
        $pegawai = Pegawai::find($request->pegawai_id);
        $date = Carbon::parse($request->scan_time)->toDateString();
        
        $this->attendanceService->calculateDailyAttendance($pegawai, $date);

        return redirect()->route('attendance.index', ['date' => $date])
            ->with('type', 'success')
            ->with('message', 'Log presensi berhasil ditambahkan.');
    }

    /**
     * Re-calculate attendance for a range of dates.
     */
    public function process(Request $request)
    {
        $request->validate([
             'start_date' => 'required|date',
             'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $pegawais = Pegawai::where('aktif', 'Aktif')->get();

        DB::beginTransaction();
        try {
            foreach ($pegawais as $pegawai) {
                // Iterate through each day
                $current = $startDate->copy();
                while ($current->lte($endDate)) {
                    $this->attendanceService->calculateDailyAttendance($pegawai, $current->toDateString());
                    $current->addDay();
                }
            }
            DB::commit();
            return back()->with('type', 'success')->with('message', 'Sinkronisasi dan kalkulasi ulang berrhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Gagal proses: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        // 1. Get Aggregated Stats from Service
        $stats = $this->attendanceService->getMonthlyStats($month, $year);

        // 2. Fetch All Active Employees
        $activePegawais = Pegawai::where('aktif', 'Aktif')
            ->with(['ruleAllocations' => function($query) use ($year) {
                $query->whereHas('tahun', function($q) use ($year) {
                    $q->where('tahun', $year);
                })->with('attendanceRule');
            }])
            ->orderBy('name')
            ->get();

        // 3. Merge Data
        $report = $activePegawais->map(function($pegawai) use ($stats) {
            $stat = $stats->get($pegawai->id);
            
            return (object) [
                'pegawai' => $pegawai,
                'hadir_count' => $stat->hadir_count ?? 0,
                'telat_count' => $stat->telat_count ?? 0,
                'alpha_count' => $stat->alpha_count ?? 0,
                'izin_count' => $stat->izin_count ?? 0,
                'total_gaji' => $stat->total_gaji ?? 0,
                'total_makan' => $stat->total_makan ?? 0,
                'grand_total' => $stat->grand_total ?? 0,
            ];
        });

        return view('attendance.report', compact('report', 'month', 'year'));
    }

    public function setting()
    {
        $pegawais = Pegawai::with('fingerprintEnrollments')->orderBy('name')->paginate(100);
        return view('attendance.setting', compact('pegawais'));
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:pegawais,id',
            'settings.*.fingerprint_id' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->settings as $setting) {
                $pegawai = Pegawai::find($setting['id']);
                if ($pegawai) {
                     // Handle Enrollment (Update or Create the primary one)
                    if (!empty($setting['fingerprint_id'])) {
                        // Check if exists
                        $enrollment = $pegawai->fingerprintEnrollments()->first();
                        
                        if ($enrollment) {
                            $enrollment->update(['fingerprint_user_id' => $setting['fingerprint_id']]);
                        } else {
                            $pegawai->fingerprintEnrollments()->create([
                                'fingerprint_user_id' => $setting['fingerprint_id'],
                                'fingerprint_machine_id' => null // Default/Global
                            ]);
                        }
                    } else {
                        // If empty, user wants to clear it? 
                        // Or just clear the primary one.
                        $pegawai->fingerprintEnrollments()->delete();
                    }
                }
            }
            DB::commit();
            return back()->with('type', 'success')->with('message', 'Konfigurasi absensi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('type', 'error')->with('message', 'Gagal memperbarui konfigurasi: ' . $e->getMessage());
        }
    }

    public function rekapPegawai(Request $request)
    {
        $pegawaiId = $request->input('pegawai_id');
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        
        $pegawais = Pegawai::where('aktif', 'Aktif')->orderBy('name')->get();
        
        $attendanceData = collect([]);
        $stats = [
            'total_hadir' => 0,
            'total_durasi_jam' => 0, // Decimal hours
            'total_durasi_formatted' => '00:00:00'
        ];
        
        $selectedPegawai = null;
        
        if ($pegawaiId) {
            $selectedPegawai = Pegawai::find($pegawaiId);
            
            if ($selectedPegawai) {
                // Get start and end of month
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
                
                // Fetch attendance for range
                $dbAttendance = PegawaiAbsensi::where('pegawai_id', $pegawaiId)
                    ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                    ->get()
                    ->keyBy('tanggal');
                    
                // Generate full date range
                $attendanceData = collect([]);
                $current = $startDate->copy();
                $totalSeconds = 0;

                $joinDate = $selectedPegawai->created_at ? $selectedPegawai->created_at->startOfDay() : null;
                
                while ($current->lte($endDate)) {
                    $dateStr = $current->toDateString();
                    
                    if ($dbAttendance->has($dateStr)) {
                        $log = $dbAttendance->get($dateStr);
                        $attendanceData->push($log);
                        
                        // Process Totals (Existing Logic)
                        if ($log->status == 'Hadir' || $log->status == 'Telat' || $log->status == 'Hadir (Event)') {
                            $stats['total_hadir']++;
                            
                            if ($log->durasi_kerja) {
                                $parts = explode(':', $log->durasi_kerja);
                                if (count($parts) === 3) {
                                    $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                }
                            }
                        }
                    } else {
                        // Check for "Belum Bekerja" or "Libur"
                        $statusSpec = '-';
                        if ($joinDate && $current->lt($joinDate)) {
                            $statusSpec = 'Belum Bekerja';
                        } elseif ($current->isWeekend()) {
                            $statusSpec = 'Libur';
                        }

                        // Create Empty Placeholder
                        $attendanceData->push((object)[
                            'tanggal' => $dateStr,
                            'jam_masuk' => '-',
                            'jam_pulang' => '-',
                            'status' => $statusSpec,
                            'attendance_source' => '-',
                            'durasi_kerja' => '-'
                        ]);
                    }
                    
                    $current->addDay();
                }
                
                // Format total duration
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $seconds = $totalSeconds % 60;
                $stats['total_durasi_formatted'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                $stats['total_durasi_jam'] = round($totalSeconds / 3600, 2);
            }
        }

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return view('attendance.rekap_pegawai', compact('pegawais', 'attendanceData', 'stats', 'month', 'year', 'pegawaiId', 'selectedPegawai', 'monthNames'));
    }
}
