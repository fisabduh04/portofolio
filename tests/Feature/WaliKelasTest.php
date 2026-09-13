<?php

use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\Tahun;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
        'database/migrations/2026_09_12_113236_create_wali_kelas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
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

it('rejects duplicate assignments without partially saving the batch', function () {
    $old = WaliKelas::factory()->create();
    $otherClass = Kelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->post(route('walikelas.store'), [
        'tahun_id' => $old->tahun_id,
        'penugasans' => [
            ['kelas_id' => $otherClass->id, 'pegawai_id' => $old->pegawai_id, 'is_active' => 1],
            ['kelas_id' => $old->kelas_id, 'pegawai_id' => $old->pegawai_id, 'is_active' => 1],
        ],
    ])->assertSessionHasErrors(['penugasans.1.pegawai_id' => 'Penugasan ini sudah ada pada periode terpilih. Gunakan Edit untuk mengubah status atau keterangan.']);

    $this->assertDatabaseCount('wali_kelas', 1);
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

it('rejects changes to the assignment identity', function () {
    $assignment = WaliKelas::factory()->create();

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))->put(route('walikelas.update', $assignment), [
        'is_active' => 0, 'tahun_id' => Tahun::factory()->create()->id,
    ])->assertSessionHasErrors('tahun_id');

    $this->assertDatabaseHas('wali_kelas', ['id' => $assignment->id, 'tahun_id' => $assignment->tahun_id, 'is_active' => true]);
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
    ]))->assertSee('Edit Penugasan')->assertSee($assignment->pegawai->name);
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
        ->assertRedirect(route('walikelas.index', ['tahun_id' => $assignment->tahun_id]));

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
