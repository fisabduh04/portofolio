<?php

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Support\Facades\DB;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->withoutVite();
    Illuminate\Support\Facades\View::share('sekolah', new App\Models\Sekolah);
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110145_create_siswas_table.php',
        'database/migrations/2024_05_04_110200_create_jurusans_table.php',
        'database/migrations/2024_05_04_110210_create_kelas_table.php',
        'database/migrations/2024_05_04_110313_create_tahuns_table.php',
        'database/migrations/2024_05_12_055121_create_kelas_siswas_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

test('failed class assignments keep every draft and show Indonesian errors below the title', function () {
    $student = Siswa::factory()->create();
    $year = Tahun::factory()->create(['isActive' => true]);
    $class = Kelas::factory()->create();
    $data = ['_kelassiswa_form' => '1', 'tahun_id' => $year->id,
        'siswa_id' => [$student->id, ''], 'kelas_id' => [$class->id, $class->id], 'ket' => ['aktif', 'do']];
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('kelassiswa.index'))->post(route('kelassiswa.store'), $data)
        ->assertRedirect(route('kelassiswa.index'))->assertSessionHasErrors(['siswa_id.1' => 'Siswa wajib diisi.'])
        ->assertSessionHas('_old_input.ket', ['aktif', 'do']);
    $this->assertDatabaseCount('kelas_siswas', 0);
    $response = $this->get(route('kelassiswa.index'))->assertOk();
    $response->assertSeeInOrder(['Rombongan Belajar', 'Data belum disimpan.', 'Siswa wajib diisi.']);
});

test('failed standard edits preserve the edited assignment and its fields', function () {
    $assignment = KelasSiswa::create(['siswa_id' => Siswa::factory()->create()->id,
        'kelas_id' => Kelas::factory()->create()->id, 'tahun_id' => Tahun::factory()->create(['isActive' => true])->id, 'ket' => 'aktif']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('kelassiswa.index'))->put(route('kelassiswa.update', $assignment),
            ['_kelassiswa_form' => '1', 'siswa' => $assignment->siswa_id, 'kelas' => '', 'tahun' => $assignment->tahun_id, 'ket' => 'do'])
        ->assertSessionHasErrors(['kelas_id' => 'Kelas wajib diisi.'])
        ->assertSessionHas('_old_input._kelassiswa_edit_id', (string) $assignment->id)
        ->assertSessionHas('_old_input.ket', 'do');
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id, 'ket' => 'aktif']);
    $this->get(route('kelassiswa.index'))->assertOk();
});

test('stopped students can be assigned and the originating table filters are preserved', function () {
    $year = Tahun::factory()->create(['isActive' => true]);
    $student = Siswa::factory()->create();
    $class = Kelas::factory()->create();
    $origin = route('kelassiswa.index', ['filter_tahun' => $year->id, 'filter_kelas' => $class->id, 'search' => 'Siswa', 'per_page' => 25, 'page' => 2]);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from($origin)->post(route('kelassiswa.store'), ['tahun_id' => $year->id,
            'siswa_id' => [$student->id], 'kelas_id' => [$class->id], 'ket' => ['do']])
        ->assertRedirect($origin)->assertSessionHasNoErrors();
    $this->assertDatabaseHas('kelas_siswas', ['siswa_id' => $student->id, 'ket' => 'do']);
});

test('an import without a file gives an Indonesian validation error', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('kelassiswa.index'))->post(route('kelas-siswa-import'))
        ->assertRedirect(route('kelassiswa.index'))
        ->assertSessionHasErrors(['file' => 'Pilih berkas yang akan diimpor terlebih dahulu.']);
});

function createClassAssignment(): KelasSiswa
{
    return KelasSiswa::create([
        'siswa_id' => Siswa::factory()->create()->id,
        'kelas_id' => Kelas::factory()->create()->id,
        'tahun_id' => Tahun::factory()->create(['isActive' => true])->id,
        'ket' => 'aktif',
    ]);
}

test('standard edits validate identities and status consistently', function (string $field, mixed $value, string $message) {
    $assignment = createClassAssignment();
    $data = $assignment->only(['siswa_id', 'kelas_id', 'tahun_id', 'ket']);
    $data[$field] = $value;

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from(route('kelassiswa.index'))->put(route('kelassiswa.update', $assignment), $data)
        ->assertSessionHasErrors([$field => $message]);
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id] + $assignment->only(['siswa_id', 'kelas_id', 'tahun_id', 'ket']));
})->with([
    ['siswa_id', 99999, 'Siswa yang dipilih tidak tersedia. Silakan pilih kembali.'],
    ['kelas_id', 99999, 'Kelas yang dipilih tidak tersedia. Silakan pilih kembali.'],
    ['tahun_id', 99999, 'Tahun ajaran yang dipilih tidak tersedia. Silakan pilih kembali.'],
    ['ket', 'invalid', 'Status siswa tidak valid. Pilih Aktif, Berhenti, Naik Kelas, atau Tidak Naik Kelas.'],
]);

test('an edit using consistent field names saves and keeps table state', function () {
    $assignment = createClassAssignment();
    $newClass = Kelas::factory()->create();
    $data = $assignment->only(['siswa_id', 'kelas_id', 'tahun_id', 'ket']);
    $data['kelas_id'] = $newClass->id;
    $origin = route('kelassiswa.index', ['filter_tahun' => $assignment->tahun_id, 'per_page' => 25]);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->from($origin)->put(route('kelassiswa.update', $assignment), $data)
        ->assertRedirect($origin)->assertSessionHasNoErrors();
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id, 'kelas_id' => $newClass->id]);
});

test('inline status updates return JSON without requiring unrelated form fields', function () {
    $assignment = createClassAssignment();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->putJson(route('kelassiswa.update', $assignment), ['ket' => 'do'])
        ->assertOk()->assertJsonPath('data.ket', 'do');
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id, 'ket' => 'do']);
});

test('an invalid inline status returns an Indonesian JSON error and keeps saved data', function () {
    $assignment = createClassAssignment();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->putJson(route('kelassiswa.update', $assignment), ['ket' => 'invalid'])
        ->assertUnprocessable()->assertJsonValidationErrors('ket')
        ->assertJsonPath('errors.ket.0', 'Status siswa tidak valid. Pilih Aktif, Berhenti, Naik Kelas, atau Tidak Naik Kelas.');
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id, 'ket' => 'aktif']);
});

test('bulk rows with mismatched lengths are rejected before any record is written', function () {
    $students = Siswa::factory()->count(2)->create();
    $year = Tahun::factory()->create(['isActive' => true]);
    $class = Kelas::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->post(route('kelassiswa.store'), ['siswa_id' => $students->pluck('id')->all(), 'kelas_id' => [$class->id],
            'tahun_id' => $year->id, 'ket' => ['aktif', 'do']])
        ->assertSessionHasErrors(['kelas_id' => 'Jumlah isian Kelas harus sesuai dengan jumlah siswa.']);
    $this->assertDatabaseCount('kelas_siswas', 0);
});

test('bulk saving updates an existing assignment without creating a duplicate', function () {
    $assignment = createClassAssignment();
    $newClass = Kelas::factory()->create();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->post(route('kelassiswa.store'), ['siswa_id' => [$assignment->siswa_id], 'kelas_id' => [$newClass->id],
            'tahun_id' => $assignment->tahun_id, 'ket' => ['naik']])
        ->assertSessionHasNoErrors();
    $this->assertDatabaseCount('kelas_siswas', 1);
    $this->assertDatabaseHas('kelas_siswas', ['id' => $assignment->id, 'kelas_id' => $newClass->id, 'ket' => 'naik']);
});

test('the edit route loads its assignment with the same filtered table', function () {
    $assignment = createClassAssignment();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => true]))
        ->get(route('kelassiswa.edit', ['kelassiswa' => $assignment->id, 'filter_kelas' => $assignment->kelas_id]))
        ->assertOk()
        ->assertViewHas('pemetaan', fn ($value) => $value->is($assignment))
        ->assertViewHas('pemetaans', fn ($rows) => $rows->total() === 1);
});
