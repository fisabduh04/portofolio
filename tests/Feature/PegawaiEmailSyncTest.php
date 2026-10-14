<?php

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Fortify;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->withoutVite();
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

it('synchronizes login and reset delivery when saving an employee email', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = Pegawai::factory()->create(['email' => 'new@example.com']);
    $user = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'old@example.com', 'password' => 'password']);
    $password = $user->password;
    Password::createToken($user);

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => ' New@Example.com ',
    ])->assertRedirect(route('pegawai.index'))->assertSessionHasNoErrors();

    expect($pegawai->fresh()->email)->toBe('new@example.com');
    expect($user->fresh()->email)->toBe('new@example.com');
    expect($user->fresh()->password)->toBe($password);
    expect($user->fresh()->email_verified_at)->toBeNull();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'old@example.com']);
    $authenticate = Fortify::$authenticateUsingCallback;
    expect($authenticate(Illuminate\Http\Request::create('/login', 'POST', ['email' => 'new@example.com', 'password' => 'password']))?->id)->toBe($user->id);
    expect($authenticate(Illuminate\Http\Request::create('/login', 'POST', ['email' => 'old@example.com', 'password' => 'password'])))->toBeNull();

    Notification::fake();
    $this->post(route('operator.users.resend-reset'), ['email' => 'new@example.com'])->assertSessionHas('success');
    Notification::assertSentTo($user, ResetPassword::class, fn ($notification, $channels, $recipient) => $recipient->email === 'new@example.com');
});

it('rejects invalid or occupied login email without changing either record', function ($email) {
    $admin = User::factory()->create(['role' => 'admin', 'email' => 'taken@example.com']);
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com']);
    $user = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'old@example.com']);

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => 'Changed', 'nuptk' => $pegawai->nuptk, 'email' => $email,
    ])->assertSessionHasErrors('email');

    expect($pegawai->fresh()->email)->toBe('old@example.com');
    expect($pegawai->fresh()->name)->toBe($pegawai->name);
    expect($user->fresh()->email)->toBe('old@example.com');
})->with(['taken@example.com', '', 'invalid-email', str_repeat('a', 250).'@example.com']);

it('prevents an operator from changing a higher ranked login email', function () {
    $actor = User::factory()->create(['role' => 'operator']);
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com']);
    $user = User::factory()->create(['role' => 'admin', 'pegawai_id' => $pegawai->id, 'email' => 'old@example.com']);

    $this->actingAs($actor)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => 'new@example.com',
    ])->assertForbidden();

    expect($pegawai->fresh()->email)->toBe('old@example.com');
    expect($user->fresh()->email)->toBe('old@example.com');
});

it('preserves verification when the login email is unchanged or omitted', function ($includeEmail) {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = Pegawai::factory()->create(['email' => 'same@example.com']);
    $user = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'same@example.com']);
    $data = ['name' => 'Updated name', 'nuptk' => $pegawai->nuptk];
    if ($includeEmail) {
        $data['email'] = 'same@example.com';
    }

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), $data)->assertSessionHasNoErrors();

    expect($pegawai->fresh()->name)->toBe('Updated name');
    expect($user->fresh()->email)->toBe('same@example.com');
    expect($user->fresh()->email_verified_at->equalTo($user->email_verified_at))->toBeTrue();
})->with([true, false]);

it('updates an employee without an account without creating an account', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $pegawai = Pegawai::factory()->create();

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => 'new@example.com',
    ])->assertSessionHasNoErrors();

    expect($pegawai->fresh()->email)->toBe('new@example.com');
    expect($pegawai->fresh()->user)->toBeNull();
});
