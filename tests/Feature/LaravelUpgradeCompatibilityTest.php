<?php

use App\Models\Sekolah;
use Illuminate\Cache\Repository;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

function upgradeCompatibilityCache(string $driver): Repository
{
    if ($driver === 'database') {
        test()->artisan('migrate', [
            '--path' => 'database/migrations/0001_01_01_000001_create_cache_table.php',
            '--no-interaction' => true,
        ])->assertExitCode(0);

        config(['cache.stores.upgrade_compatibility' => [
            'driver' => 'database',
            'table' => 'cache',
        ]]);
    } else {
        Storage::fake('upgrade-cache');
        config(['cache.stores.upgrade_compatibility' => [
            'driver' => 'file',
            'path' => Storage::disk('upgrade-cache')->path(''),
        ]]);
    }

    return Cache::store('upgrade_compatibility');
}

test('rejects cross site login submissions without a CSRF token with 419', function () {
    $this->app->instance('env', 'production');

    $this->withHeaders(['Sec-Fetch-Site' => 'cross-site'])
        ->post('/login', [])
        ->assertStatus(419);
});

test('allows same origin login submissions to reach input validation', function () {
    $this->app->instance('env', 'production');

    $this->withHeaders(['Sec-Fetch-Site' => 'same-origin'])
        ->from('/login')->post('/login', [])
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['email', 'password']);
});

test('accepts a valid CSRF token when browser origin metadata is absent', function () {
    $this->app->instance('env', 'production');
    $token = Str::random(40);

    $this->withSession(['_token' => $token])
        ->from('/login')->post('/login', ['_token' => $token])
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['email', 'password']);
});

test('retains existing PHP session payloads including two factor and face enrollment state', function () {
    $handler = new ArraySessionHandler(120);
    $sessionId = Str::random(40);
    $handler->write($sessionId, serialize([
        '_token' => Str::random(40),
        '2fa_passed' => true,
        'face_enrollment_review' => [12 => ['token' => 'pending-confirmation']],
    ]));
    $session = new Store(config('session.cookie'), $handler, $sessionId, config('session.serialization'));

    $session->start();
    $session->put('selected_date', '2026-11-22');
    $session->save();

    expect($session->get('2fa_passed'))->toBeTrue();
    expect($session->get('face_enrollment_review.12.token'))->toBe('pending-confirmation');
    expect(unserialize($handler->read($sessionId), ['allowed_classes' => false]))
        ->toMatchArray(['2fa_passed' => true, 'selected_date' => '2026-11-22']);
});

test('restores the cached school profile with its date attributes', function (string $driver) {
    $cache = upgradeCompatibilityCache($driver);
    $school = new Sekolah(['nama_sekolah' => 'Sekolah Pengujian', 'tgl_sk_pendirian' => '2020-01-01']);

    $cache->put('school-profile', $school, 60);
    $restored = $cache->get('school-profile');

    expect($restored)->toBeInstanceOf(Sekolah::class);
    expect($restored->nama_sekolah)->toBe('Sekolah Pengujian');
    expect($restored->tgl_sk_pendirian->format('Y-m-d'))->toBe('2020-01-01');
})->with(['file', 'database']);

test('does not restore cache objects outside the allowed school profile class', function (string $driver) {
    $cache = upgradeCompatibilityCache($driver);

    $cache->put('unexpected-object', (object) ['value' => 'untrusted'], 60);

    expect($cache->get('unexpected-object'))->toBeInstanceOf(__PHP_Incomplete_Class::class);
})->with(['file', 'database']);
