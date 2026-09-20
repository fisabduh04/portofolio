<?php

use App\Livewire\Jurusan\Data as JurusanData;
use App\Livewire\Kelas\Data as KelasData;
use App\Livewire\Mapel\Data as MapelData;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->masterTestDatabase = null;
    if (getenv('MASTER_TEST_MYSQL') === '1') {
        $connection = DB::connection('mysql')->getConfig();
        $this->masterTestDatabase = 'master_test_'.bin2hex(random_bytes(8));
        config(['database.connections.master_test_admin' => array_replace($connection, ['name' => 'master_test_admin', 'database' => null, 'url' => null])]);
        DB::purge('master_test_admin');
        DB::connection('master_test_admin')->getSchemaBuilder()->createDatabase($this->masterTestDatabase);
        config([
            'database.default' => 'master_test',
            'database.connections.master_test' => array_replace($connection, ['name' => 'master_test', 'database' => $this->masterTestDatabase, 'url' => null]),
        ]);
        DB::purge('master_test');
        Schema::clearResolvedInstance('db.schema');
        $this->assertSame($this->masterTestDatabase, Schema::getConnection()->getDatabaseName());
    } else {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null, 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
    }
    $this->withoutVite();
    $paths = array_values(array_filter(glob(database_path('migrations/*.php')), fn (string $path): bool => in_array(basename($path), [
        '0001_01_01_000000_create_users_table.php',
        '2024_05_04_110145_create_siswas_table.php',
        '2024_05_04_110159_create_pegawais_table.php',
        '2024_05_04_110200_create_jurusans_table.php',
        '2024_05_04_110210_create_kelas_table.php',
        '2024_05_04_110240_create_mapels_table.php',
        '2024_05_04_110313_create_tahuns_table.php',
        '2024_05_04_110343_create_jadwals_table.php',
        '2024_05_04_110407_create_logbooks_table.php',
        '2024_05_04_110500_create_absensis_table.php',
        '2024_05_12_055121_create_kelas_siswas_table.php',
        '2026_09_12_113236_create_wali_kelas_table.php',
        '2026_09_17_034304_restrict_kelas_jurusan_and_mapel_deletion.php',
    ], true)));
    $this->artisan('migrate', ['--database' => config('database.default'), '--path' => $paths, '--realpath' => true, '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    if ($this->masterTestDatabase !== null) {
        DB::purge('master_test');
        if (! preg_match('/^master_test_[a-f0-9]{16}$/', $this->masterTestDatabase)) {
            throw new LogicException('Refusing to drop a database outside this test run.');
        }
        DB::connection('master_test_admin')->getSchemaBuilder()->dropDatabaseIfExists($this->masterTestDatabase);
        DB::purge('master_test_admin');
    } else {
        DB::purge('sqlite');
    }
});

/** @return array{parent: \Illuminate\Database\Eloquent\Model, table: string, id: int} */
function masterDeletionDependency(string $relationship): array
{
    $parent = match ($relationship) {
        'jurusan kelas' => Jurusan::factory()->create(),
        'mapel jadwal' => Mapel::factory()->create(),
        default => Kelas::factory()->create(),
    };
    $table = match ($relationship) {
        'jurusan kelas' => 'kelas',
        'mapel jadwal', 'kelas jadwal' => 'jadwals',
        'kelas siswa' => 'kelas_siswas',
        'kelas logbook' => 'logbooks',
        'kelas wali' => 'wali_kelas',
    };
    $attributes = match ($relationship) {
        'jurusan kelas' => ['jurusan_id' => $parent->id, 'kelas' => 'X-1'],
        'mapel jadwal', 'kelas jadwal' => [
            'kelas_id' => $relationship === 'kelas jadwal' ? $parent->id : Kelas::factory()->create()->id,
            'mapel_id' => $relationship === 'mapel jadwal' ? $parent->id : Mapel::factory()->create()->id,
            'pegawai_id' => Pegawai::factory()->create()->id, 'tahun_id' => Tahun::factory()->create()->id,
            'jam' => 1, 'mulai' => '07:00:00', 'akhir' => '08:00:00',
        ],
        'kelas siswa' => ['kelas_id' => $parent->id, 'siswa_id' => Siswa::factory()->create()->id, 'tahun_id' => Tahun::factory()->create()->id],
        'kelas logbook' => ['kelas_id' => $parent->id],
        'kelas wali' => ['kelas_id' => $parent->id, 'pegawai_id' => Pegawai::factory()->create()->id, 'tahun_id' => Tahun::factory()->create()->id, 'is_active' => false],
    };

    return ['parent' => $parent, 'table' => $table, 'id' => DB::table($table)->insertGetId($attributes)];
}

dataset('master bulk components', [
    'kelas' => [KelasData::class, Kelas::class, 'kelas_selected_id', 'kelas siswa', 'Penghapusan dibatalkan. Kelas masih digunakan oleh penempatan siswa, jadwal, jurnal mengajar, atau wali kelas.'],
    'mapel' => [MapelData::class, Mapel::class, 'mapel_selected_id', 'mapel jadwal', 'Mapel tidak dapat dihapus karena masih digunakan dalam jadwal atau jurnal mengajar.'],
]);

it('deletes only the clicked class after search without deleting another selected class', function () {
    $target = Kelas::factory()->create(['kelas' => 'Kelas Target']);
    $other = Kelas::factory()->create(['kelas' => 'Kelas Lain']);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->test(KelasData::class)->set('kelas_selected_id', [$other->id])->set('search', 'Kelas Target')
        ->assertSee('wire:click="del('.$target->id.')"', false)
        ->call('del', $target->id)->assertDispatched('showToast', message: 'Data kelas berhasil dihapus.', type: 'success');

    $this->assertModelMissing($target);
    $this->assertModelExists($other);
});

it('protects a used class when its individual delete button is invoked', function (string $relationship) {
    $dependency = masterDeletionDependency($relationship);

    Livewire::actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]))
        ->test(KelasData::class)->call('del', $dependency['parent']->id)
        ->assertDispatched('showToast', type: 'warning');

    $this->assertModelExists($dependency['parent']);
    $this->assertDatabaseHas($dependency['table'], ['id' => $dependency['id']]);
})->with(['kelas jadwal', 'kelas siswa', 'kelas logbook', 'kelas wali']);

it('denies individual class deletion by a teacher', function () {
    $target = Kelas::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => 'guru', 'is_active' => true]))
        ->test(KelasData::class)->call('del', $target->id)->assertForbidden();

    $this->assertModelExists($target);
});

it('protects each master relation against direct database deletion', function (string $relationship) {
    $dependency = masterDeletionDependency($relationship);
    $parent = $dependency['parent'];

    expect(fn () => DB::table($parent->getTable())->where('id', $parent->id)->delete())->toThrow(QueryException::class);

    $this->assertModelExists($parent);
    $this->assertDatabaseHas($dependency['table'], ['id' => $dependency['id']]);
})->with(['jurusan kelas', 'mapel jadwal', 'kelas jadwal', 'kelas siswa', 'kelas logbook', 'kelas wali']);

it('keeps the entire bulk selection when one master record is used', function (string $component, string $model, string $property, string $relationship, string $message) {
    $unused = $model::factory()->create();
    $dependency = masterDeletionDependency($relationship);
    $ids = [$unused->id, $dependency['parent']->id];

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test($component)
        ->set($property, $ids)->call('del')->assertDispatched('showToast', message: $message, type: 'warning')
        ->assertSet($property, $ids);

    $this->assertModelExists($unused);
    $this->assertModelExists($dependency['parent']);
    $this->assertDatabaseHas($dependency['table'], ['id' => $dependency['id']]);
})->with('master bulk components');

it('deletes unused bulk selections and resets selection', function (string $component, string $model, string $property) {
    $records = $model::factory()->count(2)->create();

    Livewire::actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]))->test($component)
        ->set($property, $records->modelKeys())->call('del')->assertSet($property, [])
        ->assertDispatched('showToast', fn (string $event, array $params): bool => $params['type'] === 'success');

    foreach ($records as $record) {
        $this->assertModelMissing($record);
    }
})->with([[KelasData::class, Kelas::class, 'kelas_selected_id'], [MapelData::class, Mapel::class, 'mapel_selected_id']]);

it('warns about an empty bulk selection', function (string $component, string $message) {
    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test($component)
        ->call('del')->assertDispatched('showToast', message: $message, type: 'warning');
})->with([[KelasData::class, 'Pilih kelas yang akan dihapus.'], [MapelData::class, 'Pilih mata pelajaran yang akan dihapus.']]);

it('rejects invalid bulk ids without deleting valid records', function (string $component, string $model, string $property) {
    $record = $model::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test($component)
        ->set($property, [$record->id, 999999])->call('del')->assertHasErrors([$property.'.1' => 'exists']);

    $this->assertModelExists($record);
})->with([[KelasData::class, Kelas::class, 'kelas_selected_id'], [MapelData::class, Mapel::class, 'mapel_selected_id']]);

it('refuses a used jurusan through Livewire', function () {
    $class = Kelas::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(JurusanData::class)
        ->call('del', $class->jurusan_id)->assertDispatched('showToast', message: 'Jurusan tidak dapat dihapus karena masih memiliki kelas.', type: 'warning');

    $this->assertModelExists($class);
    $this->assertModelExists($class->jurusan);
});

it('deletes a jurusan without classes and preserves its optional mapel', function () {
    $jurusan = Jurusan::factory()->create();
    $mapel = Mapel::factory()->create(['jurusan_id' => $jurusan->id]);

    Livewire::actingAs(User::factory()->create(['role' => 'operator', 'is_active' => true]))->test(JurusanData::class)
        ->call('del', $jurusan->id)->assertDispatched('showToast', message: 'Data Jurusan berhasil dihapus.', type: 'success');

    $this->assertModelMissing($jurusan);
    $this->assertModelExists($mapel);
    expect($mapel->fresh()->jurusan_id)->toBeNull();
});

it('warns when a jurusan was already deleted', function () {
    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(JurusanData::class)
        ->call('del', 999999)->assertDispatched('showToast', message: 'Jurusan tidak ditemukan atau sudah dihapus.', type: 'warning');
});

it('protects the single deletion controller and explains the refusal', function (string $relationship, string $route, string $message) {
    $dependency = masterDeletionDependency($relationship);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route($route.'.index'))
        ->delete(route($route.'.destroy', $dependency['parent']))->assertRedirect(route($route.'.index'))
        ->assertSessionHas('message', $message)->assertSessionHas('type', 'warning');

    $this->assertModelExists($dependency['parent']);
    $this->assertDatabaseHas($dependency['table'], ['id' => $dependency['id']]);
})->with([
    ['kelas logbook', 'kelas', 'Kelas tidak dapat dihapus karena masih digunakan oleh penempatan siswa, jadwal, jurnal mengajar, atau wali kelas.'],
    ['mapel jadwal', 'mapel', 'Mapel tidak dapat dihapus karena masih digunakan dalam jadwal atau jurnal mengajar.'],
]);

it('allows an unused master record to be deleted through its controller', function (string $model, string $route) {
    $record = $model::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route($route.'.index'))
        ->delete(route($route.'.destroy', $record))->assertRedirect(route($route.'.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($record);
})->with([[Kelas::class, 'kelas'], [Mapel::class, 'mapel']]);

it('refuses unauthorized Livewire deletion', function (string $component, string $role, bool $active) {
    $record = Jurusan::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => $role, 'is_active' => $active]))->test($component)
        ->call('del', $record->id)->assertForbidden();

    $this->assertModelExists($record);
})->with([KelasData::class, MapelData::class, JurusanData::class])->with([['guru', true], ['admin', false]]);

it('refuses guest Livewire deletion', function (string $component) {
    Livewire::test($component)->call('del', 1)->assertForbidden();
})->with([KelasData::class, MapelData::class, JurusanData::class]);

it('rolls back unexpected Livewire deletion errors and reports them safely', function (string $component, string $model, ?string $property, string $label) {
    $record = $model::factory()->create();
    $exception = new QueryException(config('database.default'), 'delete', [], new PDOException('Private database details'));
    $events = clone $model::getEventDispatcher();
    $model::setEventDispatcher($events);
    $model::deleted(function () use ($exception): void {
        throw $exception;
    });
    Exceptions::fake();

    try {
        $test = Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test($component);
        if ($property !== null) {
            $test->set($property, [$record->id]);
        }
        $test->call('del', ...($property === null ? [$record->id] : []))
            ->assertDispatched('showToast', message: 'Data '.$label.' gagal dihapus. Silakan coba kembali.', type: 'error');

        $this->assertModelExists($record);
        Exceptions::assertReported(fn (QueryException $reported): bool => $reported === $exception);
    } finally {
        $model::setEventDispatcher(app('events'));
    }
})->with([
    [KelasData::class, Kelas::class, 'kelas_selected_id', 'kelas'],
    [MapelData::class, Mapel::class, 'mapel_selected_id', 'mata pelajaran'],
    [JurusanData::class, Jurusan::class, null, 'jurusan'],
]);

it('rolls back unexpected controller deletion errors and reports them safely', function (string $model, string $route, string $label) {
    $record = $model::factory()->create();
    $exception = new QueryException(config('database.default'), 'delete', [], new PDOException('Private database details'));
    $events = clone $model::getEventDispatcher();
    $model::setEventDispatcher($events);
    $model::deleted(function () use ($exception): void {
        throw $exception;
    });
    Exceptions::fake();

    try {
        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->from(route($route.'.index'))
            ->delete(route($route.'.destroy', $record))->assertRedirect(route($route.'.index'))
            ->assertSessionHas('message', 'Data '.$label.' gagal dihapus. Silakan coba kembali.')->assertSessionHas('type', 'error');

        $this->assertModelExists($record);
        Exceptions::assertReported(fn (QueryException $reported): bool => $reported === $exception);
    } finally {
        $model::setEventDispatcher(app('events'));
    }
})->with([[Kelas::class, 'kelas', 'kelas'], [Mapel::class, 'mapel', 'mata pelajaran']]);

it('preserves existing journals and attendance when upgrading restrictive keys', function () {
    $migration = require database_path('migrations/2026_09_17_034304_restrict_kelas_jurusan_and_mapel_deletion.php');
    $migration->down();
    $class = Kelas::factory()->create();
    $logbookId = DB::table('logbooks')->insertGetId(['kelas_id' => $class->id]);
    $attendanceId = DB::table('absensis')->insertGetId(['logbook_id' => $logbookId, 'siswa_id' => Siswa::factory()->create()->id, 'status' => 'Hadir']);

    $migration->up();

    expect(fn () => $class->delete())->toThrow(QueryException::class);
    $this->assertModelExists($class);
    $this->assertDatabaseHas('logbooks', ['id' => $logbookId]);
    $this->assertDatabaseHas('absensis', ['id' => $attendanceId]);

    $migration->down();
    $this->assertDatabaseHas('absensis', ['id' => $attendanceId]);
    $class->delete();
    $this->assertDatabaseMissing('logbooks', ['id' => $logbookId]);
    $this->assertDatabaseMissing('absensis', ['id' => $attendanceId]);
});
