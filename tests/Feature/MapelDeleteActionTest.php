<?php

use App\Livewire\Mapel\Data;
use App\Models\Jadwal;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110240_create_mapels_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_04_110343_create_jadwals_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

it('deletes only the clicked subject after a search even when another row is selected', function () {
    $target = Mapel::create(['kode' => 'A', 'mapel' => 'Matematika']);
    $other = Mapel::create(['kode' => 'B', 'mapel' => 'Bahasa']);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->test(Data::class)->set('mapel_selected_id', [$other->id])->set('search', 'Matematika')
        ->assertSee('wire:click="del('.$target->id.')"', false)
        ->call('del', $target->id)->assertDispatched('showToast', type: 'success');

    $this->assertModelMissing($target);
    $this->assertModelExists($other);
});

it('keeps a subject and its schedule when deletion is requested', function () {
    $target = Mapel::create(['kode' => 'A', 'mapel' => 'Matematika']);
    $jadwal = Jadwal::factory()->create(['mapel_id' => $target->id]);

    Livewire::actingAs(User::factory()->create(['role' => 'operator', 'is_active' => 1]))
        ->test(Data::class)->call('del', $target->id)
        ->assertDispatched('showToast', type: 'warning');

    $this->assertModelExists($target);
    $this->assertModelExists($jadwal);
});

it('deletes checked subjects through the bulk action', function () {
    $first = Mapel::create(['kode' => 'A', 'mapel' => 'Matematika']);
    $second = Mapel::create(['kode' => 'B', 'mapel' => 'Bahasa']);

    Livewire::actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->test(Data::class)->set('mapel_selected_id', [$first->id, $second->id])
        ->call('del')->assertDispatched('showToast', type: 'success');

    $this->assertDatabaseCount('mapels', 0);
});

it('denies deletion by a teacher', function () {
    $target = Mapel::create(['kode' => 'A', 'mapel' => 'Matematika']);

    Livewire::actingAs(User::factory()->create(['role' => 'guru', 'is_active' => 1]))
        ->test(Data::class)->call('del', $target->id)->assertForbidden();

    $this->assertModelExists($target);
});
