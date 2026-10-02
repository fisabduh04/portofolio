<?php

use App\Models\FaceSample;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('stores only three encrypted samples when reenrolling and removes previous versions for that student', function (string $role) {
    $operator = User::factory()->create(['role' => $role, 'is_active' => 1]);
    $student = Siswa::factory()->create();
    $other = Siswa::factory()->create();
    $previous = FaceSample::create(['siswa_id' => $student->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id]);
    $inactive = FaceSample::create(['siswa_id' => $student->id, 'model' => 'old', 'descriptor' => [0.25], 'is_active' => false, 'created_by' => $operator->id]);
    $untouched = FaceSample::create(['siswa_id' => $other->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id]);
    $samples = array_fill(0, 3, array_fill(0, 128, 0.125));

    $this->actingAs($operator)->postJson(route('siswa.face.store', $student), ['samples' => $samples, 'siswa_id' => $other->id, 'created_by' => 999])
        ->assertExactJson(['message' => 'Tiga sampel wajah berhasil disimpan.', 'count' => 3]);

    expect($previous->fresh())->toBeNull();
    expect($inactive->fresh())->toBeNull();
    expect($untouched->fresh()->is_active)->toBeTrue();
    $saved = FaceSample::where('siswa_id', $student->id)->where('is_active', true)->get();
    expect($saved)->toHaveCount(3);
    expect($saved->pluck('label')->all())->toBe(['front', 'left', 'right']);
    expect($saved->first()->descriptor)->toBe($samples[0]);
    expect($saved->first()->created_by)->toBe($operator->id);
    expect(DB::table('face_samples')->where('id', $saved->first()->id)->value('descriptor'))->not->toBe(json_encode($samples[0]));
    $this->postJson(route('siswa.face.store', $student), ['samples' => $samples])->assertOk();
    expect(FaceSample::where('siswa_id', $student->id)->count())->toBe(3);
})->with(['admin', 'operator', 'kepala']);

it('rejects incomplete or invalid samples without replacing saved data', function (array $samples, string $field, string $message) {
    $operator = User::factory()->create(['role' => 'operator', 'is_active' => 1]);
    $student = Siswa::factory()->create();
    $previous = FaceSample::create(['siswa_id' => $student->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id]);

    $this->actingAs($operator)->postJson(route('siswa.face.store', $student), ['samples' => $samples])
        ->assertInvalid([$field => $message]);

    expect($previous->fresh()->is_active)->toBeTrue();
    $this->assertDatabaseCount('face_samples', 1);
})->with([
    'missing poses' => [[array_fill(0, 128, 0.1)], 'samples', 'Diperlukan tepat tiga sampel wajah.'],
    'short descriptor' => [array_fill(0, 3, [0.1]), 'samples.0', 'Sampel wajah tidak lengkap. Silakan ambil ulang.'],
    'invalid number' => [array_fill(0, 3, array_fill(0, 128, 'bad')), 'samples.0.0', 'Sampel wajah tidak valid. Silakan ambil ulang.'],
    'out of range' => [array_fill(0, 3, array_fill(0, 128, 10)), 'samples.0.0', 'Sampel wajah di luar batas. Silakan ambil ulang.'],
]);

it('forbids unauthorized and inactive accounts from enrolling faces', function (string $role, int $active) {
    $user = User::factory()->create(['role' => $role, 'is_active' => $active]);
    $student = Siswa::factory()->create();

    $this->actingAs($user)->postJson(route('siswa.face.store', $student), ['samples' => array_fill(0, 3, array_fill(0, 128, 0.1))])->assertForbidden();
    $this->actingAs($user)->deleteJson(route('siswa.face.destroy', $student), ['confirm_delete' => 1])->assertForbidden();

    $this->assertDatabaseCount('face_samples', 0);
})->with([['guru', 1], ['siswa', 1], ['staff', 1], ['bendahara', 1], ['admin', 0]]);

it('requires authentication for face enrollment', function () {
    $this->postJson(route('siswa.face.store', 1), [])->assertUnauthorized();
    $this->deleteJson(route('siswa.face.destroy', 1), ['confirm_delete' => 1])->assertUnauthorized();
});

it('rejects missing students and empty face vectors', function () {
    $operator = User::factory()->create(['role' => 'admin', 'is_active' => 1]);
    $student = Siswa::factory()->create();
    $this->actingAs($operator)->postJson(route('siswa.face.store', 999999), [])->assertNotFound();
    $this->postJson(route('siswa.face.store', $student), ['samples' => array_fill(0, 3, array_fill(0, 128, 0))])->assertUnprocessable();
    $this->assertDatabaseCount('face_samples', 0);
});

it('renders the face panel with escaped student identity and saved status', function () {
    $student = Siswa::factory()->make(['nama' => '<script>alert(1)</script>']);
    $student->id = 1;

    $html = view('siswa.face-enrollment', ['siswa' => $student, 'faceSampleCount' => 3, 'kelasAktif' => 'X A'])->render();

    expect($html)->toContain('3 sampel terdaftar', 'Simpan wajah', 'Hapus data wajah', 'X A', 'confirm_delete', '&lt;script&gt;alert(1)&lt;/script&gt;')->not->toContain('<script>alert(1)</script>');
});

it('deletes only the selected students face samples after confirmation and preserves attendance', function () {
    $operator = User::factory()->create(['role' => 'operator', 'is_active' => 1]);
    $student = Siswa::factory()->create();
    $other = Siswa::factory()->create();
    foreach ([$student, $other] as $owner) {
        FaceSample::create(['siswa_id' => $owner->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id]);
        FaceSample::create(['siswa_id' => $owner->id, 'model' => 'old', 'descriptor' => [0.25], 'is_active' => false, 'created_by' => $operator->id]);
    }
    $schedule = \App\Models\Jadwal::factory()->create();
    $logbook = \App\Models\Logbook::create(['jadwal_id' => $schedule->id, 'kelas_id' => $schedule->kelas_id, 'pegawai_id' => $schedule->pegawai_id, 'tanggal' => '2026-10-02', 'kategori' => 'mapel']);
    $attendance = \App\Models\Absensi::create(['siswa_id' => $student->id, 'logbook_id' => $logbook->id, 'status' => 'Hadir']);
    $this->actingAs($operator)->deleteJson(route('siswa.face.destroy', $student))->assertInvalid(['confirm_delete']);
    $this->assertDatabaseCount('face_samples', 4);
    $this->delete(route('siswa.face.destroy', $student), ['confirm_delete' => 1, 'siswa_id' => $other->id])->assertRedirect(route('siswa.show', $student));
    expect(FaceSample::where('siswa_id', $student->id)->count())->toBe(0);
    expect(FaceSample::where('siswa_id', $other->id)->count())->toBe(2);
    expect($student->fresh())->not->toBeNull();
    expect($attendance->fresh()->status)->toBe('Hadir');
    $this->delete(route('siswa.face.destroy', $student), ['confirm_delete' => 1])->assertRedirect();
    $this->deleteJson(route('siswa.face.destroy', 999999), ['confirm_delete' => 1])->assertNotFound();
});

it('previews and prunes only inactive student face samples', function () {
    $operator = User::factory()->create();
    $student = Siswa::factory()->create();
    $attributes = ['siswa_id' => $student->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id];
    $active = FaceSample::create($attributes);
    $inactive = FaceSample::create([...$attributes, 'is_active' => false]);
    $employee = FaceSample::create([...$attributes, 'siswa_id' => null, 'pegawai_id' => \App\Models\Pegawai::factory()->create()->id, 'is_active' => false]);
    $this->artisan('attendance:prune-inactive-face-samples')->assertSuccessful();
    expect($inactive->fresh())->not->toBeNull();
    $this->artisan('attendance:prune-inactive-face-samples', ['--apply' => true])->expectsOutput('Dihapus: 1 sampel nonaktif.')->assertSuccessful();
    expect($inactive->fresh())->toBeNull();
    expect($active->fresh())->not->toBeNull();
    expect($employee->fresh())->not->toBeNull();
});

it('restores previous samples if replacement fails partway through saving', function () {
    $operator = User::factory()->create(['role' => 'admin', 'is_active' => 1]);
    $student = Siswa::factory()->create();
    $previous = FaceSample::create(['siswa_id' => $student->id, 'model' => 'old', 'descriptor' => [0.25], 'created_by' => $operator->id]);
    $dispatcher = FaceSample::getEventDispatcher();
    FaceSample::setEventDispatcher(clone $dispatcher);
    $attempts = 0;
    FaceSample::creating(function () use (&$attempts): void {
        if (++$attempts === 2) {
            throw new \RuntimeException('Simulated storage failure');
        }
    });
    try {
        $this->withoutExceptionHandling()->actingAs($operator);
        expect(fn () => $this->postJson(route('siswa.face.store', $student), ['samples' => array_fill(0, 3, array_fill(0, 128, 0.1))]))
            ->toThrow(\RuntimeException::class, 'Simulated storage failure');
    } finally {
        FaceSample::setEventDispatcher($dispatcher);
    }
    expect($previous->fresh()->descriptor)->toBe([0.25]);
    expect($previous->fresh()->is_active)->toBeTrue();
    $this->assertDatabaseCount('face_samples', 1);
});
