<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('database refresh stays inside a fresh temporary MySQL database', function (string $email) {
    $database = DB::connection()->getDatabaseName();
    expect($database)->toMatch('/^portofolio_test_[a-f0-9]{16}$/');
    expect(DB::connection()->getDriverName())->toBe('mysql');
    $this->assertDatabaseCount('users', 0);
    User::factory()->create(['email' => $email]);

    $this->assertDatabaseHas('users', ['email' => $email]);
    expect(DB::connection()->selectOne('SELECT DATABASE() AS name')->name)->toBe($database);
})->with(['first test' => 'first@example.com', 'next test' => 'next@example.com']);
