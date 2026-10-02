<?php

use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Illuminate\Support\Facades\View::share('sekolah', new Sekolah);
});

it('prevents operators from changing administrators or promoting accounts above their rank', function () {
    $actor = User::factory()->create(['role' => 'operator', 'is_active' => true]);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $teacher = User::factory()->create(['role' => 'guru', 'is_active' => true]);
    $this->actingAs($actor)->patch(route('operator.users.update-role', $admin), ['role' => 'guru'])->assertSessionHas('error');
    $this->patch(route('operator.users.toggle-active', $admin))->assertSessionHas('error');
    $this->patch(route('operator.users.update-role', $teacher), ['role' => 'admin'])->assertSessionHas('error');
    $this->patch(route('operator.users.update-role', $actor), ['role' => 'admin'])->assertSessionHas('error');
    $this->post(route('operator.users.store'), ['pegawai_id' => Pegawai::factory()->create()->id, 'email' => 'blocked@example.test', 'role' => 'kepala'])->assertSessionHas('error');
    expect($admin->fresh()->role->value)->toBe('admin');
    expect((bool) $admin->fresh()->is_active)->toBeTrue();
    expect($teacher->fresh()->role->value)->toBe('guru');
    $this->assertDatabaseMissing('users', ['email' => 'blocked@example.test']);
    $this->patch(route('operator.users.update-role', $teacher), ['role' => 'staff'])->assertSessionHas('success');
    expect($teacher->fresh()->role->value)->toBe('staff');
});

it('allows principals to open master and employee management and manage administrator accounts', function () {
    $actor = User::factory()->create(['role' => 'kepala', 'is_active' => true]);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Pegawai::factory()->create();
    $this->actingAs($actor)->get(route('operator.users.index'))->assertOk()->assertSee('Hierarki:');
    $this->get(route('attendance.rules.create'))->assertOk();
    $this->patch(route('operator.users.update-role', $admin), ['role' => 'operator'])->assertSessionHas('success');
    expect($admin->fresh()->role->value)->toBe('operator');
    $this->patch(route('operator.users.toggle-active', $actor))->assertSessionHas('error');
    expect((bool) $actor->fresh()->is_active)->toBeTrue();
});

it('refuses account management by teachers staff treasurers and students', function () {
    $target = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    foreach (['guru', 'staff', 'bendahara', 'siswa'] as $role) {
        $actor = User::factory()->create(['role' => $role, 'is_active' => true]);
        Pegawai::factory()->create();
        $this->actingAs($actor)->get(route('operator.users.index'))->assertForbidden();
        $this->patch(route('operator.users.update-role', $target), ['role' => 'guru'])->assertForbidden();
        $this->patch(route('operator.users.toggle-active', $target))->assertForbidden();
    }
    expect($target->fresh()->role->value)->toBe('siswa');
    expect((bool) $target->fresh()->is_active)->toBeTrue();
});
