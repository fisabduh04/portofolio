<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class FacePrototypeTestCase extends Tests\TestCase
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../../bootstrap/face-prototype.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}

uses(FacePrototypeTestCase::class);

beforeEach(function (): void {
    config(['face-prototype.token' => str_repeat('test', 12)]);
    expect(app()->environment())->toBe('testing');
    expect(DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME))->toBe('sqlite');
    expect(DB::connection()->getDatabaseName())->toBe(':memory:');
    expect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->toBe([]);
});

/** @return array<string, mixed> */
function facePrototypePayload(): array
{
    $vector = array_fill(0, 128, 0.0);
    $vector[0] = 1.0;

    return [
        'direction' => 'masuk', 'consent' => true, 'model' => config('face-prototype.model'),
        'descriptor' => $vector, 'references' => [['alias' => 'UJI-001', 'descriptor' => $vector]],
    ];
}

test('prototype renders a camera page without operational routes', function (): void {
    $this->get('/uji/absensi-wajah')->assertOk()->assertSee('Hasil uji tidak mencatat absensi.')
        ->assertSee('Kode operator dari terminal')->assertHeader('Cache-Control', 'no-store, private');
    $this->get('/absensi/harian')->assertNotFound();
    $this->get('/api/iclock/cdata')->assertNotFound();
});

test('matching requires the temporary operator credential', function (): void {
    $this->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertUnauthorized();
    $this->withToken('wrong')->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertUnauthorized();
});

test('access probe validates credentials without requiring face data', function (): void {
    $this->postJson('/uji/absensi-wajah/access')->assertUnauthorized();
    $this->withToken('wrong')->postJson('/uji/absensi-wajah/access')->assertUnauthorized();
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/access')
        ->assertOk()->assertExactJson(['authorized' => true, 'attendance_saved' => false])
        ->assertHeader('Cache-Control', 'no-store, private');
    expect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->toBe([]);
});

test('missing server credentials are distinguished from an incorrect operator code', function (): void {
    config(['face-prototype.token' => '']);
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', facePrototypePayload())
        ->assertStatus(503)->assertJsonPath('message',
            'Kode operator belum diterima server. Hentikan server dengan Ctrl+C, lalu jalankan kembali npm run face:start.');
});

test('matching refuses non JSON requests with a valid credential', function (): void {
    $this->withToken(str_repeat('test', 12))->post('/uji/absensi-wajah/match', facePrototypePayload())
        ->assertStatus(415);
});

test('unsafe environments and database configurations fail closed', function (string $setting, mixed $value, int $status): void {
    config([$setting => $value]);
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertStatus($status);
})->with([
    'disabled isolation' => ['face-prototype.isolated', false, 404],
    'wrong driver' => ['database.default', 'mysql', 503],
    'file database' => ['database.connections.sqlite.database', 'school.sqlite', 503],
    'overriding URL' => ['database.connections.sqlite.url', 'mysql://invalid', 503],
    'extra operational connection' => ['database.connections.mysql', ['driver' => 'mysql'], 503],
]);

test('production environment refuses the prototype even with a valid token', function (): void {
    app()->instance('env', 'production');
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertNotFound();
});

test('one or more references identify a candidate without database writes or queued work', function (): void {
    Queue::fake();
    $this->mock(App\Services\AttendanceService::class)->shouldNotReceive('calculate');
    $queries = [];
    DB::listen(function ($query) use (&$queries): void {
        $queries[] = $query->sql;
    });
    $payload = facePrototypePayload();
    $response = $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload);
    $response->assertOk()->assertJsonPath('status', 'candidate')->assertJsonPath('candidates.0.alias', 'UJI-001')
        ->assertJsonPath('attendance_saved', false)->assertJsonPath('timings.storage_ms', null)
        ->assertHeader('Server-Timing')->assertJsonPath('reference_count', 1);
    expect($response->json('timings.matching_ms'))->toBeGreaterThanOrEqual(0);
    expect($response->json('timings.server_ms'))->toBeGreaterThanOrEqual($response->json('timings.matching_ms'));
    foreach (['bootstrap_ms', 'guard_ms', 'dispatch_validation_ms'] as $stage) {
        expect($response->json('timings.'.$stage))->toBeNumeric()->toBeGreaterThanOrEqual(0);
    }
    expect($response->json('opcode_cache_enabled'))->toBeBool();
    $payload['references'][] = $payload['references'][0];
    $other = $payload['references'][0];
    $other['alias'] = 'UJI-002';
    $other['descriptor'][0] = 1.4;
    $payload['references'][] = $other;
    $this->postJson('/uji/absensi-wajah/match', $payload)->assertOk()
        ->assertJsonPath('status', 'candidate')->assertJsonCount(2, 'candidates');
    expect($queries)->toBe([]);
    expect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->toBe([]);
    Queue::assertNothingPushed();
});

test('close candidates from different people are ambiguous', function (): void {
    $payload = facePrototypePayload();
    $payload['references'][] = ['alias' => 'UJI-002', 'descriptor' => $payload['descriptor']];
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload)
        ->assertOk()->assertJsonPath('status', 'ambiguous')->assertJsonCount(2, 'candidates');
});

test('distant faces remain unknown even when a nearest candidate exists', function (): void {
    $payload = facePrototypePayload();
    $payload['descriptor'][0] = -1;
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload)
        ->assertOk()->assertJsonPath('status', 'unknown')->assertJsonPath('candidates.0.alias', 'UJI-001');
});

test('repeated requests are measurements and never create attendance', function (): void {
    $this->withToken(str_repeat('test', 12));
    for ($i = 0; $i < 3; $i++) {
        $this->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertOk()->assertJsonPath('attendance_saved', false);
    }
    expect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->toBe([]);
});

test('invalid biometric requests receive validation errors', function (string $field, mixed $value, string $error): void {
    $payload = facePrototypePayload();
    data_set($payload, $field, $value);
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload)
        ->assertUnprocessable()->assertJsonValidationErrors([$error]);
})->with([
    'checkout forbidden' => ['direction', 'pulang', 'direction'],
    'consent required' => ['consent', false, 'consent'],
    'model mismatch' => ['model', 'another-model', 'model'],
    'wrong dimensions' => ['descriptor', [1, 2], 'descriptor'],
    'nonnumeric coordinate' => ['descriptor.0', 'NaN', 'descriptor.0'],
    'boolean coordinate' => ['descriptor.0', true, 'descriptor.0'],
    'null coordinate' => ['descriptor.0', null, 'descriptor.0'],
    'nested coordinate' => ['descriptor.0', [1], 'descriptor.0'],
    'infinite numeric string' => ['descriptor.0', '1e9999', 'descriptor.0'],
    'negative out of range coordinate' => ['descriptor.0', -2.01, 'descriptor.0'],
    'out of range coordinate' => ['descriptor.0', 1e30, 'descriptor.0'],
    'no references' => ['references', [], 'references'],
    'personal name forbidden' => ['references.0.alias', '<script>name</script>', 'references.0.alias'],
    'bad reference dimensions' => ['references.0.descriptor', [1], 'references.0.descriptor'],
    'bad reference coordinate' => ['references.0.descriptor.0', 'bad', 'references.0.descriptor.0'],
    'boolean reference coordinate' => ['references.0.descriptor.127', false, 'references.0.descriptor.127'],
    'unexpected reference key' => ['references.0.siswa_id', 42, 'references.0'],
]);

test('all coordinates are checked at the reference limit and valid boundary numbers remain accepted', function (): void {
    $payload = facePrototypePayload();
    $payload['descriptor'] = array_fill(0, 128, -2);
    $payload['descriptor'][127] = '2';
    $payload['references'] = array_fill(0, 50, ['alias' => 'UJI-001', 'descriptor' => $payload['descriptor']]);
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload)
        ->assertOk()->assertJsonPath('status', 'candidate')->assertJsonPath('reference_count', 50);
    $payload['references'][49]['descriptor'][127] = 2.01;
    $this->postJson('/uji/absensi-wajah/match', $payload)->assertUnprocessable()
        ->assertJsonValidationErrors(['references.49.descriptor.127']);
});

test('reference and request size limits reject excessive input', function (): void {
    $payload = facePrototypePayload();
    $payload['references'] = array_fill(0, 51, $payload['references'][0]);
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', $payload)
        ->assertUnprocessable()->assertJsonValidationErrors(['references']);
    $payload['padding'] = str_repeat('x', 524289);
    $this->postJson('/uji/absensi-wajah/match', $payload)->assertStatus(413);
});

test('separate requests cannot retrieve another tabs references', function (): void {
    $this->withToken(str_repeat('test', 12))->postJson('/uji/absensi-wajah/match', facePrototypePayload())->assertOk();
    $payload = facePrototypePayload();
    $payload['references'] = [];
    $this->postJson('/uji/absensi-wajah/match', $payload)->assertUnprocessable()->assertJsonValidationErrors(['references']);
});
