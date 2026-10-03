<?php

use App\Models\Absensi;
use App\Models\FaceSample;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Logbook;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\TestHandler;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(\Carbon\Carbon::parse('2026-10-01 10:00:00'));
    \Illuminate\Support\Facades\View::share('sekolah', new \App\Models\Sekolah);
});

afterEach(function () {
    $this->travelBack();
});

/**
 * @return array{0: User, 1: Jadwal, 2: Siswa, 3: array<string, mixed>}
 */
function faceAttendanceFixture(): array
{
    $year = Tahun::factory()->create(['isActive' => 1]);
    $schedule = Jadwal::factory()->create(['tahun_id' => $year->id, 'hari' => 'Kamis', 'mulai' => '09:00:00', 'akhir' => '11:00:00']);
    $actor = User::factory()->create(['role' => 'guru', 'is_active' => 1, 'pegawai_id' => $schedule->pegawai_id]);
    $student = Siswa::factory()->create();
    KelasSiswa::create(['siswa_id' => $student->id, 'kelas_id' => $schedule->kelas_id, 'tahun_id' => $year->id, 'ket' => 'aktif']);
    FaceSample::create(['siswa_id' => $student->id, 'model' => config('face-attendance.model'), 'descriptor' => array_fill(0, 128, 0.1), 'created_by' => $actor->id]);

    return [$actor, $schedule, $student, ['mode' => 'mapel', 'jadwal_id' => $schedule->id, 'materi' => 'Latihan kelas', 'descriptor' => array_fill(0, 128, 0.1)]];
}

it('records a matched face in the same mapel logbook and prevents duplicate scans', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $logbook = Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $actor->pegawai_id, 'kategori' => 'mapel', 'tanggal' => '2026-10-01', 'materi' => 'Materi manual']);

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)
        ->assertJsonPath('server_ms', fn ($duration) => is_numeric($duration) && $duration >= 0)
        ->assertJsonPath('student', $student->nama)->assertJsonPath('logbook_id', $logbook->id)->assertJsonPath('already_recorded', false);
    $this->postJson(route('face-attendance.store'), $payload)->assertJsonPath('already_recorded', true);

    $this->assertDatabaseCount('logbooks', 1);
    $this->assertDatabaseCount('absensis', 1);
    $this->assertDatabaseHas('absensis', ['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => 'Hadir']);
    expect($logbook->fresh()->materi)->toBe('Materi manual');
    $this->get(route('absensi.create', ['jadwal_id' => $schedule->id, 'date' => '2026-10-01']))->assertOk()
        ->assertViewHas('existingLogbook', fn ($entry) => $entry->absensis->contains('siswa_id', $student->id));
});

it('returns Euclidean monitoring values for duty checkout', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $handler = new TestHandler;
    Log::channel('face-attendance')->getLogger()->setHandlers([$handler]);
    config(['face-attendance.threshold' => 0.45, 'face-attendance.minimum_gap' => 0.08]);
    JadwalPiket::create(['pegawai_id' => $actor->pegawai_id, 'tahun_id' => $schedule->tahun_id, 'hari' => 'Kamis']);
    $payload['descriptor'][0] = 0.4;
    $scan = ['mode' => 'piket', 'type' => 'pulang', 'descriptor' => $payload['descriptor']];

    $response = $this->actingAs($actor)->postJson(route('face-attendance.store'), $scan);

    $response->assertOk()->assertJsonPath('matching.status', 'candidate')
        ->assertJsonPath('matching.distance', fn ($distance) => abs($distance - 0.3) < 0.000001)
        ->assertJsonPath('matching.second_distance', null)->assertJsonPath('matching.gap', null)
        ->assertJsonPath('matching.threshold', 0.45)->assertJsonPath('matching.minimum_gap', 0.08)
        ->assertJsonMissingPath('matching.descriptor')->assertJsonCount(1, 'matching.candidates')
        ->assertJsonPath('matching.candidates.0.student', $student->nama)
        ->assertJsonMissingPath('matching.candidates.0.descriptor');
    $this->assertDatabaseHas('absensis', ['siswa_id' => $student->id, 'status' => 'Hadir']);
    $this->postJson(route('face-attendance.store'), $scan)->assertJsonPath('already_recorded', true)
        ->assertJsonPath('matching.status', 'candidate');
    $this->assertDatabaseCount('absensis', 1);
    expect($handler->getRecords())->toBe([]);
    $this->get(route('face-attendance.index', ['mode' => 'piket', 'type' => 'pulang']))
        ->assertSee('Monitor jarak Euclidean')->assertSee('data-scan-match-distance', false);
});

it('returns empty monitoring distances when no eligible reference exists', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    FaceSample::where('siswa_id', $student->id)->update(['is_active' => false]);

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)
        ->assertUnprocessable()->assertInvalid(['descriptor'])
        ->assertJsonPath('matching.status', 'unknown')->assertJsonPath('matching.distance', null)
        ->assertJsonPath('matching.second_distance', null)->assertJsonPath('matching.gap', null)
        ->assertJsonPath('matching.candidates', []);

    $this->assertDatabaseCount('absensis', 0);
});

it('preserves an existing manual status instead of replacing it with a face scan', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $logbook = Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $actor->pegawai_id, 'kategori' => 'mapel', 'tanggal' => '2026-10-01']);
    Absensi::create(['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => 'Izin', 'keterangan' => 'Catatan manual']);

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertJsonPath('status', 'Izin')->assertJsonPath('already_recorded', true);

    $this->assertDatabaseHas('absensis', ['siswa_id' => $student->id, 'keterangan' => 'Catatan manual', 'status' => 'Izin']);
});

it('refreshes report regions with attendance recorded by another signed in device', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $viewer = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $filters = ['kelas_id' => $schedule->kelas_id, 'date' => '2026-10-01', 'month' => '10', 'year' => '2026',
        'from' => '2026-10-01', 'to' => '2026-10-01', 'start_date' => '2026-10-01', 'end_date' => '2026-10-01', 'type_guru' => 'mapel'];
    $this->actingAs($viewer)->get(route('absensi.rekap-harian', $filters))
        ->assertSee('data-live-recap', false)->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 0);

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertOk();
    $this->actingAs($viewer);
    foreach (['absensi.rekap-harian', 'absensi.rekap-bulanan', 'absensi.rekap-tahunan', 'absensi.rekap-periode', 'absensi.rekap'] as $route) {
        $this->get(route($route, $filters), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()->assertSee('data-live-recap-content', false)->assertSee('data-enabled="1"', false)
            ->assertViewHas($route === 'absensi.rekap' ? 'stats' : 'summaryStats', fn ($stats) => $stats['Hadir'] === 1);
    }
    $this->get(route('absensi.rekap', [...$filters, 'view' => 'detail']), ['X-Requested-With' => 'XMLHttpRequest'])
        ->assertOk()->assertSee('data-live-recap-content', false)->assertSee('Latihan kelas');
});

it('does not match faces belonging to another class', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    KelasSiswa::where('siswa_id', $student->id)->update(['kelas_id' => Kelas::factory()->create()->id]);

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertInvalid(['descriptor' => 'Wajah tidak dikenali']);

    $this->assertDatabaseCount('absensis', 0);
    $this->assertDatabaseCount('logbooks', 0);
});

it('allows a duty teacher to scan another class into the daily manual logbook', function (string $type, string $status) {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $dutyTeacher = Pegawai::factory()->create();
    $actor->update(['pegawai_id' => $dutyTeacher->id]);
    JadwalPiket::create(['pegawai_id' => $dutyTeacher->id, 'tahun_id' => $schedule->tahun_id, 'hari' => 'Kamis']);
    $logbook = Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $dutyTeacher->id, 'kategori' => 'piket_'.$type, 'tanggal' => '2026-10-01', 'catatan' => 'Tetap']);
    $payload = ['mode' => 'piket', 'type' => $type, 'descriptor' => $payload['descriptor']];

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertJsonPath('logbook_id', $logbook->id)->assertJsonPath('status', $status);

    $this->assertDatabaseCount('logbooks', 1);
    $this->assertDatabaseHas('absensis', ['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => $status]);
    expect($logbook->fresh()->catatan)->toBe('Tetap');
})->with([['masuk', 'Hadir'], ['pulang', 'Hadir']]);

it('requires both duty scans only in simple recap and explains which scan is missing', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    JadwalPiket::create(['pegawai_id' => $actor->pegawai_id, 'tahun_id' => $schedule->tahun_id, 'hari' => 'Kamis']);
    $this->actingAs($actor);
    $scan = ['mode' => 'piket', 'descriptor' => $payload['descriptor']];
    $filters = ['kelas_id' => $schedule->kelas_id, 'date' => '2026-10-01', 'type_guru' => 'piket', 'view_mode' => 'sederhana'];

    $this->postJson(route('face-attendance.store'), [...$scan, 'type' => 'masuk'])->assertJsonPath('status', 'Hadir');
    $this->get(route('absensi.rekap-harian', $filters))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->daily_status === 'Alpha')
        ->assertSee('Hanya absen masuk; belum absen pulang.');
    $this->get(route('absensi.rekap-harian', [...$filters, 'view_mode' => 'detail']))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->daily_status === 'Hadir');
    $this->get(route('absensi.rekap-bulanan', [...$filters, 'month' => 10, 'year' => 2026]))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->statuses[1]['code'] === 'A')
        ->assertSee('Hanya absen masuk; belum absen pulang.');
    $rangeFilters = [...$filters, 'month' => 10, 'year' => 2026, 'start_date' => '2026-10-01', 'end_date' => '2026-10-01'];
    foreach (['absensi.rekap-tahunan', 'absensi.rekap-periode'] as $report) {
        $this->get(route($report, $rangeFilters))->assertOk()
            ->assertViewHas('summaryStats', fn ($stats) => $stats['Alpha'] === 1 && $stats['Hadir'] === 0)
            ->assertSee('Hanya absen masuk; belum absen pulang.');
    }
    foreach (['absensi.rekap-harian.export', 'absensi.rekap-bulanan.export', 'absensi.rekap-tahunan.export'] as $report) {
        $this->get(route($report, [...$rangeFilters, 'format' => 'pdf']))->assertOk()
            ->assertSee('Hanya absen masuk; belum absen pulang.');
    }
    $export = new \App\Exports\AbsensiExport($rangeFilters, 'periode');
    expect($export->collection()->sole()['Status'])->toBe('Alpha');
    expect($export->collection()->sole()['Keterangan'])->toContain('Hanya absen masuk; belum absen pulang.');
    $this->get(route('absensi.rekap', [...$rangeFilters, 'kategori' => 'piket', 'from' => '2026-10-01', 'to' => '2026-10-01']))->assertOk()
        ->assertViewHas('stats', fn ($stats) => $stats['Alpha'] === 1 && $stats['Hadir'] === 0);

    $this->postJson(route('face-attendance.store'), [...$scan, 'type' => 'pulang'])->assertJsonPath('status', 'Hadir');
    $this->postJson(route('face-attendance.store'), [...$scan, 'type' => 'pulang'])->assertJsonPath('already_recorded', true);
    $this->get(route('absensi.rekap-harian', $filters))->assertOk()
        ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 1 && $stats['Alpha'] === 0 && $stats['Pulang'] === 0)
        ->assertDontSee('Hanya absen masuk; belum absen pulang.');
    $this->assertDatabaseCount('absensis', 2);
    $this->assertDatabaseMissing('absensis', ['status' => 'Alpha']);
    foreach (['absensi.rekap-bulanan', 'absensi.rekap-tahunan', 'absensi.rekap-periode'] as $report) {
        $this->get(route($report, $rangeFilters))->assertOk()
            ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 1 && $stats['Alpha'] === 0 && $stats['Pulang'] === 0);
    }
    $this->get(route('absensi.rekap-tahunan', [...$rangeFilters, 'view_mode' => 'detail']))->assertOk()
        ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 2);
    $this->get(route('absensi.rekap-periode', [...$rangeFilters, 'view_mode' => 'ringkasan']))->assertOk()
        ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 2);
    expect($export->collection()->sole()['Status'])->toBe('Hadir');
    $this->get(route('absensi.rekap', [...$rangeFilters, 'kategori' => 'piket', 'from' => '2026-10-01', 'to' => '2026-10-01']))->assertOk()
        ->assertViewHas('stats', fn ($stats) => $stats['Hadir'] === 1 && $stats['Pulang'] === 0);

    $this->travelTo(\Carbon\Carbon::parse('2026-10-08 10:00:00'));
    $this->postJson(route('face-attendance.store'), [...$scan, 'type' => 'pulang'])->assertJsonPath('status', 'Hadir');
    $this->get(route('absensi.rekap-harian', [...$filters, 'date' => '2026-10-08']))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->daily_status === 'Alpha')
        ->assertSee('Hanya absen pulang; belum absen masuk.');
    expect($export->collection())->toHaveCount(1);
    $this->get(route('absensi.rekap-tahunan', $rangeFilters))->assertOk()
        ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 1 && $stats['Alpha'] === 1);
    $this->get(route('absensi.rekap-periode', $rangeFilters))->assertOk()
        ->assertViewHas('summaryStats', fn ($stats) => $stats['Hadir'] === 1 && $stats['Alpha'] === 0);
});

it('preserves a duty absence for going home and does not require checkout for a mapel recap', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    JadwalPiket::create(['pegawai_id' => $actor->pegawai_id, 'tahun_id' => $schedule->tahun_id, 'hari' => 'Kamis']);
    $logbook = Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $actor->pegawai_id, 'kategori' => 'piket_pulang', 'tanggal' => '2026-10-01']);
    Absensi::create(['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => 'Pulang', 'keterangan' => 'Pulang ke rumah dari pesantren']);
    $filters = ['kelas_id' => $schedule->kelas_id, 'date' => '2026-10-01', 'view_mode' => 'sederhana'];

    $this->actingAs($actor)->postJson(route('face-attendance.store'), ['mode' => 'piket', 'type' => 'pulang', 'descriptor' => $payload['descriptor']])
        ->assertJsonPath('status', 'Pulang')->assertJsonPath('already_recorded', true);
    $this->get(route('absensi.rekap-harian', [...$filters, 'type_guru' => 'piket']))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->daily_status === 'Pulang');
    $this->postJson(route('face-attendance.store'), $payload)->assertJsonPath('status', 'Hadir');
    $this->get(route('absensi.rekap-harian', [...$filters, 'type_guru' => 'mapel']))->assertOk()
        ->assertViewHas('rekapData', fn ($rows) => $rows->first()->daily_status === 'Hadir');
    $this->assertDatabaseHas('absensis', ['logbook_id' => $logbook->id, 'status' => 'Pulang', 'keterangan' => 'Pulang ke rumah dari pesantren']);
});

it('repairs only explicitly selected legacy face checkout records and preserves manual absences', function () {
    [$actor, $schedule, $student] = faceAttendanceFixture();
    $records = collect([
        ['piket_pulang', 'Presensi wajah 10:00:00'],
        ['piket_pulang', 'Pulang ke rumah'],
        ['mapel', 'Presensi wajah 10:00:00'],
    ])->map(function (array $case) use ($actor, $schedule, $student) {
        $logbook = Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $actor->pegawai_id, 'kategori' => $case[0], 'tanggal' => '2026-10-01']);

        return Absensi::create(['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => 'Pulang', 'keterangan' => $case[1]]);
    });
    $this->artisan('attendance:repair-face-checkouts')->assertSuccessful();
    expect($records->first()->fresh()->status)->toBe('Pulang');
    $this->artisan('attendance:repair-face-checkouts', ['--apply' => true])->assertFailed();

    $this->artisan('attendance:repair-face-checkouts', ['--apply' => true, '--id' => $records->pluck('id')->all()])->assertSuccessful();

    expect($records->first()->fresh()->status)->toBe('Hadir');
    expect($records[1]->fresh()->status)->toBe('Pulang');
    expect($records[2]->fresh()->status)->toBe('Pulang');
    $this->artisan('attendance:repair-face-checkouts', ['--apply' => true, '--id' => [$records->first()->id]])
        ->expectsOutput('Diperbaiki: 0 catatan.')->assertSuccessful();
    $this->assertDatabaseCount('absensis', 3);
});

it('rejects teachers outside their schedule or duty assignment', function (string $mode) {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $actor->update(['pegawai_id' => Pegawai::factory()->create()->id]);
    $payload['mode'] = $mode;

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertForbidden();

    $this->assertDatabaseCount('absensis', 0);
})->with(['mapel', 'piket']);

it('rejects scans outside the current schedule window', function (string $time) {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $this->travelTo(\Carbon\Carbon::parse($time));

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertUnprocessable();

    $this->assertDatabaseCount('logbooks', 0);
})->with(['2026-10-01 08:00:00', '2026-10-01 12:00:00', '2026-10-02 10:00:00']);

it('rejects inactive periods and invalid descriptors', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $this->actingAs($actor)->postJson(route('face-attendance.store'), [...$payload, 'descriptor' => [0.1]])->assertInvalid(['descriptor']);
    $schedule->tahun->update(['isActive' => 0]);
    $this->postJson(route('face-attendance.store'), $payload)->assertUnprocessable();
    $this->assertDatabaseCount('absensis', 0);
});

it('does not record unknown or ambiguous faces', function (bool $ambiguous) {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $handler = new TestHandler;
    Log::channel('face-attendance')->getLogger()->setHandlers([$handler]);
    if ($ambiguous) {
        $other = Siswa::factory()->create();
        KelasSiswa::create(['siswa_id' => $other->id, 'kelas_id' => $schedule->kelas_id, 'tahun_id' => $schedule->tahun_id]);
        FaceSample::create(['siswa_id' => $other->id, 'model' => config('face-attendance.model'), 'descriptor' => $payload['descriptor'], 'created_by' => $actor->id]);
    } else {
        $payload['descriptor'][0] = 0.7;
    }

    $response = $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertInvalid(['descriptor'])
        ->assertJsonPath('matching.status', $ambiguous ? 'ambiguous' : 'unknown')
        ->assertJsonPath('matching.distance', fn ($distance) => abs($distance - ($ambiguous ? 0 : 0.6)) < 0.000001)
        ->assertJsonPath('matching.gap', $ambiguous ? 0 : null)
        ->assertJsonPath('matching.second_distance', $ambiguous ? 0 : null);

    $names = array_column($response->json('matching.candidates'), 'student');
    expect($names)->toContain($student->nama)->toHaveCount($ambiguous ? 2 : 1);
    if ($ambiguous) {
        expect($names)->toContain($other->nama);
        expect($response->json('errors.descriptor.0'))->toContain($student->nama, $other->nama, 'Identitas belum dipastikan');
    }
    $response->assertJsonMissingPath('matching.candidates.0.descriptor');
    expect($handler->getRecords())->toHaveCount($ambiguous ? 1 : 0);
    if ($ambiguous) {
        $record = $handler->getRecords()[0];
        expect($record->message)->toBe('Presensi wajah ambigu');
        expect($record->context)->toBe([
            'occurred_at' => now()->toIso8601String(),
            'actor_id' => $actor->id,
            'pegawai_id' => $actor->pegawai_id,
            'mode' => 'mapel',
            'session' => 'mapel',
            'jadwal_id' => $schedule->id,
            'model' => config('face-attendance.model'),
            'threshold' => (float) config('face-attendance.threshold'),
            'minimum_gap' => (float) config('face-attendance.minimum_gap'),
            'gap' => 0.0,
            'candidates' => [
                ['siswa_id' => $student->id, 'student' => $student->nama, 'distance' => 0.0],
                ['siswa_id' => $other->id, 'student' => $other->nama, 'distance' => 0.0],
            ],
            'attendance_saved' => false,
        ]);
    }

    $this->assertDatabaseCount('absensis', 0);
    $this->assertDatabaseCount('logbooks', 0);
})->with([false, true]);

it('logs the duty session and nonzero distances when a scan is ambiguous', function (string $type) {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $handler = new TestHandler;
    Log::channel('face-attendance')->getLogger()->setHandlers([$handler]);
    JadwalPiket::create(['pegawai_id' => $actor->pegawai_id, 'tahun_id' => $schedule->tahun_id, 'hari' => 'Kamis']);
    $other = Siswa::factory()->create();
    KelasSiswa::create(['siswa_id' => $other->id, 'kelas_id' => $schedule->kelas_id, 'tahun_id' => $schedule->tahun_id]);
    $reference = $payload['descriptor'];
    $reference[0] = 0.75;
    FaceSample::create(['siswa_id' => $other->id, 'model' => config('face-attendance.model'), 'descriptor' => $reference, 'created_by' => $actor->id]);
    $payload['descriptor'][0] = 0.4;

    $this->actingAs($actor)->postJson(route('face-attendance.store'), [
        'mode' => 'piket', 'type' => $type, 'descriptor' => $payload['descriptor'],
    ])->assertUnprocessable()->assertJsonPath('matching.status', 'ambiguous');

    expect($handler->getRecords())->toHaveCount(1);
    $context = $handler->getRecords()[0]->context;
    expect($context['session'])->toBe('piket_'.$type);
    expect($context['mode'])->toBe('piket');
    expect($context['jadwal_id'])->toBeNull();
    expect(abs($context['candidates'][0]['distance'] - 0.3))->toBeLessThan(0.000001);
    expect(abs($context['candidates'][1]['distance'] - 0.35))->toBeLessThan(0.000001);
    expect(abs($context['gap'] - 0.05))->toBeLessThan(0.000001);
    $this->assertDatabaseCount('absensis', 0);
    $this->assertDatabaseCount('logbooks', 0);
})->with(['masuk', 'pulang']);

it('requires login and refuses inactive accounts', function () {
    $this->postJson(route('face-attendance.store'), [])->assertUnauthorized();
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $actor->update(['is_active' => 0]);
    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertForbidden();
    $this->assertDatabaseCount('absensis', 0);
});

it('renders the scanner without exposing stored face descriptors', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $this->actingAs($actor)->get(route('face-attendance.index', ['mode' => 'mapel', 'jadwal_id' => $schedule->id]))
        ->assertOk()->assertSee('Pindai & catat', false)->assertSee('Performa absensi wajah')
        ->assertSee('Mulai otomatis')->assertSee('data-scan-identity', false)
        ->assertSee('aria-labelledby="scan-results-heading"', false)
        ->assertSee('data-scan-feedback', false)->assertSee('Riwayat pemindaian')
        ->assertSeeInOrder(['Hasil sesi ini', 'Riwayat pemindaian', 'Sesi presensi', 'Performa absensi wajah'])
        ->assertSee('data-scan-perf-server', false)->assertDontSee(json_encode($payload['descriptor']), false);
});

it('allows queue scanning beyond thirty requests while still rate limiting excessive traffic', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $this->actingAs($actor);
    $payload['descriptor'] = [0.1];

    for ($attempt = 0; $attempt < 120; $attempt++) {
        $this->postJson(route('face-attendance.store'), $payload)->assertUnprocessable();
    }
    $this->postJson(route('face-attendance.store'), $payload)->assertTooManyRequests();

    $this->assertDatabaseCount('absensis', 0);
});

it('prepares a trial schedule without recording fabricated attendance', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $trial = Siswa::factory()->create();
    $kelas = Kelas::factory()->create();
    $teacher = Pegawai::factory()->create();
    $this->artisan('attendance:prepare-face-trial', ['siswa' => $trial->id, 'kelas' => $kelas->id, 'tahun' => $schedule->tahun_id, 'pegawai' => $teacher->id, 'mapel' => $schedule->mapel_id])->assertExitCode(0);

    $this->assertDatabaseHas('jadwals', ['kelas_id' => $kelas->id, 'mulai' => '10:00:00', 'akhir' => '12:00:00', 'hari' => 'Kamis']);
    $this->assertDatabaseHas('kelas_siswas', ['siswa_id' => $trial->id, 'kelas_id' => $kelas->id]);
    $this->assertDatabaseHas('jadwal_pikets', ['pegawai_id' => $teacher->id, 'hari' => 'Kamis']);
    $this->assertDatabaseCount('absensis', 0);
});

it('does not modify an existing placement when preparing a trial', function () {
    [$actor, $schedule, $student, $payload] = faceAttendanceFixture();
    $this->artisan('attendance:prepare-face-trial', ['siswa' => $student->id, 'kelas' => $schedule->kelas_id, 'tahun' => $schedule->tahun_id, 'pegawai' => $schedule->pegawai_id, 'mapel' => $schedule->mapel_id])->assertExitCode(1);
    $this->assertDatabaseCount('jadwals', 1);
    $this->assertDatabaseCount('kelas_siswas', 1);
});
