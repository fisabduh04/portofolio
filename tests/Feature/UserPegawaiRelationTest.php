<?php

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->artisan('migrate', ['--database' => config('database.default'), '--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
    $this->relationMigration = require database_path('migrations/2026_09_18_032252_add_pegawai_foreign_key_to_users_table.php');
});

it('repairs only orphan links while preserving account credentials and valid links', function () {
    $orphan = User::factory()->create(['pegawai_id' => 999999, 'role' => 'admin', 'is_active' => true]);
    $valid = User::factory()->create(['pegawai_id' => Pegawai::factory()->create()->id]);
    $unlinked = User::factory()->create(['pegawai_id' => null]);
    $attributes = $orphan->fresh()->getAttributes();
    Log::spy();

    $this->relationMigration->up();

    expect($orphan->fresh()->getAttributes())->toBe(array_replace($attributes, ['pegawai_id' => null]));
    expect($valid->fresh()->pegawai_id)->toBe($valid->pegawai_id);
    expect($unlinked->fresh()->pegawai_id)->toBeNull();
    Log::shouldHaveReceived('warning')->once()->with('Referensi pegawai akun tidak valid dikosongkan', [
        'user_id' => $orphan->id, 'previous_pegawai_id' => 999999,
    ]);
});

it('rejects a new account pointing to a nonexistent employee', function () {
    $this->relationMigration->up();

    expect(fn () => User::factory()->create(['pegawai_id' => 999999]))->toThrow(QueryException::class);

    $this->assertDatabaseCount('users', 0);
});

it('rejects an invalid employee reassignment without changing the existing link', function () {
    $this->relationMigration->up();
    $employee = Pegawai::factory()->create();
    $user = User::factory()->create(['pegawai_id' => $employee->id]);

    expect(fn () => $user->update(['pegawai_id' => 999999]))->toThrow(QueryException::class);

    expect($user->fresh()->pegawai_id)->toBe($employee->id);
});

it('warns when deleting an employee linked to an account and preserves both records', function () {
    $this->relationMigration->up();
    $employee = Pegawai::factory()->create();
    $user = User::factory()->create(['pegawai_id' => $employee->id]);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)->from(route('pegawai.index'))
        ->delete(route('pegawai.destroy', $employee))
        ->assertRedirect(route('pegawai.index'))->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', 'Pegawai tidak dapat dihapus karena masih memiliki data terkait. Gunakan status nonaktif untuk mempertahankan riwayat.');

    $this->assertModelExists($employee);
    $this->assertModelExists($user);
    expect($user->fresh()->pegawai_id)->toBe($employee->id);
});

it('allows an unlinked account and deletion of an unused employee', function () {
    $this->relationMigration->up();
    $user = User::factory()->create(['pegawai_id' => null]);
    $employee = Pegawai::factory()->create();

    $employee->delete();

    $this->assertModelMissing($employee);
    $this->assertModelExists($user);
});

it('removes only the constraint on rollback and can apply it again', function () {
    $this->relationMigration->up();
    $employee = Pegawai::factory()->create();
    $user = User::factory()->create(['pegawai_id' => $employee->id]);

    $this->relationMigration->down();
    $employee->delete();

    expect($user->fresh()->pegawai_id)->toBe($employee->id);
    $this->relationMigration->up();
    expect($user->fresh()->pegawai_id)->toBeNull();
});
