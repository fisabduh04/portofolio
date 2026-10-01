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
})->with([['masuk', 'Hadir'], ['pulang', 'Pulang']]);

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
    if ($ambiguous) {
        $other = Siswa::factory()->create();
        KelasSiswa::create(['siswa_id' => $other->id, 'kelas_id' => $schedule->kelas_id, 'tahun_id' => $schedule->tahun_id]);
        FaceSample::create(['siswa_id' => $other->id, 'model' => config('face-attendance.model'), 'descriptor' => $payload['descriptor'], 'created_by' => $actor->id]);
    } else {
        $payload['descriptor'] = array_fill(0, 128, 1);
    }

    $this->actingAs($actor)->postJson(route('face-attendance.store'), $payload)->assertInvalid(['descriptor']);

    $this->assertDatabaseCount('absensis', 0);
    $this->assertDatabaseCount('logbooks', 0);
})->with([false, true]);

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
        ->assertOk()->assertSee('Pindai & catat absensi', false)->assertSee('Performa absensi wajah')
        ->assertSee('Mulai pemindaian otomatis')->assertSee('data-scan-identity', false)
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
