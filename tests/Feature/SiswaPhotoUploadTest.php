<?php

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(Tests\TestCase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110145_create_siswas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

function invalidStudentPhoto(string $kind): UploadedFile|string
{
    return match ($kind) {
        'disguised script' => UploadedFile::fake()->createWithContent('portrait.jpg', '<?php echo "not-an-image";'),
        'svg' => UploadedFile::fake()->createWithContent('portrait.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"></svg>'),
        'php extension' => UploadedFile::fake()->image('portrait.php'),
        'oversized' => UploadedFile::fake()->image('portrait.jpg')->size(2049),
        'too wide' => UploadedFile::fake()->image('portrait.jpg', 2049, 100),
        'too tall' => UploadedFile::fake()->image('portrait.jpg', 100, 2049),
        'forged path' => 'uploads/foto/another-student.jpg',
    };
}

it('rejects unsafe student photos on creation without storing files or records', function (string $kind) {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($actor)->post(route('siswa.store'), [
        'nama' => 'Siswa Baru', 'nipd' => 'NEW-001', 'foto' => invalidStudentPhoto($kind),
    ])->assertSessionHasErrors('foto');

    $this->assertDatabaseCount('siswas', 0);
    expect(Storage::disk('public')->allFiles())->toBe([]);
})->with(['disguised script', 'svg', 'php extension', 'oversized', 'too wide', 'too tall', 'forged path']);

it('rejects unsafe replacement photos and preserves the old photo and student data', function (string $kind) {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg']);
    Storage::disk('public')->put($student->foto, 'old-photo');

    $this->actingAs($actor)->put(route('siswa.update', $student), [
        'nama' => 'Nama Diubah', 'nipd' => $student->nipd, 'foto' => invalidStudentPhoto($kind),
    ])->assertSessionHasErrors('foto');

    expect($student->fresh()->foto)->toBe('uploads/foto/old.jpg');
    expect($student->fresh()->nama)->toBe($student->nama);
    expect(Storage::disk('public')->get($student->foto))->toBe('old-photo');
    expect(Storage::disk('public')->allFiles())->toBe(['uploads/foto/old.jpg']);
})->with(['disguised script', 'svg', 'php extension', 'oversized', 'too wide', 'too tall', 'forged path']);

it('stores accepted photos as new jpeg images without appended upload content', function (string $extension) {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $image = UploadedFile::fake()->image('portrait.'.$extension, 120, 80);
    $upload = UploadedFile::fake()->createWithContent('portrait.php.'.$extension,
        $image->getContent().'<?php echo "removed-payload-marker"; ?>');

    $this->actingAs($actor)->post(route('siswa.store'), [
        'nama' => 'Siswa Baru', 'nipd' => 'NEW-001', 'foto' => $upload,
    ])->assertRedirect(route('siswa.index'))->assertSessionHasNoErrors();

    $student = Siswa::sole();
    $contents = Storage::disk('public')->get($student->foto);
    $dimensions = getimagesizefromstring($contents);
    expect($student->foto)->toStartWith('uploads/foto/')->toEndWith('.jpg')->not->toContain('portrait');
    expect($contents)->not->toContain('removed-payload-marker');
    expect($dimensions['mime'])->toBe('image/jpeg');
    expect([$dimensions[0], $dimensions[1]])->toBe([120, 80]);
})->with(['jpg', 'png', 'gif']);

it('replaces the old photo only after the new student data is saved', function () {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg']);
    Storage::disk('public')->put($student->foto, 'old-photo');

    $this->actingAs($actor)->put(route('siswa.update', $student), [
        'nama' => 'Nama Diubah', 'nipd' => $student->nipd, 'foto' => UploadedFile::fake()->image('portrait.png', 120, 80),
    ])->assertRedirect(route('siswa.index'))->assertSessionHasNoErrors();

    $updated = $student->fresh();
    expect($updated->nama)->toBe('Nama Diubah');
    expect($updated->foto)->toEndWith('.jpg')->not->toBe($student->foto);
    Storage::disk('public')->assertExists($updated->foto);
    Storage::disk('public')->assertMissing($student->foto);
});

it('preserves the existing photo when no replacement file is submitted', function (array $photoInput) {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg']);
    Storage::disk('public')->put($student->foto, 'old-photo');

    $this->actingAs($actor)->put(route('siswa.update', $student), [
        'nama' => 'Nama Diubah', 'nipd' => $student->nipd, ...$photoInput,
    ])->assertSessionHasNoErrors();

    expect($student->fresh()->foto)->toBe('uploads/foto/old.jpg');
    Storage::disk('public')->assertExists($student->foto);
})->with(['omitted' => [[]], 'empty' => [['foto' => null]]]);

it('retains the old photo and removes the replacement when a database update fails', function () {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg']);
    Storage::disk('public')->put($student->foto, 'old-photo');
    Siswa::updating(fn () => throw new RuntimeException('Student save failed'));
    $this->withoutExceptionHandling();

    try {
        expect(fn () => $this->actingAs($actor)->put(route('siswa.update', $student), [
            'nama' => 'Nama Diubah', 'nipd' => $student->nipd, 'foto' => UploadedFile::fake()->image('portrait.jpg'),
        ]))->toThrow(RuntimeException::class, 'Student save failed');

        expect($student->fresh()->foto)->toBe('uploads/foto/old.jpg');
        expect($student->fresh()->nama)->toBe($student->nama);
        expect(Storage::disk('public')->allFiles())->toBe(['uploads/foto/old.jpg']);
    } finally {
        Siswa::flushEventListeners();
    }
});

it('removes the uploaded photo when student creation fails', function () {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Siswa::creating(fn () => throw new RuntimeException('Student save failed'));
    $this->withoutExceptionHandling();

    try {
        expect(fn () => $this->actingAs($actor)->post(route('siswa.store'), [
            'nama' => 'Siswa Baru', 'nipd' => 'NEW-001', 'foto' => UploadedFile::fake()->image('portrait.jpg'),
        ]))->toThrow(RuntimeException::class, 'Student save failed');

        $this->assertDatabaseCount('siswas', 0);
        expect(Storage::disk('public')->allFiles())->toBe([]);
    } finally {
        Siswa::flushEventListeners();
    }
});

it('still accepts a status-only ajax update without replacing the student photo', function () {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg', 'aktif' => 'Aktif']);
    Storage::disk('public')->put($student->foto, 'old-photo');

    $this->actingAs($actor)->putJson(route('siswa.update', $student), ['aktif' => 'Non-Aktif'])
        ->assertOk()->assertJsonPath('success', true);

    expect($student->fresh()->aktif)->toBe('Non-Aktif');
    expect($student->fresh()->foto)->toBe('uploads/foto/old.jpg');
    Storage::disk('public')->assertExists($student->foto);
});

it('preserves the old photo and cleans partial files when storage rejects the replacement', function () {
    $actor = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = Siswa::factory()->create(['foto' => 'uploads/foto/old.jpg']);
    $disk = Storage::disk('public');
    $disk->put($student->foto, 'old-photo');
    $failingDisk = Mockery::mock($disk)->makePartial();
    $failingDisk->shouldReceive('put')->once()->andReturnUsing(function (string $path) use ($disk): bool {
        $disk->put($path, 'partial-upload');

        return false;
    });
    Storage::shouldReceive('disk')->with('public')->andReturn($failingDisk);
    $this->withoutExceptionHandling();

    expect(fn () => $this->actingAs($actor)->put(route('siswa.update', $student), [
        'nama' => 'Nama Diubah', 'nipd' => $student->nipd, 'foto' => UploadedFile::fake()->image('portrait.jpg'),
    ]))->toThrow(RuntimeException::class, 'Foto siswa gagal disimpan.');

    expect($student->fresh()->foto)->toBe('uploads/foto/old.jpg');
    expect($student->fresh()->nama)->toBe($student->nama);
    expect($disk->allFiles())->toBe(['uploads/foto/old.jpg']);
});
