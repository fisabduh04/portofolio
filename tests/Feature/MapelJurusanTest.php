<?php

use App\Imports\ImportMapel;
use App\Livewire\Mapel\Data;
use App\Models\Jurusan;
use App\Models\Mapel;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->artisan('migrate', ['--path' => [
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

it('creates a general subject with no department and displays all departments', function () {
    Livewire::test(Data::class)->call('add')
        ->set('kode.1', 'UMUM')->set('mapel.1', 'Matematika')
        ->set('jurusan.1', '')->call('store')->assertHasNoErrors()
        ->assertSee('Semua Jurusan');

    $this->assertDatabaseHas('mapels', ['kode' => 'UMUM', 'jurusan_id' => null]);
});

it('can change a department subject to general and assign a department again', function () {
    $jurusan = Jurusan::create(['kode' => 'TKJ', 'jurusan' => 'Teknik Komputer']);
    $mapel = Mapel::create(['kode' => 'TKJ01', 'mapel' => 'Komputer', 'jurusan_id' => $jurusan->id]);
    $component = Livewire::test(Data::class)->call('edit', $mapel->id)
        ->set('editjurusan', '')->call('update', $mapel->id)->assertHasNoErrors();

    $this->assertDatabaseHas('mapels', ['id' => $mapel->id, 'jurusan_id' => null]);

    $component->call('edit', $mapel->id)->set('editjurusan', $jurusan->id)
        ->call('update', $mapel->id)->assertHasNoErrors();
    $this->assertDatabaseHas('mapels', ['id' => $mapel->id, 'jurusan_id' => $jurusan->id]);
});

it('rejects nonexistent departments when creating and editing', function () {
    Livewire::test(Data::class)->call('add')->set('kode.1', 'UMUM')
        ->set('mapel.1', 'Matematika')->set('jurusan.1', 999)
        ->call('store')->assertHasErrors(['jurusan.1' => 'exists']);
    $this->assertDatabaseCount('mapels', 0);

    $mapel = Mapel::create(['kode' => 'UMUM', 'mapel' => 'Matematika']);
    Livewire::test(Data::class)->call('edit', $mapel->id)->set('editjurusan', 999)
        ->call('update', $mapel->id)->assertHasErrors(['editjurusan' => 'exists']);
    $this->assertDatabaseHas('mapels', ['id' => $mapel->id, 'jurusan_id' => null]);
});

it('imports empty departments as general but skips unknown department names', function () {
    $import = new ImportMapel;
    $import->model(['kode' => 'UMUM', 'mata_pelajaran' => 'Matematika']);
    $import->model(['kode' => 'SALAH', 'mata_pelajaran' => 'Komputer', 'jurusan' => 'Tidak Ada']);

    $this->assertDatabaseHas('mapels', ['kode' => 'UMUM', 'jurusan_id' => null]);
    $this->assertDatabaseCount('mapels', 1);
    expect($import->skippedRows)->toHaveCount(1);
});
