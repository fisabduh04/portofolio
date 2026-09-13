<?php

use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\Tahun;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->waliTestDatabase = null;
    if (getenv('WALIKELAS_TEST_MYSQL') === '1') {
        $connection = DB::connection('mysql')->getConfig();
        $databaseName = 'walikelas_test_'.bin2hex(random_bytes(8));
        config(['database.connections.walikelas_test_admin' => array_replace($connection, ['name' => 'walikelas_test_admin', 'database' => null, 'url' => null])]);
        DB::purge('walikelas_test_admin');
        DB::connection('walikelas_test_admin')->getSchemaBuilder()->createDatabase($databaseName);
        $this->waliTestDatabase = $databaseName;
        config([
            'database.default' => 'walikelas_test',
            'database.connections.walikelas_test' => array_replace($connection, ['name' => 'walikelas_test', 'database' => $databaseName, 'url' => null]),
        ]);
        DB::purge('walikelas_test');
        Schema::clearResolvedInstance('db.schema');
        $this->assertSame($databaseName, DB::connection()->selectOne('SELECT DATABASE() AS name')->name);
        $this->assertSame($databaseName, Schema::getConnection()->getDatabaseName());
        $this->assertSame('walikelas_test', Schema::getConnection()->getName());
    } else {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
        DB::purge('sqlite');
    }
    $this->withoutVite();
    View::share('sekolah', new Sekolah);

    $this->artisan('migrate', ['--database' => config('database.default'), '--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2026_09_12_113236_create_wali_kelas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    if ($this->waliTestDatabase !== null) {
        DB::purge('walikelas_test');
        if (! preg_match('/^walikelas_test_[a-f0-9]{16}$/', $this->waliTestDatabase)) {
            throw new LogicException('Refusing to drop a database outside this test run.');
        }
        DB::connection('walikelas_test_admin')->getSchemaBuilder()->dropDatabaseIfExists($this->waliTestDatabase);
        DB::purge('walikelas_test_admin');
    } else {
        DB::purge('sqlite');
    }
});

it('renders only the active period by default and escapes assignment notes', function () {
    $period = Tahun::factory()->create(['isActive' => true, 'tahun' => '2026/2027']);
    $assignment = WaliKelas::factory()->create(['tahun_id' => $period->id, 'keterangan' => '<script>alert(1)</script>']);
    WaliKelas::factory()->create();

    $response = $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->get(route('walikelas.index'));

    $response->assertSee('2026/2027 — Ganjil')->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('data-edit-assignment', false)
        ->assertSee('data-status-form', false)
        ->assertSee('Tambah Wali Kelas Lain')
        ->assertViewHas('penugasans', fn ($rows) => $rows->total() === 1 && $rows->first()->id === $assignment->id);
});

it('filters an older period by search class and inactive status', function () {
    Tahun::factory()->create(['isActive' => true]);
    $assignment = WaliKelas::factory()->create(['is_active' => false]);
    WaliKelas::factory()->create(['tahun_id' => $assignment->tahun_id]);

    $response = $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => 1]))->get(route('walikelas.index', [
        'tahun_id' => $assignment->tahun_id, 'kelas_id' => $assignment->kelas_id,
        'search' => $assignment->pegawai->name, 'status' => 'nonaktif', 'per_page' => 25,
    ]));

    $response->assertViewHas('penugasans', fn ($rows) => $rows->total() === 1 && $rows->first()->id === $assignment->id);
});

it('renders an empty page when no periods exist', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.index'))->assertSee('Belum ada periode.');
});

it('stores a batch in the selected period and keeps the replaced assignment inactive', function () {
    $old = WaliKelas::factory()->create();
    $pegawai = Pegawai::factory()->create();
    $otherClass = Kelas::factory()->create();

    $response = $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => $old->tahun_id,
        'penugasans' => [
            ['kelas_id' => $old->kelas_id, 'pegawai_id' => $pegawai->id, 'is_active' => '1', 'keterangan' => 'Pengganti'],
            ['kelas_id' => $otherClass->id, 'pegawai_id' => $pegawai->id, 'is_active' => '1'],
        ],
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('walikelas.index', ['tahun_id' => $old->tahun_id]));
    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => false]);
    $this->assertDatabaseHas('wali_kelas', ['tahun_id' => $old->tahun_id, 'kelas_id' => $old->kelas_id, 'pegawai_id' => $pegawai->id, 'is_active' => true]);
    $this->assertDatabaseHas('wali_kelas', ['tahun_id' => $old->tahun_id, 'kelas_id' => $otherClass->id, 'pegawai_id' => $pegawai->id, 'is_active' => true]);
});

it('does not deactivate the current wali when an inactive assignment is added', function () {
    $old = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => $old->tahun_id,
        'penugasans' => [['kelas_id' => $old->kelas_id, 'pegawai_id' => Pegawai::factory()->create()->id, 'is_active' => '0']],
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => true]);
    $this->assertDatabaseCount('wali_kelas', 2);
});

it('updates existing assignments when a batch is submitted again', function () {
    $old = WaliKelas::factory()->create();
    $otherClass = Kelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => $old->tahun_id,
        'penugasans' => [
            ['kelas_id' => $otherClass->id, 'pegawai_id' => $old->pegawai_id, 'is_active' => 1],
            ['kelas_id' => $old->kelas_id, 'pegawai_id' => $old->pegawai_id, 'is_active' => 1],
        ],
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseCount('wali_kelas', 2);
    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => true]);
});

it('rejects repeated classes in a batch', function () {
    $period = Tahun::factory()->create();
    $row = ['kelas_id' => Kelas::factory()->create()->id, 'pegawai_id' => Pegawai::factory()->create()->id, 'is_active' => 1];

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => $period->id, 'penugasans' => [$row, $row],
    ])->assertSessionHasErrors(['penugasans.0.kelas_id' => 'Kelas tidak boleh berulang dalam satu pengiriman.']);

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('rejects invalid references and status before writing', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => 999,
        'penugasans' => [['kelas_id' => 999, 'pegawai_id' => 999, 'is_active' => 'maybe']],
    ])->assertSessionHasErrors([
        'tahun_id' => 'Periode tidak ditemukan.',
        'penugasans.0.kelas_id' => 'Kelas tidak ditemukan.',
        'penugasans.0.pegawai_id' => 'Pegawai tidak ditemukan.',
        'penugasans.0.is_active' => 'Status penugasan tidak valid.',
    ]);

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('reactivates an existing assignment and updates its notes without losing other rows', function () {
    $old = WaliKelas::factory()->create(['is_active' => false]);
    $current = WaliKelas::factory()->create(['tahun_id' => $old->tahun_id, 'kelas_id' => $old->kelas_id]);

    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => 1]))->put(route('walikelas.update', $old), [
        'is_active' => 1, 'keterangan' => 'Kembali bertugas',
    ])->assertSessionHasNoErrors()->assertRedirect(route('walikelas.index', ['tahun_id' => $old->tahun_id]));

    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => true, 'keterangan' => 'Kembali bertugas']);
    $this->assertDatabaseHas('wali_kelas', ['id' => $current->id, 'is_active' => false]);
    $this->assertDatabaseCount('wali_kelas', 2);
});

it('deactivates an assignment without deleting it', function () {
    $assignment = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->put(route('walikelas.update', $assignment), [
        'is_active' => 0, 'keterangan' => 'Selesai',
    ])->assertSessionHasNoErrors();

    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'is_active' => false, 'keterangan' => 'Selesai']);
});

it('edits the employee class and period through the same form as creation', function () {
    $assignment = WaliKelas::factory()->create();
    $target = WaliKelas::factory()->create();
    $pegawai = Pegawai::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->put(route('walikelas.update', $assignment), [
            'is_active' => 1, 'tahun_id' => $target->tahun_id, 'kelas_id' => $target->kelas_id,
            'pegawai_id' => $pegawai->id, 'keterangan' => 'Data dikoreksi',
        ])->assertSessionHasNoErrors()->assertRedirect(route('walikelas.index', ['tahun_id' => $target->tahun_id]));

    $this->assertDatabaseHas('wali_kelas', [
        'id' => $assignment->id, 'tahun_id' => $target->tahun_id, 'kelas_id' => $target->kelas_id,
        'pegawai_id' => $pegawai->id, 'is_active' => true, 'keterangan' => 'Data dikoreksi',
    ]);
    $this->assertDatabaseHas('wali_kelas', ['id' => $target->id, 'is_active' => false]);
    $this->assertDatabaseCount('wali_kelas', 2);
});

it('deletes only the selected assignment', function () {
    $assignment = WaliKelas::factory()->create();
    $other = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->delete(route('walikelas.destroy', $assignment))
        ->assertRedirect(route('walikelas.index', ['tahun_id' => $assignment->tahun_id]));

    $this->assertModelMissing($assignment);
    $this->assertModelExists($other);
});

it('redirects guests and denies non-management writes', function () {
    $this->get(route('walikelas.index'))->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => 1]))->post(route('walikelas.store'), [])
        ->assertForbidden();

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('does not allow editing a record from another selected period', function () {
    $assignment = WaliKelas::factory()->create();
    $period = Tahun::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->get(route('walikelas.index', [
        'tahun_id' => $period->id, 'edit' => $assignment->id,
    ]))->assertNotFound();
});

it('renders the edit panel for the selected assignment', function () {
    $assignment = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->get(route('walikelas.index', [
        'tahun_id' => $assignment->tahun_id, 'edit' => $assignment->id,
    ]))->assertSee('Edit Penugasan')->assertSee($assignment->pegawai->name)
        ->assertSee('name="pegawai_id"', false)->assertSee('name="kelas_id"', false)
        ->assertSee('name="tahun_id"', false)->assertSee('Update Data')
        ->assertDontSee('Untuk mengganti pegawai, tambahkan penugasan baru');
});

it('enforces one active wali per class and period at the database boundary', function () {
    $assignment = WaliKelas::factory()->create();
    $replacement = WaliKelas::factory()->make(['tahun_id' => $assignment->tahun_id, 'kelas_id' => $assignment->kelas_id]);

    expect(fn () => $replacement->save())->toThrow(QueryException::class);
    $this->assertDatabaseCount('wali_kelas', 1);
});

it('keeps relations scoped to each assignment period', function () {
    $assignment = WaliKelas::factory()->create();
    $other = WaliKelas::factory()->create(['pegawai_id' => $assignment->pegawai_id, 'kelas_id' => $assignment->kelas_id]);

    expect($assignment->pegawai->kelasWali()->wherePivot('tahun_id', $assignment->tahun_id)->first()->pivot->id)->toBe($assignment->id);
    expect($assignment->kelas->pegawaiWali()->wherePivot('tahun_id', $other->tahun_id)->first()->pivot->id)->toBe($other->id);
    expect($assignment->tahun->waliKelas->modelKeys())->toBe([$assignment->id]);
});

it('keeps the selected period in pagination links', function () {
    $period = Tahun::factory()->create(['isActive' => true]);
    WaliKelas::factory()->count(11)->create(['tahun_id' => $period->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.index', ['per_page' => 10]))
        ->assertViewHas('penugasans', fn ($rows) => $rows->count() === 10
            && $rows->total() === 11
            && str_contains($rows->nextPageUrl(), 'tahun_id='.$period->id));
});

it('rejects missing input without creating assignments', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.store'), [])
        ->assertSessionHasErrors([
            'tahun_id' => 'Pilih periode terlebih dahulu.',
            'penugasans' => 'Tambahkan minimal satu penugasan.',
        ]);

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('renders the form again after a malformed batch is rejected', function () {
    $period = Tahun::factory()->create(['isActive' => true]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->from(route('walikelas.index'))->post(route('walikelas.store'), [
            'tahun_id' => $period->id, 'penugasans' => 'invalid',
        ])->assertSessionHasErrors('penugasans');

    $this->get(route('walikelas.index'))->assertSee('Data belum disimpan:');
    $this->assertDatabaseCount('wali_kelas', 0);
});

it('updates status from the table without clearing existing notes', function () {
    $assignment = WaliKelas::factory()->create(['keterangan' => 'Catatan tetap tersimpan']);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->putJson(route('walikelas.update', $assignment), ['is_active' => 0])
        ->assertJsonPath('data.is_active', false)
        ->assertJsonPath('statuses.0.id', $assignment->id)
        ->assertJsonPath('statuses.0.is_active', false);

    $this->assertDatabaseHas('wali_kelas', [
        'id' => $assignment->id, 'is_active' => false, 'keterangan' => 'Catatan tetap tersimpan',
    ]);
});

it('restores the edit panel and entered notes after validation fails', function () {
    $assignment = WaliKelas::factory()->create();
    $url = route('walikelas.index', ['tahun_id' => $assignment->tahun_id, 'edit' => $assignment->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->from($url)->put(route('walikelas.update', $assignment), [
            'is_active' => 'invalid', 'keterangan' => 'Catatan belum disimpan',
        ])->assertSessionHasErrors('is_active')->assertRedirect($url);

    $this->get($url)->assertSee('Edit Penugasan')->assertSee('Catatan belum disimpan');
    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'is_active' => true]);
});

it('rejects duplicate identities during edit without deactivating any assignment', function () {
    $assignment = WaliKelas::factory()->create();
    $target = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->put(route('walikelas.update', $assignment), [
            'is_active' => 1, 'tahun_id' => $target->tahun_id, 'kelas_id' => $target->kelas_id,
            'pegawai_id' => $target->pegawai_id,
        ])->assertSessionHasErrors(['pegawai_id' => 'Penugasan pegawai pada kelas dan periode ini sudah ada.']);

    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'tahun_id' => $assignment->tahun_id, 'is_active' => true]);
    $this->assertDatabaseHas('wali_kelas', ['id' => $target->id, 'is_active' => true]);
});

it('rejects invalid references on edit', function () {
    $assignment = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->putJson(route('walikelas.update', $assignment), [
            'is_active' => 0, 'tahun_id' => 999, 'kelas_id' => 999, 'pegawai_id' => 999,
        ])->assertInvalid(['tahun_id', 'kelas_id', 'pegawai_id']);

    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'is_active' => true]);
});

it('deletes only checked assignments in the selected period', function () {
    $first = WaliKelas::factory()->create();
    $second = WaliKelas::factory()->create(['tahun_id' => $first->tahun_id]);
    $untouched = WaliKelas::factory()->create(['tahun_id' => $first->tahun_id]);

    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => 1]))
        ->delete(route('walikelas.bulkDelete'), ['tahun_id' => $first->tahun_id, 'id' => [$first->id, $second->id]])
        ->assertSessionHasNoErrors()->assertRedirect(route('walikelas.index', ['tahun_id' => $first->tahun_id]));

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    $this->assertModelExists($untouched);
});

it('rejects bulk deletion across periods without deleting any selected rows', function () {
    $first = WaliKelas::factory()->create();
    $other = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->delete(route('walikelas.bulkDelete'), ['tahun_id' => $first->tahun_id, 'id' => [$first->id, $other->id]])
        ->assertSessionHasErrors('id');

    $this->assertModelExists($first);
    $this->assertModelExists($other);
});

it('rejects an empty bulk selection', function () {
    $assignment = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->delete(route('walikelas.bulkDelete'), ['tahun_id' => $assignment->tahun_id])
        ->assertSessionHasErrors('id');

    $this->assertModelExists($assignment);
});

it('denies non-management edits and bulk deletes', function () {
    $assignment = WaliKelas::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => 1]));

    $this->putJson(route('walikelas.update', $assignment), ['is_active' => 0])->assertForbidden();
    $this->delete(route('walikelas.bulkDelete'), ['tahun_id' => $assignment->tahun_id, 'id' => [$assignment->id]])->assertForbidden();

    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'is_active' => true]);
});

it('returns the affected class statuses after inline reactivation', function () {
    $old = WaliKelas::factory()->create(['is_active' => false]);
    $current = WaliKelas::factory()->create(['tahun_id' => $old->tahun_id, 'kelas_id' => $old->kelas_id]);
    WaliKelas::factory()->create(['tahun_id' => $old->tahun_id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->putJson(route('walikelas.update', $old), ['is_active' => 1])
        ->assertJsonPath('data.is_active', true)->assertJsonCount(2, 'statuses')
        ->assertJsonFragment(['id' => $old->id, 'is_active' => true])
        ->assertJsonFragment(['id' => $current->id, 'is_active' => false]);

    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => true]);
    $this->assertDatabaseHas('wali_kelas', ['id' => $current->id, 'is_active' => false]);
});

it('sorts by employee name in both directions and preserves sorting in pagination', function (string $direction, string $firstName) {
    $period = Tahun::factory()->create(['isActive' => true]);
    $anna = Pegawai::factory()->create(['name' => 'Anna']);
    $zara = Pegawai::factory()->create(['name' => 'Zara']);
    WaliKelas::factory()->count(10)->create(['tahun_id' => $period->id, 'pegawai_id' => $anna->id]);
    WaliKelas::factory()->create(['tahun_id' => $period->id, 'pegawai_id' => $zara->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.index', ['sort' => 'nama', 'direction' => $direction]))
        ->assertViewHas('penugasans', fn ($rows) => $rows->first()->pegawai->name === $firstName
            && str_contains($rows->nextPageUrl(), 'sort=nama') && str_contains($rows->nextPageUrl(), 'direction='.$direction));
})->with([['asc', 'Anna'], ['desc', 'Zara']]);

it('rejects unsupported sorting parameters', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->getJson(route('walikelas.index', ['sort' => 'password', 'direction' => 'unsafe']))
        ->assertInvalid(['sort', 'direction']);
});

it('searches employee identifiers and assignment notes', function (string $search) {
    $period = Tahun::factory()->create(['isActive' => true]);
    $employee = Pegawai::factory()->create(['nuptk' => '0012345678901234']);
    $assignment = WaliKelas::factory()->create(['tahun_id' => $period->id, 'pegawai_id' => $employee->id, 'keterangan' => 'Pendamping khusus']);
    WaliKelas::factory()->create(['tahun_id' => $period->id]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.index', ['search' => $search]))
        ->assertViewHas('penugasans', fn ($rows) => $rows->modelKeys() === [$assignment->id]);
})->with(['0012345678901234', 'Pendamping khusus']);

it('exports selected assignments as text-safe Excel and imports them back', function () {
    $assignment = WaliKelas::factory()->create(['keterangan' => '=1+1']);
    $assignment->pegawai->update(['nuptk' => '0012345678901234']);
    WaliKelas::factory()->create(['tahun_id' => $assignment->tahun_id]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]));

    $response = $this->get(route('walikelas.export', ['tahun_id' => $assignment->tahun_id, 'ids' => [$assignment->id]]));
    $response->assertDownload('WaliKelas.xlsx');
    $file = $response->baseResponse->getFile()->getPathname();
    $sheet = IOFactory::load($file)->getActiveSheet();
    expect($sheet->getHighestRow())->toBe(2)
        ->and($sheet->getCell('E2')->getValue())->toBe('0012345678901234')
        ->and($sheet->getCell('H2')->getDataType())->toBe('s');
    $assignment->update(['is_active' => false, 'keterangan' => 'Diubah']);

    $this->post(route('walikelas.import'), [
        'tahun_id' => $assignment->tahun_id,
        'file' => UploadedFile::fake()->createWithContent('WaliKelas.xlsx', file_get_contents($file)),
    ])->assertSessionHasNoErrors()->assertRedirect(route('walikelas.index', ['tahun_id' => $assignment->tahun_id]));

    $this->assertDatabaseCount('wali_kelas', 2);
    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'is_active' => true, 'keterangan' => '=1+1']);
});

it('exports all matching assignments rather than only the current page', function () {
    $period = Tahun::factory()->create();
    WaliKelas::factory()->count(11)->create(['tahun_id' => $period->id, 'is_active' => false]);
    WaliKelas::factory()->create(['tahun_id' => $period->id]);
    WaliKelas::factory()->create(['is_active' => false]);

    $response = $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.export', ['tahun_id' => $period->id, 'status' => 'nonaktif']));

    $response->assertDownload('WaliKelas.xlsx');
    expect(IOFactory::load($response->baseResponse->getFile()->getPathname())->getActiveSheet()->getHighestRow())->toBe(12);
});

it('rejects exporting selected records from another period', function () {
    $assignment = WaliKelas::factory()->create();
    $period = Tahun::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.export', ['tahun_id' => $period->id, 'ids' => [$assignment->id]]))
        ->assertSessionHasErrors('ids');
});

it('imports CSV by NUPTK and replaces the active teacher', function () {
    $old = WaliKelas::factory()->create();
    $employee = Pegawai::factory()->create(['nuptk' => '0098765432101234']);
    $csv = "tahun,semester,kelas,nuptk,status,keterangan\n{$old->tahun->tahun},{$old->tahun->semester},{$old->kelas->kelas},{$employee->nuptk},aktif,Baru\n";

    $this->actingAs(User::factory()->create(['role' => 'operator', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $old->tahun_id, 'file' => UploadedFile::fake()->createWithContent('wali.csv', $csv)])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => false]);
    $this->assertDatabaseHas('wali_kelas', ['tahun_id' => $old->tahun_id, 'kelas_id' => $old->kelas_id, 'pegawai_id' => $employee->id, 'is_active' => true]);
});

it('rejects an invalid import row without saving earlier valid rows', function (string $invalidColumn, string $invalidValue) {
    $old = WaliKelas::factory()->create();
    $employee = Pegawai::factory()->create();
    $header = ['tahun', 'semester', 'kelas', 'pegawai_id', 'status'];
    $valid = [$old->tahun->tahun, $old->tahun->semester, $old->kelas->kelas, $employee->id, 'aktif'];
    $invalid = array_combine($header, $valid);
    $invalid[$invalidColumn] = $invalidValue;
    $csv = implode(',', $header)."\n".implode(',', $valid)."\n".implode(',', $invalid)."\n";

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $old->tahun_id, 'file' => UploadedFile::fake()->createWithContent('wali.csv', $csv)])
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('wali_kelas', 1);
    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => true]);
})->with([['kelas', 'Tidak Ada'], ['pegawai_id', '999999'], ['status', 'unknown'], ['tahun', '1900/1901'], ['semester', 'Tidak Ada']]);

it('rejects repeated active classes within an import', function () {
    $old = WaliKelas::factory()->create();
    $employee = Pegawai::factory()->create();
    $csv = "tahun,semester,kelas,pegawai_id,status\n{$old->tahun->tahun},{$old->tahun->semester},{$old->kelas->kelas},{$old->pegawai_id},aktif\n{$old->tahun->tahun},{$old->tahun->semester},{$old->kelas->kelas},{$employee->id},aktif\n";

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $old->tahun_id, 'file' => UploadedFile::fake()->createWithContent('wali.csv', $csv)])
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('wali_kelas', 1);
});

it('rejects empty import files and unsupported uploads', function (string $name, string $content) {
    $period = Tahun::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $period->id, 'file' => UploadedFile::fake()->createWithContent($name, $content)])
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('wali_kelas', 0);
})->with([['wali.csv', "tahun,semester,kelas,pegawai_id,status\n"], ['wali.php', '<?php echo 1;']]);

it('denies non-management import and export requests', function () {
    $period = Tahun::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => 1]));

    $this->post(route('walikelas.import'), ['tahun_id' => $period->id])->assertForbidden();
    $this->get(route('walikelas.export', ['tahun_id' => $period->id]))->assertForbidden();

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('exports an empty period with headings usable as an import format', function () {
    $period = Tahun::factory()->create();

    $response = $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->get(route('walikelas.export', ['tahun_id' => $period->id]));

    $response->assertDownload('WaliKelas.xlsx');
    $sheet = IOFactory::load($response->baseResponse->getFile()->getPathname())->getActiveSheet();
    expect($sheet->getHighestRow())->toBe(1)
        ->and($sheet->rangeToArray('A1:H1')[0])->toBe(['tahun', 'semester', 'kelas', 'pegawai_id', 'nuptk', 'nama', 'status', 'keterangan']);
});

it('reports an unreadable spreadsheet as a validation error', function () {
    $period = Tahun::factory()->create();
    $file = UploadedFile::fake()->create('broken.xlsx', 1, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $period->id, 'file' => $file])
        ->assertSessionHasErrors('file');

    $this->assertDatabaseCount('wali_kelas', 0);
});

it('imports historical and active assignments for the same class without losing either', function () {
    $old = WaliKelas::factory()->create(['is_active' => false]);
    $current = WaliKelas::factory()->create(['tahun_id' => $old->tahun_id, 'kelas_id' => $old->kelas_id]);
    $csv = "tahun,semester,kelas,pegawai_id,status\n{$old->tahun->tahun},{$old->tahun->semester},{$old->kelas->kelas},{$current->pegawai_id},aktif\n{$old->tahun->tahun},{$old->tahun->semester},{$old->kelas->kelas},{$old->pegawai_id},nonaktif\n";

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('walikelas.import'), ['tahun_id' => $old->tahun_id, 'file' => UploadedFile::fake()->createWithContent('wali.csv', $csv)])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('wali_kelas', 2);
    $this->assertDatabaseHas('wali_kelas', ['id' => $old->id, 'is_active' => false]);
    $this->assertDatabaseHas('wali_kelas', ['id' => $current->id, 'is_active' => true]);
});
