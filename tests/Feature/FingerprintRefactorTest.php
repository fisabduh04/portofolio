<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Pegawai;
use App\Models\FingerprintMachine;
use App\Models\FingerprintEnrollment;
use App\Models\AttendanceLog;
use App\Models\AttendanceRule;
use App\Models\Tahun;
use App\Models\PegawaiRuleAllocation;
use Carbon\Carbon;

class FingerprintRefactorTest extends TestCase
{
    // use RefreshDatabase; // Use manually if needed, but be careful with existing data. 
    // Since we are testing critical migration logic, maybe just use transactions or rely on cleanup.

    public function test_fingerprint_enrollment_flow()
    {
        // 1. Setup Data
        $machine = FingerprintMachine::firstOrCreate(
            ['ip_address' => '192.168.1.201'],
            ['name' => 'Test Machine 1', 'port' => 4370, 'comkey' => 0]
        );

        $pegawai = Pegawai::create([
            'name' => 'Refactor Test Pegawai', 
            'aktif' => 'Aktif',
            'status' => 'PNS' // Assuming status needed
        ]);

        // 2. Enroll (Simulate Controller Logic)
        $enrollment = FingerprintEnrollment::create([
            'pegawai_id' => $pegawai->id,
            'fingerprint_machine_id' => $machine->id,
            'fingerprint_user_id' => '99999'
        ]);

        // 3. Simulate API Push (Simulate FingerprintApiController Logic)
        // Logic: Find Enrollment -> Get Pegawai -> Create Log
        $foundEnrollment = FingerprintEnrollment::where('fingerprint_user_id', '99999')
            ->where(function($q) use ($machine) {
                $q->where('fingerprint_machine_id', $machine->id)
                  ->orWhereNull('fingerprint_machine_id');
            })
            ->with('pegawai')
            ->first();

        $this->assertNotNull($foundEnrollment, 'Enrollment not found via lookup logic');
        $this->assertEquals($pegawai->id, $foundEnrollment->pegawai->id, 'Pegawai mismatch');

        // 4. Create Log
        $logTime = now()->subHours(2);
        AttendanceLog::create([
            'pegawai_id' => $foundEnrollment->pegawai->id,
            'scan_time' => $logTime,
            'machine_id' => $machine->name
        ]);

        // 5. Verify Log Exists
        $this->assertDatabaseHas('attendance_logs', [
            'pegawai_id' => $pegawai->id,
            'machine_id' => $machine->name
        ]);

        // 6. Test Accessor
        $this->assertEquals('99999', $pegawai->fingerprint_id, 'Accessor failed');

        // Cleanup
        $pegawai->delete(); // Cascades logs and enrollments? Check model
        // Enrollment has cascade on delete pegawai
        // Log has cascade on delete pegawai
        // Machine keep it.
    }
}
