<?php

use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->personTestDatabase = null;
    if (getenv('PERSON_TEST_MYSQL') === '1') {
        $connection = DB::connection('mysql')->getConfig();
        $this->personTestDatabase = 'person_test_'.bin2hex(random_bytes(8));
        config(['database.connections.person_test_admin' => array_replace($connection, ['name' => 'person_test_admin', 'database' => null, 'url' => null])]);
        DB::purge('person_test_admin');
        DB::connection('person_test_admin')->getSchemaBuilder()->createDatabase($this->personTestDatabase);
        config([
            'database.default' => 'person_test',
            'database.connections.person_test' => array_replace($connection, ['name' => 'person_test', 'database' => $this->personTestDatabase, 'url' => null]),
        ]);
        DB::purge('person_test');
        Schema::clearResolvedInstance('db.schema');
        $this->assertSame($this->personTestDatabase, Schema::getConnection()->getDatabaseName());
    } else {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null, 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
    }
    $this->withoutVite();
    $paths = array_values(array_filter(glob(database_path('migrations/*.php')), fn (string $path): bool => ! str_contains($path, 'alter_absensis_status')));
    $this->artisan('migrate', ['--database' => config('database.default'), '--path' => $paths, '--realpath' => true, '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    if ($this->personTestDatabase !== null) {
        DB::purge('person_test');
        if (! preg_match('/^person_test_[a-f0-9]{16}$/', $this->personTestDatabase)) {
            throw new LogicException('Refusing to drop a database outside this test run.');
        }
        DB::connection('person_test_admin')->getSchemaBuilder()->dropDatabaseIfExists($this->personTestDatabase);
        DB::purge('person_test_admin');
    } else {
        DB::purge('sqlite');
    }
});

function studentDeletionHistory(Siswa $student, string $table): int
{
    $attributes = match ($table) {
        'absensis' => ['logbook_id' => DB::table('logbooks')->insertGetId([]), 'status' => 'Hadir'],
        'kelas_siswas' => ['kelas_id' => Kelas::factory()->create()->id, 'tahun_id' => Tahun::factory()->create()->id],
    };

    return DB::table($table)->insertGetId(['siswa_id' => $student->id, ...$attributes]);
}

function employeeDeletionHistory(Pegawai $employee, string $table): int
{
    $attributes = match ($table) {
        'jadwals' => [
            'kelas_id' => Kelas::factory()->create()->id, 'tahun_id' => Tahun::factory()->create()->id,
            'mapel_id' => DB::table('mapels')->insertGetId(['mapel' => 'Matematika']),
            'jam' => 1, 'mulai' => '07:00:00', 'akhir' => '08:00:00',
        ],
        'jadwal_pikets', 'pegawai_wajib_hadirs' => ['tahun_id' => Tahun::factory()->create()->id, 'hari' => 'Senin'],
        'attendance_logs' => ['scan_time' => '2026-09-01 07:00:00'],
        'pegawai_absensis' => ['tanggal' => '2026-09-01', 'nominal_gaji' => 100000, 'total_honor' => 100000],
        'pegawai_izins' => ['jenis_izin' => 'Sakit', 'tanggal_mulai' => '2026-09-01', 'tanggal_akhir' => '2026-09-01'],
        'pegawai_rule_allocations', 'pegawai_schedule_overrides' => [
            'attendance_rule_id' => DB::table('attendance_rules')->insertGetId([
                'name' => 'Aturan uji', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:00:00',
                'scan_masuk_start' => '06:00:00', 'scan_pulang_end' => '16:00:00',
            ]),
            ...($table === 'pegawai_rule_allocations' ? ['tahun_id' => Tahun::factory()->create()->id] : ['date' => '2026-09-01']),
        ],
        'special_event_participants' => ['special_event_id' => DB::table('special_events')->insertGetId(['name' => 'Rapat', 'date' => '2026-09-01'])],
        'logbooks' => [],
        'wali_kelas' => ['tahun_id' => Tahun::factory()->create()->id, 'kelas_id' => Kelas::factory()->create()->id, 'is_active' => false],
    };

    return DB::table($table)->insertGetId(['pegawai_id' => $employee->id, ...$attributes]);
}

it('blocks direct student deletion and retains each kind of history', function (string $table) {
    $student = Siswa::factory()->create();
    $childId = studentDeletionHistory($student, $table);

    expect(fn () => DB::table('siswas')->where('id', $student->id)->delete())->toThrow(QueryException::class);

    $this->assertModelExists($student);
    $this->assertDatabaseHas($table, ['id' => $childId, 'siswa_id' => $student->id]);
})->with(['absensis', 'kelas_siswas']);

it('blocks direct employee deletion and retains each kind of history', function (string $table) {
    $employee = Pegawai::factory()->create();
    $childId = employeeDeletionHistory($employee, $table);

    expect(fn () => DB::table('pegawais')->where('id', $employee->id)->delete())->toThrow(QueryException::class);

    $this->assertModelExists($employee);
    $this->assertDatabaseHas($table, ['id' => $childId, 'pegawai_id' => $employee->id]);
})->with(['jadwals', 'jadwal_pikets', 'attendance_logs', 'pegawai_absensis', 'pegawai_izins', 'pegawai_wajib_hadirs', 'pegawai_rule_allocations', 'pegawai_schedule_overrides', 'special_event_participants', 'logbooks', 'wali_kelas']);

it('explains refused student deletion and preserves the photo', function () {
    Storage::fake('public');
    $student = Siswa::factory()->create(['foto' => 'students/photo.jpg']);
    Storage::disk('public')->put($student->foto, 'photo');
    studentDeletionHistory($student, 'kelas_siswas');

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route('siswa.index'))
        ->delete(route('siswa.destroy', $student))->assertRedirect(route('siswa.index'))
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', 'Penghapusan dibatalkan. Siswa masih memiliki data terkait. Gunakan status nonaktif untuk mempertahankan riwayat.');

    $this->assertModelExists($student);
    Storage::disk('public')->assertExists($student->foto);
});

it('rolls back the entire bulk deletion and keeps photos when one student has history', function () {
    Storage::fake('public');
    $unused = Siswa::factory()->create(['foto' => 'students/unused.jpg']);
    $used = Siswa::factory()->create(['foto' => 'students/used.jpg']);
    Storage::disk('public')->put($unused->foto, 'photo');
    Storage::disk('public')->put($used->foto, 'photo');
    studentDeletionHistory($used, 'kelas_siswas');

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route('siswa.index'))
        ->delete(route('siswa.bulkDelete'), ['ids' => [$unused->id, $used->id]])
        ->assertRedirect(route('siswa.index'))->assertSessionHas('type', 'warning');

    $this->assertModelExists($unused);
    $this->assertModelExists($used);
    Storage::disk('public')->assertExists([$unused->foto, $used->foto]);
});

it('deletes an unused student and removes the photo after success', function () {
    Storage::fake('public');
    $student = Siswa::factory()->create(['foto' => 'students/unused.jpg']);
    Storage::disk('public')->put($student->foto, 'photo');

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->delete(route('siswa.destroy', $student))
        ->assertRedirect(route('siswa.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($student);
    Storage::disk('public')->assertMissing($student->foto);
});

it('deletes all unused selected students and their photos', function () {
    Storage::fake('public');
    $students = Siswa::factory()->count(2)->sequence(['foto' => 'students/one.jpg'], ['foto' => 'students/two.jpg'])->create();
    foreach ($students as $student) {
        Storage::disk('public')->put($student->foto, 'photo');
    }

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route('siswa.index'))
        ->delete(route('siswa.bulkDelete'), ['ids' => $students->modelKeys()])
        ->assertRedirect(route('siswa.index'))->assertSessionHas('message', '2 data siswa berhasil dihapus');

    foreach ($students as $student) {
        $this->assertModelMissing($student);
        Storage::disk('public')->assertMissing($student->foto);
    }
});

it('explains refused employee deletion without losing honor history', function () {
    $employee = Pegawai::factory()->create();
    $childId = employeeDeletionHistory($employee, 'pegawai_absensis');

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route('pegawai.index'))
        ->delete(route('pegawai.destroy', $employee))->assertRedirect(route('pegawai.index'))
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', 'Pegawai tidak dapat dihapus karena masih memiliki data terkait. Gunakan status nonaktif untuk mempertahankan riwayat.');

    $this->assertModelExists($employee);
    $this->assertDatabaseHas('pegawai_absensis', ['id' => $childId, 'total_honor' => 100000]);
});

it('deletes an unused employee', function () {
    $employee = Pegawai::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->delete(route('pegawai.destroy', $employee))
        ->assertRedirect(route('pegawai.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($employee);
});

it('reports unexpected database errors and rolls back deletion', function (string $model, string $route) {
    $person = $model::factory()->create();
    $events = clone $model::getEventDispatcher();
    $model::setEventDispatcher($events);
    $exception = new QueryException('sqlite', 'delete', [], new PDOException('Private database details'));
    $model::deleted(function () use ($exception): void {
        throw $exception;
    });
    Exceptions::fake();

    try {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route($route.'.index'))
            ->delete(route($route.'.destroy', $person))->assertRedirect(route($route.'.index'))
            ->assertSessionHas('message', 'Data '.$route.' gagal dihapus. Silakan coba kembali.')->assertSessionHas('type', 'error');

        $this->assertModelExists($person);
        Exceptions::assertReported(fn (QueryException $reported): bool => $reported === $exception);
    } finally {
        $model::setEventDispatcher(app('events'));
    }
})->with([[Siswa::class, 'siswa'], [Pegawai::class, 'pegawai']]);

it('warns when no students are selected', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route('siswa.index'))
        ->delete(route('siswa.bulkDelete'), ['ids' => []])->assertSessionHas('message', 'Tidak ada data yang dipilih');
});

it('preserves existing history when upgrading and reversing the foreign keys', function () {
    $migration = require database_path('migrations/2026_09_17_031831_restrict_siswa_and_pegawai_deletion_on_history_tables.php');
    $migration->down();
    $student = Siswa::factory()->create();
    $employee = Pegawai::factory()->create();
    $studentHistory = studentDeletionHistory($student, 'absensis');
    $employeeHistory = employeeDeletionHistory($employee, 'pegawai_absensis');

    $migration->up();

    expect(fn () => $student->delete())->toThrow(QueryException::class);
    expect(fn () => $employee->delete())->toThrow(QueryException::class);
    $this->assertDatabaseHas('absensis', ['id' => $studentHistory, 'siswa_id' => $student->id]);
    $this->assertDatabaseHas('pegawai_absensis', ['id' => $employeeHistory, 'total_honor' => 100000]);

    $migration->down();
    $this->assertModelExists($student);
    $this->assertModelExists($employee);
    $student->delete();
    $employee->delete();
    $this->assertDatabaseMissing('absensis', ['id' => $studentHistory]);
    $this->assertDatabaseMissing('pegawai_absensis', ['id' => $employeeHistory]);
});

it('requires authentication before deleting people', function (string $model, string $route) {
    $person = $model::factory()->create();

    $this->delete(route($route.'.destroy', $person))->assertRedirect(route('login'));

    $this->assertModelExists($person);
})->with([[Siswa::class, 'siswa'], [Pegawai::class, 'pegawai']]);

it('rejects malformed bulk selections', function (mixed $ids, string $key) {
    $student = Siswa::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->delete(route('siswa.bulkDelete'), ['ids' => $ids])
        ->assertSessionHasErrors($key);

    $this->assertModelExists($student);
})->with([['invalid', 'ids'], [['invalid'], 'ids.0'], [[999999], 'ids.0']]);
