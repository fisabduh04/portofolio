<?php

use App\Livewire\Tahun\Data;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->tahunTestDatabase = null;
    if (getenv('TAHUN_TEST_MYSQL') === '1') {
        $connection = DB::connection('mysql')->getConfig();
        $this->tahunTestDatabase = 'tahun_test_'.bin2hex(random_bytes(8));
        config(['database.connections.tahun_test_admin' => array_replace($connection, ['name' => 'tahun_test_admin', 'database' => null, 'url' => null])]);
        DB::purge('tahun_test_admin');
        DB::connection('tahun_test_admin')->getSchemaBuilder()->createDatabase($this->tahunTestDatabase);
        config([
            'database.default' => 'tahun_test',
            'database.connections.tahun_test' => array_replace($connection, ['name' => 'tahun_test', 'database' => $this->tahunTestDatabase, 'url' => null]),
        ]);
        DB::purge('tahun_test');
        Schema::clearResolvedInstance('db.schema');
        $this->assertSame($this->tahunTestDatabase, Schema::getConnection()->getDatabaseName());
        $this->assertSame('tahun_test', Schema::getConnection()->getName());
    } else {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null, 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
    }
    $this->withoutVite();

    $this->artisan('migrate', ['--database' => config('database.default'), '--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110145_create_siswas_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_04_110343_create_jadwals_table.php',
        'database/migrations/2024_05_04_110407_create_logbooks_table.php',
        'database/migrations/2024_05_12_055121_create_kelas_siswas_table.php',
        'database/migrations/2026_01_28_031755_create_jadwal_pikets_table.php',
        'database/migrations/2026_02_09_092634_create_hari_liburs_table.php',
        'database/migrations/2026_02_11_062023_create_attendance_rules_table.php',
        'database/migrations/2026_02_17_153201_create_pegawai_wajib_hadirs_table.php',
        'database/migrations/2026_02_17_154753_create_pegawai_rule_allocations_table.php',
        'database/migrations/2026_09_12_113236_create_wali_kelas_table.php',
        'database/migrations/2026_09_16_075041_restrict_tahun_deletion_on_related_tables.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    if ($this->tahunTestDatabase !== null) {
        DB::purge('tahun_test');
        if (! preg_match('/^tahun_test_[a-f0-9]{16}$/', $this->tahunTestDatabase)) {
            throw new LogicException('Refusing to drop a database outside this test run.');
        }
        DB::connection('tahun_test_admin')->getSchemaBuilder()->dropDatabaseIfExists($this->tahunTestDatabase);
        DB::purge('tahun_test_admin');
    } else {
        DB::purge('sqlite');
    }
});

dataset('tahun dependencies', [
    'jadwal' => ['jadwals', 'jadwal mengajar'],
    'kelas siswa' => ['kelas_siswas', 'penempatan siswa'],
    'piket' => ['jadwal_pikets', 'jadwal piket'],
    'libur' => ['hari_liburs', 'hari libur'],
    'wajib hadir' => ['pegawai_wajib_hadirs', 'pengaturan wajib hadir'],
    'aturan absensi' => ['pegawai_rule_allocations', 'penetapan aturan absensi'],
    'wali kelas nonaktif' => ['wali_kelas', 'penugasan wali kelas'],
]);

function createTahunDependency(Tahun $tahun, string $table): int
{
    $attributes = match ($table) {
        'jadwals' => [
            'kelas_id' => Kelas::factory()->create()->id,
            'pegawai_id' => Pegawai::factory()->create()->id,
            'mapel_id' => DB::table('mapels')->insertGetId(['mapel' => 'Matematika']),
            'jam' => 1, 'mulai' => '07:00:00', 'akhir' => '08:00:00',
        ],
        'kelas_siswas' => [
            'kelas_id' => Kelas::factory()->create()->id,
            'siswa_id' => DB::table('siswas')->insertGetId(['nama' => 'Siswa Uji', 'nipd' => fake()->unique()->numerify('########')]),
        ],
        'jadwal_pikets', 'pegawai_wajib_hadirs' => ['pegawai_id' => Pegawai::factory()->create()->id, 'hari' => 'Senin'],
        'hari_liburs' => ['keterangan' => 'Libur semester'],
        'pegawai_rule_allocations' => [
            'pegawai_id' => Pegawai::factory()->create()->id,
            'attendance_rule_id' => DB::table('attendance_rules')->insertGetId([
                'name' => 'Aturan uji', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:00:00',
                'scan_masuk_start' => '06:00:00', 'scan_pulang_end' => '16:00:00',
            ]),
        ],
        'wali_kelas' => [
            'kelas_id' => Kelas::factory()->create()->id,
            'pegawai_id' => Pegawai::factory()->create()->id,
            'is_active' => false,
        ],
    };

    return DB::table($table)->insertGetId(['tahun_id' => $tahun->id, ...$attributes]);
}

it('deletes an unused inactive year and records its identity and actor', function (string $role) {
    $user = User::factory()->create(['role' => $role, 'is_active' => true]);
    $tahun = Tahun::factory()->create();
    $otherYear = Tahun::factory()->create();
    createTahunDependency($otherYear, 'hari_liburs');
    Log::spy();

    Livewire::actingAs($user)->test(Data::class)->call('del', $tahun->id)
        ->assertDispatched('showToast', message: 'Tahun ajaran berhasil dihapus.', type: 'success')
        ->assertViewHas('tahunlist', fn ($years) => ! $years->contains('id', $tahun->id));

    $this->assertModelMissing($tahun);
    $this->assertModelExists($otherYear);
    Log::shouldHaveReceived('info')->once()->with('Tahun ajaran dihapus', [
        'user_id' => $user->id, 'tahun_id' => $tahun->id, 'tahun' => $tahun->tahun, 'semester' => $tahun->semester,
    ]);
})->with(['admin', 'operator']);

it('refuses an active year even when unused', function () {
    $tahun = Tahun::factory()->create(['isActive' => true]);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(Data::class)
        ->call('del', $tahun->id)
        ->assertDispatched('showToast', message: 'Tahun ajaran aktif tidak dapat dihapus. Pindahkan tahun aktif terlebih dahulu.', type: 'warning');

    $this->assertModelExists($tahun);
});

it('explains each dependency and preserves the year and its records', function (string $table, string $label) {
    $tahun = Tahun::factory()->create();
    $childId = createTahunDependency($tahun, $table);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(Data::class)
        ->call('del', $tahun->id)
        ->assertDispatched('showToast', message: 'Tahun ajaran tidak dapat dihapus karena masih digunakan oleh 1 '.$label.'.', type: 'warning');

    $this->assertModelExists($tahun);
    $this->assertDatabaseHas($table, ['id' => $childId, 'tahun_id' => $tahun->id]);
})->with('tahun dependencies');

it('prevents direct database deletion of every referenced year', function (string $table, string $label) {
    $tahun = Tahun::factory()->create();
    $childId = createTahunDependency($tahun, $table);

    expect(fn () => DB::table('tahuns')->where('id', $tahun->id)->delete())->toThrow(QueryException::class);

    $this->assertModelExists($tahun);
    $this->assertDatabaseHas($table, ['id' => $childId, 'tahun_id' => $tahun->id]);
})->with('tahun dependencies');

it('refuses roles other than admin and operator', function (string $role) {
    $tahun = Tahun::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => $role, 'is_active' => true]))->test(Data::class)
        ->call('del', $tahun->id)->assertForbidden();

    $this->assertModelExists($tahun);
})->with(['guru', 'siswa', 'kepala', 'staff', 'bendahara']);

it('refuses inactive administrators', function () {
    $tahun = Tahun::factory()->create();

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => false]))->test(Data::class)
        ->call('del', $tahun->id)->assertForbidden();

    $this->assertModelExists($tahun);
});

it('refuses unauthenticated deletion', function () {
    $tahun = Tahun::factory()->create();

    Livewire::test(Data::class)->call('del', $tahun->id)->assertForbidden();

    $this->assertModelExists($tahun);
});

it('warns when another request has already deleted the year', function () {
    $tahun = Tahun::factory()->create();
    $component = Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(Data::class);
    $tahun->delete();

    $component->call('del', $tahun->id)
        ->assertDispatched('showToast', message: 'Tahun ajaran tidak ditemukan atau sudah dihapus.', type: 'warning');
});

it('lists multiple usages and preserves the teaching journal', function () {
    $tahun = Tahun::factory()->create();
    $jadwalId = createTahunDependency($tahun, 'jadwals');
    createTahunDependency($tahun, 'hari_liburs');
    createTahunDependency($tahun, 'hari_liburs');
    $logbookId = DB::table('logbooks')->insertGetId(['jadwal_id' => $jadwalId]);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(Data::class)
        ->call('del', $tahun->id)
        ->assertDispatched('showToast', message: 'Tahun ajaran tidak dapat dihapus karena masih digunakan oleh 1 jadwal mengajar, 2 hari libur.', type: 'warning');

    $this->assertModelExists($tahun);
    $this->assertDatabaseHas('logbooks', ['id' => $logbookId, 'jadwal_id' => $jadwalId]);
});

it('rolls back deletion and reports a database failure without exposing its details', function () {
    $tahun = Tahun::factory()->create();
    $exception = new QueryException(config('database.default'), 'delete from tahuns', [], new PDOException('Private database details'));
    Exceptions::fake();
    $events = clone Tahun::getEventDispatcher();
    Tahun::setEventDispatcher($events);
    Tahun::deleted(function () use ($exception): void {
        throw $exception;
    });
    Log::spy();

    try {
        Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))->test(Data::class)
            ->call('del', $tahun->id)
            ->assertDispatched('showToast', message: 'Tahun ajaran gagal dihapus. Silakan muat ulang halaman; jika masih gagal, hubungi administrator.', type: 'error');

        $this->assertModelExists($tahun);
        Exceptions::assertReported(fn (QueryException $reported) => $reported === $exception);
        Log::shouldNotHaveReceived('info');
    } finally {
        Tahun::setEventDispatcher(app('events'));
    }
});
