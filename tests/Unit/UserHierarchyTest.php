<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;

it('allows account management only within the approved management hierarchy', function (UserRole $role) {
    $actor = new User(['role' => $role, 'is_active' => 1]);
    $actor->id = 1;
    $allowed = match ($role) {
        UserRole::Kepala => ['kepala', 'admin', 'operator', 'guru', 'staff', 'bendahara', 'siswa'],
        UserRole::Admin => ['admin', 'operator', 'guru', 'staff', 'bendahara', 'siswa'],
        UserRole::Operator => ['operator', 'guru', 'staff', 'bendahara', 'siswa'],
        default => [],
    };
    $policy = new UserPolicy;
    foreach (UserRole::cases() as $targetRole) {
        $target = new User(['role' => $targetRole, 'is_active' => 1]);
        $target->id = 2;
        expect($policy->manage($actor, $target))->toBe(in_array($targetRole->value, $allowed, true));
    }
    expect($policy->manage($actor, $actor))->toBeFalse();
    $actor->is_active = 0;
    expect($policy->viewAny($actor))->toBeFalse();
})->with(UserRole::cases());

it('separates finance access from school management and places students below staff', function () {
    expect(UserRole::Bendahara->isManagement())->toBeFalse();
    expect(UserRole::Bendahara->canManagePayroll())->toBeTrue();
    expect(UserRole::Kepala->canManagePayroll())->toBeTrue();
    expect(UserRole::Operator->rank())->toBeLessThan(UserRole::Admin->rank());
    expect(UserRole::Guru->rank())->toBe(UserRole::Staff->rank())->toBe(UserRole::Bendahara->rank());
    expect(UserRole::Siswa->rank())->toBeLessThan(UserRole::Guru->rank());
});
