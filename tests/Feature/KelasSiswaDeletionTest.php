<?php

use App\Http\Middleware\RestrictTreasurerAccess;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Auth\Middleware\Authorize;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110145_create_siswas_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_12_055121_create_kelas_siswas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

function classAssignmentForDeletion(): KelasSiswa
{
    return KelasSiswa::create([
        'siswa_id' => Siswa::factory()->create()->id,
        'kelas_id' => Kelas::factory()->create()->id,
        'tahun_id' => Tahun::factory()->create()->id,
        'ket' => 'aktif',
    ]);
}

it('refuses unauthorized assignment deletion with 403 and preserves the records', function (string $role, string $endpoint) {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => $role, 'is_active' => true]);
    $url = $endpoint === 'bulk'
        ? route('kelassiswa.bulkDelete')
        : route('kelassiswa.destroy', $assignment->id);

    $this->actingAs($actor)->deleteJson($url, ['id' => [$assignment->id]])->assertForbidden();

    $this->assertModelExists($assignment);
})->with(['guru', 'siswa', 'staff', 'bendahara'])->with(['bulk', 'single']);

it('allows active management to delete only the selected assignments', function (string $role, string $endpoint) {
    $assignment = classAssignmentForDeletion();
    $untouched = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => $role, 'is_active' => true]);
    $url = $endpoint === 'bulk'
        ? route('kelassiswa.bulkDelete')
        : route('kelassiswa.destroy', $assignment->id);

    $this->actingAs($actor)->from(route('kelassiswa.index'))
        ->delete($url, ['id' => [$assignment->id]])
        ->assertRedirect(route('kelassiswa.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($assignment);
    $this->assertModelExists($untouched);
})->with(['kepala', 'admin', 'operator'])->with(['bulk', 'single']);

it('refuses deletion by inactive management accounts', function (string $role) {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => $role, 'is_active' => false]);

    $this->actingAs($actor)->deleteJson(route('kelassiswa.bulkDelete'), ['id' => [$assignment->id]])->assertForbidden();

    $this->assertModelExists($assignment);
})->with(['kepala', 'admin', 'operator']);

it('requires authentication before deleting assignments', function (string $endpoint) {
    $assignment = classAssignmentForDeletion();
    $url = $endpoint === 'bulk'
        ? route('kelassiswa.bulkDelete')
        : route('kelassiswa.destroy', $assignment->id);

    $this->delete($url, ['id' => [$assignment->id]])->assertRedirect(route('login'));

    $this->assertModelExists($assignment);
})->with(['bulk', 'single']);

it('authorizes the controller action even without role and gate middleware', function () {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $this->withoutMiddleware([RoleMiddleware::class, Authorize::class, RestrictTreasurerAccess::class]);

    $this->actingAs($actor)->deleteJson(route('kelassiswa.bulkDelete'), ['id' => [$assignment->id]])->assertForbidden();

    $this->assertModelExists($assignment);
});

it('rejects missing or malformed bulk selections without deleting records', function (array $payload) {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->deleteJson(route('kelassiswa.bulkDelete'), $payload)
        ->assertUnprocessable()->assertInvalid('id');

    $this->assertModelExists($assignment);
})->with([
    'missing' => [[]],
    'empty' => [['id' => []]],
    'scalar' => [['id' => 1]],
    'associative' => [['id' => ['selected' => 1]]],
]);

it('rejects an invalid id before deleting any valid selection', function (mixed $invalidId) {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->deleteJson(route('kelassiswa.bulkDelete'), ['id' => [$assignment->id, $invalidId]])
        ->assertUnprocessable()->assertInvalid('id.1');

    $this->assertModelExists($assignment);
})->with([
    'missing record' => [999999],
    'text' => ['invalid'],
    'null' => [null],
    'nested array' => [[1]],
]);

it('rejects repeated ids instead of deleting the selection', function () {
    $assignment = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->deleteJson(route('kelassiswa.bulkDelete'), ['id' => [$assignment->id, (string) $assignment->id]])
        ->assertUnprocessable()->assertInvalid('id.1');

    $this->assertModelExists($assignment);
});

it('does not expand single deletion using ids from the request body', function () {
    $assignment = classAssignmentForDeletion();
    $untouched = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->delete(route('kelassiswa.destroy', $assignment->id), ['id' => [$untouched->id]])
        ->assertRedirect(route('kelassiswa.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($assignment);
    $this->assertModelExists($untouched);
});

it('deletes every valid bulk selection while retaining unselected assignments and student records', function () {
    $first = classAssignmentForDeletion();
    $second = classAssignmentForDeletion();
    $untouched = classAssignmentForDeletion();
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->delete(route('kelassiswa.bulkDelete'), ['id' => [$first->id, $second->id]])
        ->assertRedirect(route('kelassiswa.index'))->assertSessionHas('type', 'success');

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    $this->assertModelExists($untouched);
    $this->assertDatabaseHas('siswas', ['id' => $first->siswa_id]);
    $this->assertDatabaseHas('siswas', ['id' => $second->siswa_id]);
});
