<?php

namespace App\Http\Controllers;

use App\Models\FingerprintMachine;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class FingerprintMachineController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    public function index()
    {
        $machines = FingerprintMachine::latest()->paginate(10);
        return view('attendance.fingerprint.index', compact('machines'));
    }

    public function create()
    {
        return view('attendance.fingerprint.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:fingerprint_machines',
            'port' => 'required|integer',
            'comkey' => 'required|integer',
            'location' => 'nullable|string',
            'scheduler_start_time' => 'nullable|date_format:H:i',
            'scheduler_end_time' => 'nullable|date_format:H:i',
        ]);

        $validated['status'] = $request->has('status');
        $validated['is_scheduler_active'] = $request->has('is_scheduler_active');

        FingerprintMachine::create($validated);

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Mesin fingerprint berhasil ditambahkan.');
    }

    public function edit(FingerprintMachine $fingerprintMachine)
    {
        return view('attendance.fingerprint.edit', compact('fingerprintMachine'));
    }

    public function update(Request $request, FingerprintMachine $fingerprintMachine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:fingerprint_machines,ip_address,' . $fingerprintMachine->id,
            'port' => 'required|integer',
            'comkey' => 'required|integer',
            'location' => 'nullable|string',
            'scheduler_start_time' => 'nullable|date_format:H:i',
            'scheduler_end_time' => 'nullable|date_format:H:i',
        ]);

        $validated['status'] = $request->has('status');
        $validated['is_scheduler_active'] = $request->has('is_scheduler_active');

        $fingerprintMachine->update($validated);

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Data mesin fingerprint berhasil diperbarui.');
    }

    public function destroy(FingerprintMachine $fingerprintMachine)
    {
        $fingerprintMachine->delete();

        return redirect()->route('attendance.fingerprint.index')
            ->with('type', 'success')
            ->with('message', 'Mesin fingerprint berhasil dihapus.');
    }

    public function pull(FingerprintMachine $fingerprintMachine)
    {
        $zk = new \Rats\Zkteco\Lib\ZKTeco($fingerprintMachine->ip_address, $fingerprintMachine->port);
        
        try {
            if ($zk->connect()) {
                $logs = $zk->getAttendance();
                $zk->disconnect();

                if (empty($logs)) {
                    return back()->with('type', 'warning')->with('message', 'Tidak ada data log baru di mesin.');
                }

                $count = 0;
                // Reverse logs to process newest first? Or oldest? 
                // Usually DB insertion order doesn't matter for firstOrCreate, but for calculation allow oldest first.
                // Logs from library are usually sequential.

                foreach ($logs as $log) {
                    // Log format from lib: [uid, id, state, timestamp, type]
                    // id is user id in device
                    $userId = $log['id']; 
                    $timestamp = $log['timestamp'];

                    // Save Log
                    $attendanceLog = \App\Models\AttendanceLog::firstOrCreate([
                        'machine_id' => $fingerprintMachine->name, // Or Serial Number if available via $zk->serialNumber()
                        'scan_time' => $timestamp,
                        'pegawai_id' => $this->getPegawaiId($userId, $fingerprintMachine->id), 
                    ]);

                    if ($attendanceLog->wasRecentlyCreated && $attendanceLog->pegawai_id) {
                        $count++;
                        // Trigger Calc
                        $pegawai = \App\Models\Pegawai::find($attendanceLog->pegawai_id);
                        if ($pegawai) {
                            $date = substr($timestamp, 0, 10);
                            $this->attendanceService->calculateDailyAttendance($pegawai, $date);
                        }
                    }
                }

                return back()->with('type', 'success')->with('message', "Berhasil menarik $count data log baru.");
                
            } else {
                return back()->with('type', 'error')->with('message', 'Gagal terhubung ke mesin. Cek koneksi & IP.');
            }
        } catch (\Exception $e) {
            return back()->with('type', 'error')->with('message', 'Error: ' . $e->getMessage());
        }
    }

    private function getPegawaiId($fingerprintId, $machineId)
    {
        // Find enrollment
        $enrollment = \App\Models\FingerprintEnrollment::where('fingerprint_user_id', $fingerprintId)
            ->where(function($q) use ($machineId) {
                $q->where('fingerprint_machine_id', $machineId)
                  ->orWhereNull('fingerprint_machine_id');
            })->first();

        return $enrollment ? $enrollment->pegawai_id : null;
    }
}
