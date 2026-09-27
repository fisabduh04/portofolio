<?php

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Kelas;
use App\Models\Logbook;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->withoutVite();
    View::share('sekolah', new Sekolah);
    $this->travelTo(Carbon::parse('2026-09-27 10:00:00'));
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110145_create_siswas_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_04_110343_create_jadwals_table.php',
        'database/migrations/2024_05_04_110407_create_logbooks_table.php',
        'database/migrations/2024_05_04_110500_create_absensis_table.php',
        'database/migrations/2024_05_12_055121_create_kelas_siswas_table.php',
        'database/migrations/2026_01_28_031755_create_jadwal_pikets_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    $this->travelBack();
    DB::purge('sqlite');
});

/** @return array{user: User, jadwal: Jadwal, student: Siswa} */
function dailyAttendanceContext(string $className = 'X DKV'): array
{
    $year = Tahun::factory()->create(['isActive' => true]);
    $kelas = Kelas::factory()->create(['kelas' => $className]);
    $jadwal = Jadwal::factory()->create(['tahun_id' => $year->id, 'kelas_id' => $kelas->id, 'hari' => 'Senin']);
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => Pegawai::factory()->create()->id]);
    JadwalPiket::create(['pegawai_id' => $user->pegawai_id, 'tahun_id' => $year->id, 'hari' => 'Senin']);
    $student = Siswa::factory()->create();
    DB::table('kelas_siswas')->insert(['siswa_id' => $student->id, 'kelas_id' => $kelas->id, 'tahun_id' => $year->id]);

    return compact('user', 'jadwal', 'student');
}

test('duty teachers can reach the date picker on a non duty day', function () {
    ['user' => $user] = dailyAttendanceContext();

    $this->actingAs($user)->get(route('absensi.piket'))
        ->assertOk()->assertSee(route('absensi.harian.index'));
    $this->get(route('absensi.harian.index'))
        ->assertOk()->assertViewHas('date', '2026-09-27')->assertViewHas('isPiket', false)
        ->assertSee('type="date"', false)
        ->assertSee('Pilih tanggal yang sesuai dengan jadwal piket Anda.')
        ->assertDontSee('Absen Masuk');
});

test('duty teachers can open and save attendance for a selected school date on Sunday', function (string $className, string $type) {
    ['user' => $user, 'jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext($className);
    $formUrl = route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'type' => $type, 'date' => '2026-09-21']);

    $this->actingAs($user)->get(route('absensi.harian.index', ['date' => '2026-09-21']))
        ->assertOk()->assertViewHas('isPiket', true)->assertSee($formUrl);
    $this->get($formUrl)->assertOk()
        ->assertViewHas('date', '2026-09-21')
        ->assertViewHas('students', fn ($students) => $students->modelKeys() === [$student->id])
        ->assertSee('name="tanggal" value="2026-09-21"', false)
        ->assertSee('Senin, 21 September 2026');
    $this->post(route('absensi.harian.store'), [
        'tanggal' => '2026-09-21', 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_'.$type,
        'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertRedirect(route('absensi.harian.index', ['date' => '2026-09-21']))
        ->assertSessionHas('message', 'Presensi Harian Berhasil Disimpan.');

    $this->assertDatabaseHas('logbooks', [
        'tanggal' => '2026-09-21', 'kategori' => 'piket_'.$type,
        'jadwal_id' => $jadwal->id, 'kelas_id' => $jadwal->kelas_id, 'pegawai_id' => $user->pegawai_id,
    ]);
    $this->assertDatabaseHas('absensis', ['logbook_id' => Logbook::sole()->id, 'siswa_id' => $student->id, 'status' => 'Hadir']);
    $this->assertDatabaseMissing('logbooks', ['tanggal' => '2026-09-27']);
})->with(['X DKV', 'X TBSM', 'X Busana'])->with(['masuk', 'pulang']);

test('editing a selected date updates its attendance without overwriting another date', function () {
    ['user' => $user, 'jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext();
    $attributes = ['kelas_id' => $jadwal->kelas_id, 'jadwal_id' => $jadwal->id, 'pegawai_id' => $user->pegawai_id, 'kategori' => 'piket_masuk'];
    $selectedLog = Logbook::create($attributes + ['tanggal' => '2026-09-21']);
    $otherLog = Logbook::create($attributes + ['tanggal' => '2026-09-14']);
    Absensi::create(['logbook_id' => $selectedLog->id, 'siswa_id' => $student->id, 'status' => 'Hadir']);
    Absensi::create(['logbook_id' => $otherLog->id, 'siswa_id' => $student->id, 'status' => 'Izin']);

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-21']))
        ->assertOk()->assertViewHas('existingLogbook', fn ($log) => $log->is($selectedLog));
    $this->post(route('absensi.harian.store'), [
        'tanggal' => '2026-09-21', 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_masuk',
        'attendance' => [$student->id => ['status' => 'Sakit']],
    ])->assertRedirect(route('absensi.harian.index', ['date' => '2026-09-21']));

    $this->assertDatabaseCount('logbooks', 2);
    $this->assertDatabaseCount('absensis', 2);
    $this->assertDatabaseHas('absensis', ['logbook_id' => $selectedLog->id, 'status' => 'Sakit']);
    $this->assertDatabaseHas('absensis', ['logbook_id' => $otherLog->id, 'status' => 'Izin']);
});

test('Thursday attendance finds schedules and duty assignments in a second active period', function () {
    $firstYear = Tahun::factory()->create(['isActive' => true]);
    ['user' => $user, 'jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext();
    $jadwal->update(['hari' => 'Kamis']);
    JadwalPiket::where('pegawai_id', $user->pegawai_id)->update(['hari' => 'Kamis']);
    $otherStudent = Siswa::factory()->create();
    DB::table('kelas_siswas')->insert(['siswa_id' => $otherStudent->id, 'kelas_id' => $jadwal->kelas_id, 'tahun_id' => $firstYear->id]);

    $this->actingAs($user)->get(route('absensi.harian.index', ['date' => '2026-09-24']))
        ->assertOk()->assertViewHas('isPiket', true);
    $this->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-24']))
        ->assertOk()->assertViewHas('jadwal', fn ($selected) => $selected->is($jadwal))
        ->assertViewHas('students', fn ($students) => $students->modelKeys() === [$student->id]);
    $this->post(route('absensi.harian.store'), [
        'tanggal' => '2026-09-24', 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_masuk',
        'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertRedirect(route('absensi.harian.index', ['date' => '2026-09-24']))
        ->assertSessionHas('message', 'Presensi Harian Berhasil Disimpan.');

    $this->assertDatabaseHas('logbooks', ['jadwal_id' => $jadwal->id, 'tanggal' => '2026-09-24']);
    $this->assertDatabaseHas('absensis', ['siswa_id' => $student->id, 'status' => 'Hadir']);
});

test('being on duty today does not authorize attendance on a different duty day', function () {
    ['user' => $user, 'jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext();
    $this->travelTo(Carbon::parse('2026-09-21 20:00:00'));

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-22']))
        ->assertForbidden();
    $this->post(route('absensi.harian.store'), [
        'tanggal' => '2026-09-22', 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_masuk',
        'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertForbidden();

    $this->assertDatabaseCount('logbooks', 0);
    $this->assertDatabaseCount('absensis', 0);
});

test('daily attendance still defaults to today and allows input outside lesson hours', function () {
    ['user' => $user, 'jadwal' => $jadwal] = dailyAttendanceContext();
    $this->travelTo(Carbon::parse('2026-09-21 20:00:00'));

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id]))
        ->assertOk()->assertViewHas('date', '2026-09-21');
});

test('management can select a date without a duty assignment', function (string $role) {
    ['jadwal' => $jadwal] = dailyAttendanceContext();
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-21']))
        ->assertOk()->assertViewHas('date', '2026-09-21');
})->with(['admin', 'operator', 'kepala']);

test('teachers without an active duty assignment cannot enter daily attendance', function (bool $activeYear) {
    ['user' => $user, 'jadwal' => $jadwal] = dailyAttendanceContext();
    if ($activeYear) {
        $user->update(['pegawai_id' => Pegawai::factory()->create()->id]);
    } else {
        $jadwal->tahun->update(['isActive' => false]);
    }

    $this->actingAs($user)->get(route('absensi.harian.index'))->assertForbidden();
    $this->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-21']))->assertForbidden();
})->with(['no assignment' => true, 'inactive period' => false]);

test('missing selected day schedules produce a dated message and do not save attendance', function (bool $inactiveSchedule) {
    ['jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext();
    $user = User::factory()->create(['role' => 'admin', 'is_active' => true, 'pegawai_id' => $jadwal->pegawai_id]);
    $date = $inactiveSchedule ? '2026-09-21' : '2026-09-27';
    $day = $inactiveSchedule ? 'Senin' : 'Minggu';
    if ($inactiveSchedule) {
        $jadwal->update(['tahun_id' => Tahun::factory()->create(['isActive' => false])->id]);
    }
    $message = "Tidak ada jadwal pelajaran untuk kelas ini pada $day, $date di tahun ajaran aktif. Silakan pilih tanggal lain atau periksa jadwal kelas.";

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => $date]))
        ->assertRedirect(route('absensi.harian.index', ['date' => $date]))->assertSessionHas('error', $message);
    $this->post(route('absensi.harian.store'), [
        'tanggal' => $date, 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_masuk',
        'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertRedirect(route('absensi.harian.index', ['date' => $date]))->assertSessionHas('error', $message);

    $this->assertDatabaseCount('logbooks', 0);
    $this->assertDatabaseCount('absensis', 0);
})->with(['no Sunday schedule' => false, 'schedule in inactive period' => true]);

test('invalid selected dates are rejected before looking up or saving attendance', function (string $date) {
    ['user' => $user, 'jadwal' => $jadwal] = dailyAttendanceContext();
    $message = 'Tanggal presensi harus berupa tanggal yang valid (YYYY-MM-DD).';

    $this->actingAs($user)->get(route('absensi.harian.index', ['date' => $date]))->assertInvalid(['date' => $message]);
    $this->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => $date]))->assertInvalid(['date' => $message]);
    $this->post(route('absensi.harian.store'), ['tanggal' => $date])->assertInvalid(['tanggal' => $message]);

    $this->assertDatabaseCount('logbooks', 0);
})->with(['not-a-date', '2026-02-30']);

test('saving attendance requires an explicit date', function () {
    ['user' => $user] = dailyAttendanceContext();

    $this->actingAs($user)->post(route('absensi.harian.store'), [])
        ->assertInvalid(['tanggal' => 'Tanggal presensi wajib diisi.']);

    $this->assertDatabaseCount('logbooks', 0);
});

test('management receives a clear message when no academic year is active', function () {
    ['jadwal' => $jadwal, 'student' => $student] = dailyAttendanceContext();
    $jadwal->tahun->update(['isActive' => false]);
    $user = User::factory()->create(['role' => 'admin', 'is_active' => true, 'pegawai_id' => $jadwal->pegawai_id]);

    $this->actingAs($user)->get(route('absensi.harian.create', ['kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-21']))
        ->assertRedirect(route('absensi.harian.index', ['date' => '2026-09-21']))
        ->assertSessionHas('error', 'Tidak ada tahun ajaran aktif.');
    $this->post(route('absensi.harian.store'), [
        'tanggal' => '2026-09-21', 'kelas_id' => $jadwal->kelas_id, 'kategori' => 'piket_masuk',
        'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertRedirect(route('absensi.harian.index', ['date' => '2026-09-21']))
        ->assertSessionHas('error', 'Tidak ada tahun ajaran aktif.');

    $this->assertDatabaseCount('logbooks', 0);
});

test('daily attendance rejects categories other than entry or departure', function () {
    ['user' => $user, 'jadwal' => $jadwal] = dailyAttendanceContext();

    $this->actingAs($user)->get(route('absensi.harian.create', [
        'kelas_id' => $jadwal->kelas_id, 'date' => '2026-09-21', 'type' => 'sub',
    ]))->assertInvalid(['type']);
});

test('guests cannot select a date or save daily attendance', function () {
    $this->get(route('absensi.harian.index'))->assertRedirect(route('login'));
    $this->get(route('absensi.harian.create'))->assertRedirect(route('login'));
    $this->post(route('absensi.harian.store'), ['tanggal' => '2026-09-21'])->assertRedirect(route('login'));
});
