<?php

use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Logbook;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->withoutVite();
    \Illuminate\Support\Facades\View::share('sekolah', new \App\Models\Sekolah);
    $this->travelTo(\Carbon\Carbon::parse('2026-09-21 08:00:00'));
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

test('attendance gate enforces role ownership and account state', function (string $role, ?int $employeeId, bool $active, string $category, bool $allowed) {
    $user = User::factory()->make(['role' => $role, 'pegawai_id' => $employeeId, 'is_active' => $active]);
    $jadwal = new Jadwal(['pegawai_id' => 10]);

    expect(Gate::forUser($user)->allows('input-presensi', [$jadwal, $category]))->toBe($allowed);
})->with([
    'admin' => ['admin', 20, true, 'mapel', true],
    'operator' => ['operator', 20, true, 'mapel', true],
    'kepala' => ['kepala', 20, true, 'mapel', true],
    'own schedule' => ['guru', 10, true, 'mapel', true],
    'other schedule' => ['guru', 20, true, 'mapel', false],
    'unlinked teacher' => ['guru', null, true, 'mapel', false],
    'inactive teacher' => ['guru', 10, false, 'mapel', false],
    'inactive admin' => ['admin', 10, false, 'mapel', false],
    'student' => ['siswa', 10, true, 'mapel', false],
    'staff' => ['staff', 10, true, 'mapel', false],
    'treasurer' => ['bendahara', 10, true, 'mapel', false],
    'forged substitute category' => ['guru', 10, true, 'piket_sub', false],
    'invalid category' => ['admin', 10, true, 'invalid', false],
]);

test('teachers cannot open another teachers attendance form directly', function () {
    $jadwal = Jadwal::factory()->create();
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => Pegawai::factory()->create()->id]);

    $this->actingAs($user)->get(route('absensi.create', ['jadwal_id' => $jadwal->id]))->assertForbidden();
});

test('unauthorized attendance writes cannot create or overwrite records', function (string $category) {
    $jadwal = Jadwal::factory()->create();
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => Pegawai::factory()->create()->id]);
    $student = Siswa::factory()->create();
    $logbook = Logbook::create(['jadwal_id' => $jadwal->id, 'pegawai_id' => $jadwal->pegawai_id,
        'kategori' => 'mapel', 'tanggal' => '2026-09-21', 'materi' => 'Materi asli']);

    $this->actingAs($user)->post(route('absensi.store'), [
        'jadwal_id' => $jadwal->id, 'kategori' => $category, 'tanggal' => '2026-09-21',
        'materi' => 'Materi diganti', 'attendance' => [$student->id => ['status' => 'Hadir']],
    ])->assertForbidden();

    $this->assertDatabaseCount('logbooks', 1);
    $this->assertDatabaseHas('logbooks', ['id' => $logbook->id, 'materi' => 'Materi asli', 'pegawai_id' => $jadwal->pegawai_id]);
    $this->assertDatabaseCount('absensis', 0);
})->with(['mapel', 'piket_sub']);

test('a teacher can open and save attendance for their own schedule', function () {
    $jadwal = Jadwal::factory()->create();
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => $jadwal->pegawai_id]);
    $student = Siswa::factory()->create();
    DB::table('kelas_siswas')->insert(['siswa_id' => $student->id, 'kelas_id' => $jadwal->kelas_id, 'tahun_id' => $jadwal->tahun_id]);
    $this->actingAs($user)->get(route('absensi.create', ['jadwal_id' => $jadwal->id]))
        ->assertOk()->assertViewHas('students', fn ($students) => $students->contains('id', $student->id));

    $this->post(route('absensi.store'), [
        'jadwal_id' => $jadwal->id, 'kategori' => 'mapel', 'tanggal' => '2026-09-21',
        'materi' => 'Materi hari ini', 'attendance' => [$student->id => ['status' => 'Hadir']],
        'redirect_to' => route('jadwal.presensiHarian'),
    ])->assertRedirect(route('jadwal.presensiHarian'))->assertSessionHas('message', 'Presensi berhasil disimpan.');

    $logbook = Logbook::sole();
    expect($logbook->pegawai_id)->toBe($jadwal->pegawai_id);
    $this->assertDatabaseHas('absensis', ['logbook_id' => $logbook->id, 'siswa_id' => $student->id, 'status' => 'Hadir']);
});

test('ordinary teachers only see their own schedules on daily attendance', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $own = Jadwal::factory()->create(['tahun_id' => $year->id]);
    Jadwal::factory()->create(['tahun_id' => $year->id]);
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => $own->pegawai_id]);

    $this->actingAs($user)->get(route('jadwal.presensiHarian', ['date' => '2026-09-21', 'view_mode' => 'all']))
        ->assertOk()->assertViewHas('jadwals', fn ($rows) => $rows->pluck('id')->all() === [$own->id])
        ->assertViewHas('viewMode', 'mine')
        ->assertSee(route('absensi.create', ['jadwal_id' => $own->id, 'date' => '2026-09-21']));
});

test('unlinked teachers cannot enter daily attendance', function () {
    Tahun::factory()->create(['isActive' => true]);
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => null]);

    $this->actingAs($user)->from(route('jadwal.index'))->get(route('jadwal.presensiHarian'))
        ->assertRedirect(route('jadwal.index'))->assertSessionHas('error', 'Akun anda tidak terhubung dengan data pegawai.');
});

test('duty teachers retain access to other schedules only on their duty day', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $jadwal = Jadwal::factory()->create(['tahun_id' => $year->id]);
    $user = User::factory()->create(['role' => 'guru', 'is_active' => true, 'pegawai_id' => Pegawai::factory()->create()->id]);
    JadwalPiket::create(['pegawai_id' => $user->pegawai_id, 'tahun_id' => $year->id, 'hari' => 'Senin']);

    expect(Gate::forUser($user)->allows('input-presensi', [$jadwal, 'piket_sub']))->toBeTrue();
    $this->actingAs($user)->get(route('absensi.create', ['jadwal_id' => $jadwal->id]))->assertOk();

    $this->travelTo(\Carbon\Carbon::parse('2026-09-22 08:00:00'));
    expect(Gate::forUser($user)->allows('input-presensi', [$jadwal, 'piket_sub']))->toBeFalse();
    $this->get(route('absensi.create', ['jadwal_id' => $jadwal->id]))->assertForbidden();
});

test('guests cannot open or submit attendance', function () {
    $this->get(route('absensi.create'))->assertRedirect(route('login'));
    $this->post(route('absensi.store'))->assertRedirect(route('login'));
});
