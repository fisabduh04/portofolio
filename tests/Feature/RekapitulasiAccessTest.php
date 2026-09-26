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

test('rekapitulasi menu permission follows the allowed roles', function (string $role, bool $allowed) {
    $user = User::factory()->make(['role' => $role, 'is_active' => true]);

    expect(Gate::forUser($user)->allows('view-rekapitulasi'))->toBe($allowed);
})->with([
    'kepala' => ['kepala', true],
    'admin' => ['admin', true],
    'operator' => ['operator', true],
    'staff' => ['staff', true],
    'guru' => ['guru', true],
    'siswa' => ['siswa', false],
    'bendahara' => ['bendahara', false],
]);

test('active teachers can open student reports and see the rekapitulasi menu', function (string $routeName) {
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => true]));

    $this->get(route($routeName))->assertOk()
        ->assertSee('id="dropdown-rekap"', false)
        ->assertSee(route('absensi.rekap-harian'), false)
        ->assertSee(route('absensi.rekap-bulanan'), false)
        ->assertSee(route('absensi.rekap-tahunan'), false)
        ->assertSee(route('absensi.rekap-periode'), false);
})->with(['absensi.rekap-harian', 'absensi.rekap-bulanan', 'absensi.rekap-tahunan', 'absensi.rekap-periode']);

test('existing report roles retain access', function (string $role) {
    $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => true]));

    $this->get(route('absensi.rekap-bulanan'))->assertOk();
})->with(['kepala', 'admin', 'operator', 'staff']);

test('teachers can reach student exports and receive the class selection requirement', function (string $routeName, string $message) {
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => true]));

    $this->from(route('absensi.rekap-bulanan'))->get(route($routeName))
        ->assertRedirect(route('absensi.rekap-bulanan'))
        ->assertSessionHas('error', $message);
})->with([
    'daily' => ['absensi.rekap-harian.export', 'Silakan pilih kelas terlebih dahulu.'],
    'monthly' => ['absensi.rekap-bulanan.export', 'Pilih kelas terlebih dahulu.'],
    'yearly' => ['absensi.rekap-tahunan.export', 'Pilih kelas terlebih dahulu.'],
    'period' => ['absensi.export-periode', 'Pilih kelas terlebih dahulu.'],
]);

test('unauthorized roles cannot open student reports or exports directly', function (string $role, string $routeName) {
    $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => true]));

    $this->get(route($routeName))->assertForbidden();
})->with(['siswa', 'bendahara'])->with(['absensi.rekap-bulanan', 'absensi.rekap-bulanan.export']);

test('guests must log in before opening student reports or exports', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with(['absensi.rekap-bulanan', 'absensi.rekap-bulanan.export']);

test('inactive teachers cannot open student reports or exports', function (string $routeName) {
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => false]));

    $this->get(route($routeName))->assertRedirect(route('login'));
    $this->assertGuest();
})->with(['absensi.rekap-bulanan', 'absensi.rekap-bulanan.export']);

test('student report access does not grant teachers access to employee reports or settings', function (string $routeName) {
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => true]));

    $this->get(route($routeName))->assertForbidden();
})->with(['attendance.report.employee', 'attendance.report.employee.export', 'attendance.setting', 'jadwal.rekap']);

test('teachers cannot update employee attendance settings', function () {
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => true]));

    $this->post(route('attendance.updateSetting'), [])->assertForbidden();
});
