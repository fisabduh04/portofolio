<?php

use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\PegawaiWajibHadir;
use App\Models\Sekolah;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Support\Facades\View;

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

/** @return array{tahun_id: int, version: string, complete: int} */
function wajibHadirForm(Tests\TestCase $test): array
{
    $response = $test->get(route('attendance.wajib-hadir.index'))->assertOk();

    return ['tahun_id' => $response->viewData('activeYear')->id, 'version' => $response->viewData('version'), 'complete' => 1];
}

test('saved manual days survive reload and keep automatic duties and other employees', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $teachers = Pegawai::factory()->count(2)->create(['jenisptk' => 'Guru']);
    $first = $teachers[0];
    $second = $teachers[1];
    Jadwal::factory()->create(['pegawai_id' => $first->id, 'tahun_id' => $year->id, 'hari' => 'Senin']);
    JadwalPiket::create(['pegawai_id' => $second->id, 'tahun_id' => $year->id, 'hari' => 'Selasa']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    $form = wajibHadirForm($this);

    $this->post(route('attendance.wajib-hadir.store'), $form + ['manual_days' => [$first->id => ['Minggu'], $second->id => ['Minggu']]])
        ->assertSessionHasNoErrors()->assertSessionHas('type', 'success');

    foreach ($teachers as $teacher) {
        $this->assertDatabaseHas('pegawai_wajib_hadirs', ['pegawai_id' => $teacher->id, 'tahun_id' => $year->id, 'hari' => 'Minggu']);
    }
    $this->assertDatabaseHas('pegawai_wajib_hadirs', ['pegawai_id' => $first->id, 'hari' => 'Senin']);
    $this->assertDatabaseHas('pegawai_wajib_hadirs', ['pegawai_id' => $second->id, 'hari' => 'Selasa']);
    $this->get(route('attendance.wajib-hadir.index'))->assertOk()
        ->assertViewHas('pegawais', fn ($rows) => $rows->every(fn ($teacher) => $teacher->wajibHadirs->contains('hari', 'Minggu')));
    $this->get(route('attendance.create', ['date' => '2026-09-27']))->assertOk()
        ->assertViewHas('pegawais', fn ($rows) => $rows->count() === 2);
});

test('an incomplete or invalid submission leaves saved days untouched', function (string $problem, string $field) {
    $year = Tahun::factory()->create(['isActive' => true]);
    $teacher = Pegawai::factory()->create();
    $saved = PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $year->id, 'hari' => 'Minggu']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    $form = wajibHadirForm($this);
    if ($problem === 'incomplete') {
        unset($form['complete']);
    } elseif ($problem === 'invalid day') {
        $form['manual_days'] = [$teacher->id => ['Monday']];
    } else {
        $form['manual_days'] = [999999 => ['Senin']];
    }

    $this->post(route('attendance.wajib-hadir.store'), $form)->assertInvalid($field === 'day' ? 'manual_days.'.$teacher->id.'.0' : $field);

    $this->assertModelExists($saved);
    $this->assertDatabaseCount('pegawai_wajib_hadirs', 1);
})->with([
    'truncated form' => ['incomplete', 'complete'],
    'invalid weekday' => ['invalid day', 'day'],
    'unknown employee' => ['unknown employee', 'manual_days'],
]);

test('a stale tab cannot overwrite newly saved days', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $teacher = Pegawai::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    $form = wajibHadirForm($this);
    $this->post(route('attendance.wajib-hadir.store'), $form + ['manual_days' => [$teacher->id => ['Minggu']]])->assertSessionHasNoErrors();

    $this->post(route('attendance.wajib-hadir.store'), $form)->assertInvalid('version');

    $this->assertDatabaseHas('pegawai_wajib_hadirs', ['pegawai_id' => $teacher->id, 'tahun_id' => $year->id, 'hari' => 'Minggu']);
});

test('unchecking removes only the intended days while preserving inactive employees and past years', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $past = Tahun::factory()->create(['isActive' => false]);
    $teacher = Pegawai::factory()->create();
    $inactive = Pegawai::factory()->create(['aktif' => 'Nonaktif']);
    $remove = PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $year->id, 'hari' => 'Minggu']);
    $history = PegawaiWajibHadir::create(['pegawai_id' => $teacher->id, 'tahun_id' => $past->id, 'hari' => 'Minggu']);
    $keep = PegawaiWajibHadir::create(['pegawai_id' => $inactive->id, 'tahun_id' => $year->id, 'hari' => 'Minggu']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));

    $this->post(route('attendance.wajib-hadir.store'), wajibHadirForm($this))->assertSessionHasNoErrors();

    $this->assertModelMissing($remove);
    $this->assertModelExists($history);
    $this->assertModelExists($keep);
});

test('changing the active year rejects a form opened for the previous year', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]));
    $form = wajibHadirForm($this);
    $year->update(['isActive' => false]);
    Tahun::factory()->create(['isActive' => true]);

    $this->post(route('attendance.wajib-hadir.store'), $form)->assertInvalid('tahun_id');

    $this->assertDatabaseCount('pegawai_wajib_hadirs', 0);
});
