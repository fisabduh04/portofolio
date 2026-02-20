<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FingerprintMachine;
use App\Models\AttendanceLog;
use App\Services\AttendanceService;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;

class PullAttendanceLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:pull-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull attendance logs from configured fingerprint machines based on schedule.';

    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $currentTime = $now->format('H:i:s');
        
        $machines = FingerprintMachine::where('is_scheduler_active', true)
            ->where('status', true)
            ->get();

        $this->info("Found " . $machines->count() . " active scheduled machines.");

        foreach ($machines as $machine) {
            // Check Schedule Time Window
            if ($machine->scheduler_start_time && $machine->scheduler_end_time) {
                if ($currentTime < $machine->scheduler_start_time || $currentTime > $machine->scheduler_end_time) {
                    $this->info("Skipping {$machine->name}: Outside schedule window ({$machine->scheduler_start_time} - {$machine->scheduler_end_time}).");
                    continue;
                }
            }

            $this->info("Processing {$machine->name} ({$machine->ip_address})...");
            
            try {
                $zk = new ZKTeco($machine->ip_address, $machine->port);
                if ($zk->connect()) {
                    $logs = $zk->getAttendance();
                    $zk->disconnect();

                    if (!empty($logs)) {
                        $count = 0;
                        foreach ($logs as $log) {
                            $userId = $log['id']; 
                            $timestamp = $log['timestamp'];

                            $attendanceLog = AttendanceLog::firstOrCreate([
                                'machine_id' => $machine->name,
                                'scan_time' => $timestamp,
                                'pegawai_id' => $this->getPegawaiId($userId, $machine->id), 
                            ]);

                            if ($attendanceLog->wasRecentlyCreated && $attendanceLog->pegawai_id) {
                                $pegawai = \App\Models\Pegawai::find($attendanceLog->pegawai_id);
                                if ($pegawai) {
                                    $date = substr($timestamp, 0, 10);
                                    $this->attendanceService->calculateDailyAttendance($pegawai, $date);
                                    $count++;
                                }
                            }
                        }
                        $this->info("Pulled $count new logs from {$machine->name}.");
                    } else {
                        $this->info("No logs found on {$machine->name}.");
                    }
                } else {
                    $this->error("Failed to connect to {$machine->name}.");
                    Log::error("Scheduler: Failed to connect to {$machine->name} ({$machine->ip_address})");
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$machine->name}: " . $e->getMessage());
                Log::error("Scheduler Error {$machine->name}: " . $e->getMessage());
            }
        }
    }

    private function getPegawaiId($fingerprintId, $machineId)
    {
        $enrollment = \App\Models\FingerprintEnrollment::where('fingerprint_user_id', $fingerprintId)
            ->where(function($q) use ($machineId) {
                $q->where('fingerprint_machine_id', $machineId)
                  ->orWhereNull('fingerprint_machine_id');
            })->first();

        return $enrollment ? $enrollment->pegawai_id : null;
    }
}
