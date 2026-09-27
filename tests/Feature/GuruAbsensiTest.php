<?php

use App\Models\AttendanceLog;
use App\Models\AttendanceRule;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use App\Models\PegawaiRuleAllocation;
use App\Models\PegawaiWajibHadir;
use App\Models\Sekolah;
use App\Models\Tahun;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 27)->startOfDay());
    View::share('sekolah', new Sekolah);
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_04_110343_create_jadwals_table.php',
        'database/migrations/2026_01_28_031755_create_jadwal_pikets_table.php',
        'database/migrations/2026_02_11_062023_create_attendance_rules_table.php',
        'database/migrations/2026_02_11_062024_create_attendance_logs_table.php',
        'database/migrations/2026_02_11_062027_create_pegawai_absensis_table.php',
        'database/migrations/2026_02_17_151000_create_flexible_attendance_tables.php',
        'database/migrations/2026_02_17_153201_create_pegawai_wajib_hadirs_table.php',
        'database/migrations/2026_02_17_154753_create_pegawai_rule_allocations_table.php',
        'database/migrations/2026_02_19_120000_create_pegawai_izins_table.php',
        'database/migrations/2026_09_27_091507_add_manual_attendance_fields_to_pegawai_absensis_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    $this->travelBack();
});

/** @param iterable<Pegawai> $teachers */
function scheduleManualAttendanceTeachers(iterable $teachers, string $hari = 'Jumat'): Tahun
{
    $tahun = Tahun::factory()->create(['isActive' => true]);
    foreach ($teachers as $teacher) {
        PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'hari' => $hari]);
    }

    return $tahun;
}

test('manual form at the existing address lists only active teachers and restores attendance', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['name' => 'Guru Aktif', 'jenisptk' => 'Guru', 'aktif' => 'aktif']);
    scheduleManualAttendanceTeachers([$teacher]);
    Pegawai::factory()->create(['name' => 'Petugas Tata Usaha', 'jenisptk' => 'Tenaga Kependidikan']);
    Pegawai::factory()->create(['name' => 'Guru Nonaktif', 'jenisptk' => 'Guru', 'aktif' => 'Nonaktif']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-25', 'status' => 'Sakit', 'keterangan' => 'Istirahat']);

    $this->actingAs($admin)->get('/attendance/create?date=2026-09-25')
        ->assertOk()->assertSee('Guru Aktif')->assertSee('Istirahat')
        ->assertSee('value="Sakit" checked', false)
        ->assertDontSee('Petugas Tata Usaha')->assertDontSee('Guru Nonaktif')
        ->assertSee(route('attendance.rekap-guru'), false);
});

test('administrator saves teacher statuses in the fingerprint results table without fabricating scan logs', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teachers = Pegawai::factory()->count(6)->create(['jenisptk' => 'Guru']);
    scheduleManualAttendanceTeachers($teachers);
    $statuses = ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Pulang', 'Telat'];
    $attendance = $teachers->mapWithKeys(fn (Pegawai $teacher, int $index): array => [$teacher->id => [
        'pegawai_id' => $teacher->id, 'status' => $statuses[$index], 'keterangan' => 'Catatan '.$index,
    ]])->all();

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-25', 'attendance' => $attendance])
        ->assertRedirect(route('attendance.create', ['date' => '2026-09-25']))->assertSessionHasNoErrors();

    foreach ($teachers as $index => $teacher) {
        $this->assertDatabaseHas('pegawai_absensis', [
            'pegawai_id' => $teacher->id, 'status' => $statuses[$index],
            'attendance_source' => 'Manual', 'is_manual' => true, 'created_by' => $admin->id,
            'updated_by' => $admin->id, 'keterangan' => 'Catatan '.$index, 'jam_masuk' => null,
        ]);
    }
    $this->assertDatabaseCount('pegawai_absensis', 6);
    expect(PegawaiAbsensi::whereDate('tanggal', '2026-09-25')->count())->toBe(6);
    $this->assertDatabaseEmpty('attendance_logs');
});

test('resubmission updates a daily fingerprint result without duplicating or changing other days', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $original = PegawaiAbsensi::factory()->create(['tanggal' => '2026-09-25', 'status' => 'Hadir', 'created_by' => 123, 'nominal_gaji' => 100000, 'total_honor' => 100000]);
    scheduleManualAttendanceTeachers([$original->pegawai]);
    $previousDay = PegawaiAbsensi::factory()->create(['pegawai_id' => $original->pegawai_id, 'tanggal' => '2026-09-24', 'status' => 'Hadir']);
    $payload = ['tanggal' => '2026-09-25', 'attendance' => [$original->pegawai_id => ['pegawai_id' => $original->pegawai_id, 'status' => 'Izin', 'keterangan' => 'Keperluan keluarga']]];

    $this->actingAs($admin)->post(route('attendance.store'), $payload)->assertSessionHasNoErrors();
    $this->post(route('attendance.store'), $payload)->assertSessionHasNoErrors();

    $this->assertDatabaseCount('pegawai_absensis', 2);
    $this->assertDatabaseHas('pegawai_absensis', ['id' => $original->id, 'status' => 'Izin', 'is_manual' => true, 'created_by' => 123, 'updated_by' => $admin->id, 'total_honor' => 0]);
    expect($previousDay->fresh()->status)->toBe('Hadir');
});

test('invalid or nonteacher rows reject the entire attendance submission', function (array $changes, string $field, string $message) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $payload = ['tanggal' => '2026-09-25', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => 'Hadir'],
        array_merge(['pegawai_id' => $teacher->id, 'status' => 'Hadir'], $changes),
    ]];

    $this->actingAs($admin)->post(route('attendance.store'), $payload)->assertInvalid([$field => $message]);

    $this->assertDatabaseEmpty('pegawai_absensis');
})->with([
    'unknown teacher' => [['pegawai_id' => 999999], 'attendance.1.pegawai_id', 'Presensi hanya dapat diisi untuk guru aktif.'],
    'duplicate teacher' => [[], 'attendance.1.pegawai_id', 'Guru tidak boleh berulang dalam satu pengiriman.'],
    'unknown status' => [['status' => 'Libur'], 'attendance.1.status', 'Status kehadiran tidak valid.'],
    'missing status' => [['status' => null], 'attendance.1.status', 'Pilih status kehadiran setiap guru.'],
    'long note' => [['keterangan' => str_repeat('a', 1001)], 'attendance.1.keterangan', 'Keterangan maksimal 1000 karakter.'],
]);

test('staff and inactive teachers cannot be submitted even with a valid employee id', function (array $attributes) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $employee = Pegawai::factory()->create($attributes);

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-25', 'attendance' => [
        ['pegawai_id' => $employee->id, 'status' => 'Hadir'],
    ]])->assertInvalid(['attendance.0.pegawai_id' => 'Presensi hanya dapat diisi untuk guru aktif.']);

    $this->assertDatabaseEmpty('pegawai_absensis');
})->with([
    'staff' => [['jenisptk' => 'Tenaga Kependidikan']],
    'inactive teacher' => [['jenisptk' => 'Guru', 'aktif' => 'Nonaktif']],
]);

test('future attendance is rejected', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-28', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => 'Hadir'],
    ]])->assertInvalid(['tanggal' => 'Presensi tidak dapat diisi untuk tanggal mendatang.']);

    $this->assertDatabaseEmpty('pegawai_absensis');
});

test('fingerprint recalculation preserves manually entered status notes and honor', function (bool $hasScan) {
    $record = PegawaiAbsensi::factory()->create(['tanggal' => '2026-09-25', 'status' => 'Sakit', 'is_manual' => true, 'attendance_source' => 'Manual', 'keterangan' => 'Surat dokter', 'total_honor' => 50000]);
    if ($hasScan) {
        AttendanceLog::create(['pegawai_id' => $record->pegawai_id, 'scan_time' => '2026-09-25 08:00:00', 'machine_id' => 'FINGER']);
    }

    $result = app(AttendanceService::class)->calculateDailyAttendance($record->pegawai, '2026-09-25');

    expect($result->id)->toBe($record->id);
    $this->assertDatabaseHas('pegawai_absensis', ['id' => $record->id, 'status' => 'Sakit', 'keterangan' => 'Surat dokter', 'attendance_source' => 'Manual', 'total_honor' => 50000]);
})->with(['fingerprint scan received' => true, 'no scan on a nonworking day' => false]);

test('fingerprint attendance without a manual override continues to be calculated', function () {
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    AttendanceLog::create(['pegawai_id' => $teacher->id, 'scan_time' => '2026-09-25 08:00:00', 'machine_id' => 'FINGER']);

    app(AttendanceService::class)->calculateDailyAttendance($teacher, '2026-09-25');

    $this->assertDatabaseHas('pegawai_absensis', ['pegawai_id' => $teacher->id, 'status' => 'Diluar Jadwal', 'attendance_source' => 'Fingerprint', 'is_manual' => false, 'jam_masuk' => '08:00:00']);
});

test('manual status uses configured attendance rates and lateness deductions', function (string $status, int $gaji, int $makan, int $total) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $tahun = Tahun::factory()->create(['isActive' => true]);
    $rule = AttendanceRule::create(['name' => 'Aturan Guru', 'jam_masuk' => '07:00', 'jam_pulang' => '14:00', 'scan_masuk_start' => '06:00', 'scan_pulang_end' => '17:00', 'gaji_harian' => 100000, 'bantuan_makan' => 20000, 'denda_telat' => 5000]);
    PegawaiRuleAllocation::create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'attendance_rule_id' => $rule->id]);
    PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'hari' => 'Jumat']);

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-25', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => $status],
    ]])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('pegawai_absensis', ['pegawai_id' => $teacher->id, 'status' => $status, 'nominal_gaji' => $gaji, 'nominal_makan' => $makan, 'total_honor' => $total]);
})->with([
    'present' => ['Hadir', 100000, 20000, 120000],
    'late' => ['Telat', 100000, 20000, 115000],
    'sick' => ['Sakit', 100000, 0, 100000],
    'permission' => ['Izin', 0, 0, 0],
    'absent' => ['Alpha', 0, 0, 0],
    'early departure' => ['Pulang', 100000, 20000, 120000],
]);

test('teacher report combines manual and fingerprint records within the selected period', function (array $filters, int $expectedHadir, int $expectedSakit, int $expectedDates) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-24', 'status' => 'Hadir']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-25', 'status' => 'Sakit', 'attendance_source' => 'Manual', 'is_manual' => true]);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-08-01', 'status' => 'Hadir']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2025-09-25', 'status' => 'Hadir']);
    $staff = Pegawai::factory()->create(['jenisptk' => 'Tenaga Kependidikan']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $staff->id, 'tanggal' => '2026-09-25', 'status' => 'Alpha']);

    $this->actingAs($admin)->get(route('attendance.rekap-guru', $filters))->assertOk()
        ->assertViewHas('summary', fn (array $summary): bool => $summary['Hadir'] === $expectedHadir && $summary['Sakit'] === $expectedSakit && $summary['Alpha'] === 0)
        ->assertViewHas('rows', fn ($rows): bool => $rows->count() === 1)
        ->assertViewHas('dates', fn ($dates): bool => $dates->count() === $expectedDates);
})->with([
    'daily' => [['mode' => 'harian', 'date' => '2026-09-25'], 0, 1, 1],
    'monthly' => [['mode' => 'bulanan', 'year' => 2026, 'month' => 9], 1, 1, 30],
    'yearly' => [['mode' => 'tahunan', 'year' => 2026], 2, 1, 365],
    'range' => [['mode' => 'periode', 'start_date' => '2026-09-24', 'end_date' => '2026-09-25'], 1, 1, 2],
]);

test('reports preserve inactive teacher history and show unrecorded days without inventing absences', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru', 'aktif' => 'Nonaktif']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-01', 'status' => 'Mengajar']);
    Pegawai::factory()->create(['jenisptk' => 'Guru']);

    $response = $this->actingAs($admin)->get(route('attendance.rekap-guru', ['mode' => 'bulanan', 'month' => 9, 'year' => 2026, 'pegawai_id' => $teacher->id]))
        ->assertViewHas('rows', fn ($rows): bool => $rows->count() === 1 && $rows->first()['counts']['Hadir'] === 1 && $rows->first()['counts']['Alpha'] === 0)
        ->assertSee('-');

    $document = new DOMDocument;
    $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    expect(trim($xpath->evaluate('string(//td[@title="2026-09-02: Belum ada catatan"])')))->toBe('-');
});

test('report filters reject invalid or excessive date ranges', function (array $filters, string $field) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)->get(route('attendance.rekap-guru', $filters))->assertInvalid($field);
})->with([
    'invalid date' => [['mode' => 'harian', 'date' => 'nonsense'], 'date'],
    'invalid month' => [['month' => 13], 'month'],
    'missing range' => [['mode' => 'periode'], 'start_date'],
    'reversed range' => [['mode' => 'periode', 'start_date' => '2026-09-25', 'end_date' => '2026-09-01'], 'end_date'],
    'excessive range' => [['mode' => 'periode', 'start_date' => '2020-01-01', 'end_date' => '2026-09-01'], 'end_date'],
]);

test('print and detail report escape entered notes', function (string $route, array $extra) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    PegawaiAbsensi::factory()->create(['tanggal' => '2026-09-25', 'keterangan' => '<script>alert(1)</script>']);

    $this->actingAs($admin)->get(route($route, $extra + ['mode' => 'harian', 'date' => '2026-09-25']))
        ->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
})->with([
    'detail' => ['attendance.rekap-guru', ['view_mode' => 'detail']],
    'print' => ['attendance.rekap-guru.export', ['format' => 'pdf']],
]);

test('excel download includes filtered attendance and treats user text as text', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => '=1+1']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-25', 'status' => 'Sakit', 'keterangan' => '=2+2']);
    PegawaiAbsensi::factory()->create(['pegawai_id' => $teacher->id, 'tanggal' => '2026-09-24', 'status' => 'Alpha']);

    $response = $this->actingAs($admin)->get(route('attendance.rekap-guru.export', ['mode' => 'harian', 'date' => '2026-09-25', 'format' => 'excel']));
    $response->assertDownload('rekap_guru_harian_2026-09-25.xlsx');

    $sheet = IOFactory::load($response->baseResponse->getFile()->getPathname())->getActiveSheet();
    $values = $sheet->toArray();
    $row = collect($values)->first(fn (array $values): bool => in_array('=1+1', $values, true));
    expect($row)->toContain('Sakit', '=2+2')->not->toContain('Alpha');
});

test('guests cannot open manual input or teacher recap', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with(['attendance.create', 'attendance.rekap-guru', 'attendance.rekap-guru.export']);

test('manual roster follows weekday obligations from manual teaching and duty schedules', function (string $date, array $expectedNames) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $manual = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Guru Manual Senin']);
    $teaching = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Guru Mengajar Senin']);
    $duty = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Guru Piket Senin']);
    $tuesday = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Guru Selasa']);
    $oldTeacher = Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Guru Periode Lama']);
    $inactive = Pegawai::factory()->create(['jenisptk' => 'Guru', 'aktif' => 'Nonaktif', 'name' => 'Guru Nonaktif']);
    $staff = Pegawai::factory()->create(['jenisptk' => 'Tenaga Kependidikan', 'name' => 'Staff Senin']);
    Pegawai::factory()->create(['jenisptk' => 'Guru', 'name' => 'Tanpa Jadwal']);
    $tahun = scheduleManualAttendanceTeachers([$manual, $inactive, $staff], 'Senin');
    $oldYear = Tahun::factory()->create(['isActive' => false]);
    PegawaiWajibHadir::create(['pegawai_id' => $tuesday->id, 'tahun_id' => $tahun->id, 'hari' => 'Selasa']);
    PegawaiWajibHadir::create(['pegawai_id' => $oldTeacher->id, 'tahun_id' => $oldYear->id, 'hari' => 'Senin']);
    Jadwal::factory()->create(['pegawai_id' => $teaching->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);
    Jadwal::factory()->create(['pegawai_id' => $oldTeacher->id, 'tahun_id' => $oldYear->id, 'hari' => 'Senin']);
    JadwalPiket::create(['pegawai_id' => $duty->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);
    JadwalPiket::create(['pegawai_id' => $oldTeacher->id, 'tahun_id' => $oldYear->id, 'hari' => 'Senin']);
    JadwalPiket::create(['pegawai_id' => $manual->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);

    $this->actingAs($admin)->get(route('attendance.create', ['date' => $date]))
        ->assertOk()->assertViewHas('pegawais', fn ($teachers): bool => $teachers->pluck('name')->all() === $expectedNames);
})->with([
    'Monday' => ['2026-09-21', ['Guru Manual Senin', 'Guru Mengajar Senin', 'Guru Piket Senin']],
    'Tuesday' => ['2026-09-22', ['Guru Selasa']],
    'Wednesday without obligations' => ['2026-09-23', []],
]);

test('teachers with only automatic teaching or duty obligations can submit manual attendance', function (string $source) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $tahun = Tahun::factory()->create(['isActive' => true]);
    if ($source === 'teaching') {
        Jadwal::factory()->create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);
    } else {
        JadwalPiket::create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);
    }

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-21', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => 'Hadir'],
    ]])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('pegawai_absensis', ['pegawai_id' => $teacher->id, 'status' => 'Hadir', 'is_manual' => true]);
})->with(['teaching', 'duty']);

test('a teacher without an obligation on the submitted date rejects the whole batch', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $monday = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $tuesday = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $tahun = scheduleManualAttendanceTeachers([$monday], 'Senin');
    PegawaiWajibHadir::create(['pegawai_id' => $tuesday->id, 'tahun_id' => $tahun->id, 'hari' => 'Selasa']);

    $this->actingAs($admin)->post(route('attendance.store'), ['tanggal' => '2026-09-21', 'attendance' => [
        ['pegawai_id' => $monday->id, 'status' => 'Hadir'],
        ['pegawai_id' => $tuesday->id, 'status' => 'Alpha'],
    ]])->assertInvalid(['attendance.1.pegawai_id' => 'Guru tidak memiliki kewajiban hadir pada hari yang dipilih.']);

    $this->assertDatabaseEmpty('pegawai_absensis');
});

test('without an active academic year the roster is empty and attendance cannot be submitted', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => 'Guru']);
    $tahun = Tahun::factory()->create(['isActive' => false]);
    PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $tahun->id, 'hari' => 'Senin']);

    $this->actingAs($admin)->get(route('attendance.create', ['date' => '2026-09-21']))
        ->assertSee('Tidak ada guru yang wajib hadir pada hari ini.')->assertViewHas('pegawais', fn ($teachers): bool => $teachers->isEmpty());
    $this->post(route('attendance.store'), ['tanggal' => '2026-09-21', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => 'Hadir'],
    ]])->assertInvalid('attendance.0.pegawai_id');

    $this->assertDatabaseEmpty('pegawai_absensis');
});

test('an active teacher account with an empty PTK type can be recorded and reported', function (?string $type) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = Pegawai::factory()->create(['jenisptk' => $type]);
    User::factory()->create(['pegawai_id' => $teacher->id, 'role' => 'guru']);
    $staff = Pegawai::factory()->create(['jenisptk' => 'Tenaga Kependidikan']);
    User::factory()->create(['pegawai_id' => $staff->id, 'role' => 'guru']);
    scheduleManualAttendanceTeachers([$teacher, $staff]);

    $this->actingAs($admin)->get(route('attendance.create', ['date' => '2026-09-25']))->assertOk()
        ->assertViewHas('pegawais', fn ($rows) => $rows->modelKeys() === [$teacher->id]);
    $this->post(route('attendance.store'), ['tanggal' => '2026-09-25', 'attendance' => [
        ['pegawai_id' => $teacher->id, 'status' => 'Hadir'],
    ]])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('pegawai_absensis', ['pegawai_id' => $teacher->id, 'is_manual' => true, 'status' => 'Hadir']);
    $this->get(route('attendance.rekap-guru', ['mode' => 'harian', 'date' => '2026-09-25']))->assertOk()
        ->assertViewHas('summary', fn ($summary) => $summary['Hadir'] === 1);
})->with(['null' => [null], 'blank' => ['  ']]);
