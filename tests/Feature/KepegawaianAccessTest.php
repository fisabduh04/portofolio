<?php

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->withoutVite();
    View::share('sekolah', new Sekolah);
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

test('only administrators have permission to access kepegawaian', function (string $role, bool $allowed) {
    $user = User::factory()->make(['role' => $role, 'is_active' => true]);

    expect(Gate::forUser($user)->allows('view-kepegawaian'))->toBe($allowed);
})->with([
    'admin' => ['admin', true],
    'operator' => ['operator', false],
    'kepala' => ['kepala', false],
    'staff' => ['staff', false],
    'bendahara' => ['bendahara', false],
    'guru' => ['guru', false],
    'siswa' => ['siswa', false],
]);

test('administrators can open kepegawaian and see its menu', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

    $this->get(route('attendance.rules.create'))->assertOk()
        ->assertSee('id="dropdown-kepegawaian"', false);
});

test('operators retain student reports without the kepegawaian menu', function () {
    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]));

    $this->get(route('absensi.rekap-bulanan'))->assertOk()
        ->assertDontSee('id="dropdown-kepegawaian"', false);
});

test('operators cannot open kepegawaian pages directly', function (string $routeName) {
    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]));

    $this->get(route($routeName))->assertForbidden();
})->with([
    'attendance.rules.index',
    'attendance.index',
    'attendance.payroll.index',
    'attendance.report',
    'attendance.report.employee',
    'attendance.report.employee.export',
    'attendance.rekap-pegawai',
    'attendance.wajib-hadir.index',
    'attendance.events.index',
    'attendance.izin.index',
    'attendance.fingerprint.index',
    'attendance.setting',
    'attendance.overrides.index',
    'attendance.mandatory.index',
    'attendance.create',
]);

test('operators cannot submit kepegawaian changes', function (string $routeName) {
    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]));

    $this->post(route($routeName), [])->assertForbidden();
})->with([
    'attendance.rules.store',
    'attendance.store',
    'attendance.process',
    'attendance.updateSetting',
    'attendance.wajib-hadir.store',
    'attendance.events.store',
    'attendance.izin.store',
    'attendance.fingerprint.store',
    'attendance.overrides.store',
    'attendance.mandatory.store',
]);

test('operators cannot download employee salary slips', function () {
    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]));

    $this->get(route('attendance.payroll.slip', ['id' => 1]))->assertForbidden();
});

test('guests must log in to access kepegawaian', function () {
    $this->get(route('attendance.rules.create'))->assertRedirect(route('login'));
});

test('inactive administrators cannot access kepegawaian', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => false]));

    $this->get(route('attendance.rules.create'))->assertRedirect(route('login'));
    $this->assertGuest();
});
