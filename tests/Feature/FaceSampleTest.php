<?php

use App\Models\FaceSample;
use App\Models\Pegawai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('encrypts stored descriptors and hides them from serialization', function () {
    $creator = User::factory()->create();
    $descriptor = [0.125, -0.25, 0.5];

    $sample = FaceSample::create([
        'model' => 'face-api.js-v1',
        'descriptor' => $descriptor,
        'created_by' => $creator->id,
    ])->fresh();

    $stored = DB::table('face_samples')->where('id', $sample->id)->value('descriptor');
    expect($stored)->not->toBe(json_encode($descriptor));
    expect(json_decode(Crypt::decryptString($stored), true))->toBe($descriptor);
    expect($sample->descriptor)->toBe($descriptor);
    expect($sample->toArray())->not->toHaveKey('descriptor');
    expect($sample->is_active)->toBeTrue();
    expect($sample->label)->toBeNull();
    expect($sample->siswa_id)->toBeNull();
    expect($sample->pegawai_id)->toBeNull();
    expect($sample->creator->is($creator))->toBeTrue();

    $sample->update(['descriptor' => [0.75], 'is_active' => false]);
    expect($sample->fresh()->descriptor)->toBe([0.75]);
    expect($sample->fresh()->is_active)->toBeFalse();
});

it('links samples to people and prevents deletion of their records', function (string $relation, string $personClass) {
    $person = $personClass::factory()->create();
    $creator = User::factory()->create();
    $sample = FaceSample::create([
        $relation.'_id' => $person->id,
        'model' => 'face-api.js-v1',
        'descriptor' => [0.125],
        'created_by' => $creator->id,
    ]);

    expect($sample->{$relation}->is($person))->toBeTrue();
    expect(fn () => $person->delete())->toThrow(QueryException::class);

    $this->assertModelExists($person);
    $this->assertModelExists($sample);
})->with([
    'student' => ['siswa', Siswa::class],
    'employee' => ['pegawai', Pegawai::class],
]);

it('preserves the registering account while it has samples', function () {
    $creator = User::factory()->create();
    $sample = FaceSample::create([
        'model' => 'face-api.js-v1',
        'descriptor' => [0.125],
        'created_by' => $creator->id,
    ]);

    expect(fn () => $creator->delete())->toThrow(QueryException::class);

    $this->assertModelExists($creator);
    $this->assertModelExists($sample);
});

it('rejects references to missing people or registering accounts', function (string $column) {
    $creator = User::factory()->create();
    $attributes = [
        'model' => 'face-api.js-v1',
        'descriptor' => [0.125],
        'created_by' => $creator->id,
    ];
    $attributes[$column] = 999999;

    expect(fn () => FaceSample::create($attributes))->toThrow(QueryException::class);

    $this->assertDatabaseCount('face_samples', 0);
})->with(['siswa_id', 'pegawai_id', 'created_by']);
