<?php

use App\Models\Pegawai;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;
use Laravel\Fortify\Fortify;

uses(Tests\TestCase::class);

beforeEach(function () {
    $this->withoutVite();
    View::share('sekolah', new Sekolah);
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
        'database/migrations/2026_10_27_111349_make_users_email_nullable_for_pegawai_accounts.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

it('uses the employee relation for login and reset delivery without copying its email', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com', 'nuptk' => '12345']);
    $user = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'old@example.com', 'password' => 'password']);
    $password = $user->password;
    Password::createToken($user);

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => ' New@Example.com ',
    ])->assertRedirect(route('pegawai.index'))->assertSessionHasNoErrors();

    expect($pegawai->fresh()->email)->toBe('new@example.com');
    expect($user->fresh()->email)->toBe('new@example.com');
    expect($user->fresh()->getRawOriginal('email'))->toBe('old@example.com');
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

it('creates an account from its employee without accepting a second email', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'employee@example.com']);

    $this->actingAs($admin)->post(route('operator.users.store'), [
        'pegawai_id' => $pegawai->id, 'role' => 'guru', 'email' => 'forged@example.com',
    ])->assertSessionHasNoErrors()->assertSessionHas('success');

    $account = $pegawai->fresh()->user;
    expect($account->email)->toBe('employee@example.com');
    expect($account->getRawOriginal('email'))->toBeNull();
    expect((bool) $account->is_active)->toBeFalse();
    $this->artisan('migrate', ['--path' => [
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2026_01_28_031755_create_jadwal_pikets_table.php',
        'database/migrations/2026_09_12_113236_create_wali_kelas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
    Pegawai::factory()->create(['email' => 'another@example.com']);
    $this->get(route('operator.users.index'))->assertOk()->assertSee('employee@example.com')
        ->assertSee('Email mengikuti data pegawai.')->assertDontSee('forged@example.com');
});

it('creates an account without submitting an email field', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'employee@example.com']);

    $this->actingAs($admin)->post(route('operator.users.store'), [
        'pegawai_id' => $pegawai->id, 'role' => 'guru',
    ])->assertSessionHasNoErrors()->assertSessionHas('success');

    expect($pegawai->fresh()->user->email)->toBe('employee@example.com');
    $this->assertDatabaseHas('users', ['pegawai_id' => $pegawai->id, 'email' => null]);
});

it('refuses to create an account with a missing invalid or occupied employee email', function (?string $email) {
    $admin = User::factory()->create(['role' => 'admin', 'email' => 'taken@example.com', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => $email]);

    $this->actingAs($admin)->post(route('operator.users.store'), [
        'pegawai_id' => $pegawai->id, 'role' => 'guru', 'email' => 'ignored@example.com',
    ])->assertSessionHasErrors('email');

    expect($pegawai->fresh()->user)->toBeNull();
})->with([null, 'invalid-email', 'taken@example.com']);

it('logs in and resets a password using only the related employee email', function () {
    $pegawai = Pegawai::factory()->create(['email' => 'employee@example.com']);
    $account = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => null, 'is_active' => true]);
    $token = Password::createToken($account);

    $this->post(route('password.update'), [
        'email' => 'employee@example.com', 'token' => $token,
        'password' => 'Changed-password-123!', 'password_confirmation' => 'Changed-password-123!',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('Changed-password-123!', $account->fresh()->password))->toBeTrue();
    $this->post('/login', ['email' => 'employee@example.com', 'password' => 'Changed-password-123!'])
        ->assertSessionHasNoErrors();
    $this->assertAuthenticatedAs($account);
});

it('follows employee email changes made by import and invalidates old reset tokens', function () {
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com', 'nuptk' => '12345']);
    $account = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'legacy@example.com']);
    $password = $account->password;
    Password::createToken($account);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)->post(route('importpegawai'), [
        'file' => UploadedFile::fake()->createWithContent('pegawai.csv', "nuptk,name,email\n12345,Nama Pegawai,new@example.com\n"),
    ])->assertSessionHasNoErrors()->assertRedirect(route('pegawai.index'));

    expect($account->fresh()->email)->toBe('new@example.com');
    expect($account->fresh()->getRawOriginal('email'))->toBe('legacy@example.com');
    expect($account->fresh()->password)->toBe($password);
    expect($account->fresh()->email_verified_at)->toBeNull();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'old@example.com']);
    $provider = Auth::createUserProvider('users');
    expect($provider->retrieveByCredentials(['email' => 'new@example.com'])?->id)->toBe($account->id);
    expect($provider->retrieveByCredentials(['email' => 'old@example.com']))->toBeNull();
    expect($provider->retrieveByCredentials(['email' => 'legacy@example.com']))->toBeNull();
});

it('refuses ambiguous employee login emails and retains unlinked account login', function () {
    foreach (['first@example.com', 'second@example.com'] as $legacyEmail) {
        $pegawai = Pegawai::factory()->create(['email' => 'duplicate@example.com']);
        User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => $legacyEmail]);
    }
    $unlinked = User::factory()->create(['pegawai_id' => null, 'email' => 'admin@example.com']);
    $provider = Auth::createUserProvider('users');

    expect($provider->retrieveByCredentials(['email' => 'duplicate@example.com']))->toBeNull();
    expect($provider->retrieveByCredentials(['email' => 'admin@example.com'])?->id)->toBe($unlinked->id);
});

it('sends password recovery to the employee email instead of the legacy account email', function () {
    Notification::fake();
    $pegawai = Pegawai::factory()->create(['email' => 'employee@example.com']);
    $account = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'legacy@example.com', 'is_active' => true]);

    $this->post(route('password.email'), ['email' => 'employee@example.com'])
        ->assertSessionHasNoErrors()->assertSessionHas('status');

    Notification::assertSentTo($account, ResetPassword::class, fn ($notification, $channels, $recipient) => $recipient->email === 'employee@example.com');
    $this->assertDatabaseHas('password_reset_tokens', ['email' => 'employee@example.com']);
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'legacy@example.com']);
});

it('refuses password recovery for an inactive linked account', function () {
    Notification::fake();
    $pegawai = Pegawai::factory()->create(['email' => 'employee@example.com']);
    User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => null, 'is_active' => false]);

    $this->post(route('password.email'), ['email' => 'employee@example.com'])->assertSessionHasErrors('email');

    Notification::assertNothingSent();
    $this->assertDatabaseCount('password_reset_tokens', 0);
});

it('prevents importing a login email change for a higher ranked account', function () {
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com', 'nuptk' => '12345']);
    $account = User::factory()->create(['pegawai_id' => $pegawai->id, 'role' => 'admin', 'email' => null]);
    $operator = User::factory()->create(['role' => 'operator', 'is_active' => true]);

    $this->actingAs($operator)->post(route('importpegawai'), [
        'file' => UploadedFile::fake()->createWithContent('pegawai.csv', "nuptk,name,email\n12345,Nama Diubah,new@example.com\n"),
    ])->assertForbidden();

    expect($pegawai->fresh()->email)->toBe('old@example.com');
    expect($pegawai->fresh()->name)->toBe($pegawai->name);
    expect($account->fresh()->email)->toBe('old@example.com');
});

it('rejects invalid or occupied login email without changing either record', function ($email) {
    $admin = User::factory()->create(['role' => 'admin', 'email' => 'taken@example.com', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com', 'nuptk' => '12345']);
    $user = User::factory()->create(['pegawai_id' => $pegawai->id, 'email' => 'old@example.com']);

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => 'Changed', 'nuptk' => $pegawai->nuptk, 'email' => $email,
    ])->assertSessionHasErrors('email');

    expect($pegawai->fresh()->email)->toBe('old@example.com');
    expect($pegawai->fresh()->name)->toBe($pegawai->name);
    expect($user->fresh()->email)->toBe('old@example.com');
})->with(['taken@example.com', '', 'invalid-email', str_repeat('a', 250).'@example.com']);

it('prevents an operator from changing a higher ranked login email', function () {
    $actor = User::factory()->create(['role' => 'operator', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'old@example.com', 'nuptk' => '12345']);
    $user = User::factory()->create(['role' => 'admin', 'pegawai_id' => $pegawai->id, 'email' => 'old@example.com']);

    $this->actingAs($actor)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => 'new@example.com',
    ])->assertForbidden();

    expect($pegawai->fresh()->email)->toBe('old@example.com');
    expect($user->fresh()->email)->toBe('old@example.com');
});

it('preserves verification when the login email is unchanged or omitted', function ($includeEmail) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['email' => 'same@example.com', 'nuptk' => '12345']);
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
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $pegawai = Pegawai::factory()->create(['nuptk' => '12345']);

    $this->actingAs($admin)->put(route('pegawai.update', $pegawai), [
        'name' => $pegawai->name, 'nuptk' => $pegawai->nuptk, 'email' => 'new@example.com',
    ])->assertSessionHasNoErrors();

    expect($pegawai->fresh()->email)->toBe('new@example.com');
    expect($pegawai->fresh()->user)->toBeNull();
});
