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
        $stats = [
            'total_pegawai' => Pegawai::where('aktif', 'Aktif')->count(),
            'hadir' => PegawaiAbsensi::where('tanggal', $date)->where('status', 'Hadir')->count(),
            'telat' => PegawaiAbsensi::where('tanggal', $date)->where('status', 'Telat')->count(),
            'alpha' => PegawaiAbsensi::where('tanggal', $date)->where('status', 'Alpha')->count(),
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

        $report = PegawaiAbsensi::with('pegawai.attendanceRule')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->groupBy('pegawai_id')
            ->map(function ($items, $pegawaiId) {
                $pegawai = $items->first()->pegawai;
                return [
                    'pegawai' => $pegawai,
                    'hadir_count' => $items->whereIn('status', ['Hadir', 'Telat'])->count(),
                    'telat_count' => $items->where('status', 'Telat')->count(),
                    'alpha_count' => $items->where('status', 'Alpha')->count(),
                    'total_gaji' => $items->sum('nominal_gaji'),
                    'total_makan' => $items->sum('nominal_makan'),
                    'grand_total' => $items->sum('total_honor'),
                ];
            });

        return view('attendance.report', compact('report', 'month', 'year'));
    }
}
