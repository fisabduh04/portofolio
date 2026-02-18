<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\FingerprintMachine;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FingerprintApiController extends Controller
{
    /**
     * Handle ADMS/Cloud Push from Fingerprint Machine
     * 
     * Endpoint: POST /api/attendance/push
     * 
     * Data format usually implies:
     * sn: Serial Number
     * table: 'attlog' (for attendance logs)
     * stamp: timestamp
     */
    public function push(Request $request)
    {
        // 1. Log Request for Debugging (Optional, remove in production if heavy)
        // Log::info('Fingerprint Push:', $request->all());

        $sn = $request->input('sn');
        $table = $request->input('table');

        // 2. Validate Machine
        if (!$sn) {
            return response('ERROR: NO SN', 400);
        }

        $machine = FingerprintMachine::where('name', $sn) // Assuming 'name' stores the SN or allow dynamic
            ->orWhere('ip_address', $request->ip()) 
            ->first();

        // If strict mode, uncomment below:
        // if (!$machine) {
        //     return response('ERROR: UNKNOWN MACHINE', 401); 
        // }

        // 3. Handle Handshake / Init
        if ($table == 'options') {
            return 'POST OPTIONS OK';
        }

        // 4. Handle Attendance Logs
        if ($table == 'attlog') {
            // Data might come as a bulk string or individual params depending on protocol
            // Standard ADMS often sends plain text lines in body or specific fields
            // For now, we assume standard ADMS parameters handling

            // Check if it's a bulk push or single
            // ADMS often sends: SN=...&Table=ATTLOG&Stamp=...&Risk=...
            // And the logs are in the body or standard post fields.
            // Let's assume standard field mapping for now.
            // But usually ADMS sends data line by line in body. So we might need to parse body.
            
            $content = $request->getContent(); 
            // Parsing Logic specific to ZKTeco/ADMS often requires splitting newlines
            // Example line: 1	2024-01-01 08:00:00	1	1 (ID, Time, Status, Verify)
            
            // However, some push protocols just send fields if it's a simple push.
            // Let's implement a generic handler that accepts standard fields first.
            
            // If request has 'user_id' and 'timestamp', it's a single push (rare for ADMS)
            if ($request->has('user_id') && $request->has('timestamp')) {
               $this->saveLog($request->input('user_id'), $request->input('timestamp'), $sn);
               return 'POST DATA OK';
            }

            // If it's ADMS bulk data (Raw Body)
            if (!empty($content)) {
                 $lines = explode("\n", $content);
                 foreach ($lines as $line) {
                     $line = trim($line);
                     if (empty($line)) continue;
                     
                     // Format: UserID \t Time \t Status \t Verify
                     $parts = preg_split('/\s+/', $line);
                     if (count($parts) >= 2) {
                         $userId = $parts[0];
                         $timestamp = $parts[1] . ' ' . ($parts[2] ?? '00:00:00'); // Validating format
                         
                         // Clean up timestamp if parts are split differently
                         // Usually: ID <tab> YYYY-MM-DD HH:MM:SS <tab> Status
                         if (strpos($parts[1], '-') !== false && isset($parts[2]) && strpos($parts[2], ':') !== false) {
                              $timestamp = $parts[1] . ' ' . $parts[2];
                         }

                         $this->saveLog($userId, $timestamp, $sn);
                     }
                 }
            }

            // Always return this string for ADMS
            return 'POST DATA OK';
        }

        // 5. Handle Device Info
        if ($table == 'devinfo') {
             return 'POST DATA OK';
        }
        
        // 6. Handle device operation log? (operlog)
        // Ignore for now.

        return 'POST DATA OK';
    }

    protected $attendanceService;

    public function __construct(\App\Services\AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    private function saveLog($fingerprintId, $scanTime, $machineSn)
    {
        // 1. Identify Machine ID from SN (if possible)
        $machine = FingerprintMachine::where('name', $machineSn)->first();
        $machineId = $machine ? $machine->id : null;

        // 2. Find Enrollment
        // We look for a specific enrollment for this machine, OR a global/legacy enrollment (machine_id is null)
        $enrollment = \App\Models\FingerprintEnrollment::where('fingerprint_user_id', $fingerprintId)
            ->where(function($q) use ($machineId) {
                $q->where('fingerprint_machine_id', $machineId)
                  ->orWhereNull('fingerprint_machine_id');
            })
            ->with('pegawai')
            ->first();

        // If multiple found (e.g. specific AND global), which one?
        // Ideally specific overrides global.
        // Let's refine the query to prioritize specific.
        if ($machineId) {
             $specific = \App\Models\FingerprintEnrollment::where('fingerprint_user_id', $fingerprintId)
                ->where('fingerprint_machine_id', $machineId)->with('pegawai')->first();
             if ($specific) $enrollment = $specific;
        }

        if ($enrollment && $enrollment->pegawai) {
             $pegawai = $enrollment->pegawai;

             // Avoid duplicate within same second
             AttendanceLog::firstOrCreate([
                 'pegawai_id' => $pegawai->id,
                 'scan_time' => $scanTime,
             ], [
                 'machine_id' => $machineSn, // Keeping string for now as per schema
                 'created_at' => now(),
             ]);

             // Trigger daily calculation immediately (Plug & Play)
             $date = substr($scanTime, 0, 10); // Extract YYYY-MM-DD
             $this->attendanceService->calculateDailyAttendance($pegawai, $date);
             
        } else {
            // Log for unknown user?
            // Log::warning("Unknown fingerprint ID: $fingerprintId from $machineSn");
        }
    }
}
