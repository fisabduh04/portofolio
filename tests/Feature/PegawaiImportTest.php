<?php

use App\Exports\PegawaiExport;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->artisan('migrate', ['--path' => [
        'database/migrations/0001_01_01_000000_create_users_table.php',
        'database/migrations/2024_05_04_110159_create_pegawais_table.php',
    ], '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

it('preserves every identifier digit when exporting and importing an xlsx file', function (string $identifier) {
    $pegawai = Pegawai::factory()->create([
        'nuptk' => $identifier, 'hp' => '081234567890', 'skpengangkatan' => '00001234567890123456',
        'npwp' => $identifier, 'nonik' => $identifier, 'nokk' => $identifier,
    ]);
    $file = tmpfile();

    try {
        $contents = Excel::raw(new PegawaiExport, \Maatwebsite\Excel\Excel::XLSX);
        fwrite($file, $contents);
        $workbook = (new Xlsx)->load(stream_get_meta_data($file)['uri']);
        $sheet = $workbook->getActiveSheet();

        foreach (['B2' => $identifier, 'N2' => '081234567890', 'O2' => '00001234567890123456', 'W2' => $identifier, 'X2' => $identifier, 'Y2' => $identifier] as $address => $expected) {
            $cell = $sheet->getCell($address);
            expect($cell->getValue())->toBe($expected);
            expect($cell->getDataType())->toBe(DataType::TYPE_STRING);
            expect($cell->getFormattedValue())->toBe($expected);
            expect($cell->getStyle()->getNumberFormat()->getFormatCode())->toBe(NumberFormat::FORMAT_TEXT);
        }
        $workbook->disconnectWorksheets();

        $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
            ->post(route('importpegawai'), ['file' => UploadedFile::fake()->createWithContent('pegawai.xlsx', $contents)])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseCount('pegawais', 1);
        $this->assertDatabaseHas('pegawais', [
            'id' => $pegawai->id, 'nuptk' => $identifier, 'nonik' => $identifier, 'nokk' => $identifier,
            'npwp' => $identifier, 'hp' => '081234567890', 'skpengangkatan' => '00001234567890123456',
        ]);
    } finally {
        fclose($file);
    }
})->with(['16 digits' => '3578123456789123', 'leading zeros' => '0012345678901234']);

it('updates the existing employee by NUPTK and reports new unchanged and skipped rows', function () {
    $pegawai = Pegawai::factory()->create(['nuptk' => '0012345678901234', 'name' => 'Nama Lama', 'aktif' => 'GTY/PTY', 'status' => 'Aktif']);
    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]));
    $csv = "nuptk,name,status,aktif\n 0012345678901234 ,Nama Baru,GTY/PTY,Aktif\n9988776655443322,Pegawai Baru,GTY,Non-Aktif\n,Nama Tanpa NUPTK,GTY,Aktif\n";

    $this->post(route('importpegawai'), ['file' => UploadedFile::fake()->createWithContent('pegawai.csv', $csv)])
        ->assertRedirect(route('pegawai.index'))
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', 'Impor pegawai: 1 baru, 1 diperbarui, 0 tidak berubah, 1 dilewati. Baris dilewati karena kolom nuptk atau name kosong. Periksa judul kolom file.');

    $this->assertDatabaseCount('pegawais', 2);
    $this->assertDatabaseHas('pegawais', ['id' => $pegawai->id, 'nuptk' => '0012345678901234', 'name' => 'Nama Baru', 'aktif' => 'Aktif', 'status' => 'GTY/PTY']);
    $this->assertDatabaseHas('pegawais', ['nuptk' => '9988776655443322', 'aktif' => 'Non-Aktif']);

    $this->post(route('importpegawai'), ['file' => UploadedFile::fake()->createWithContent('pegawai.csv', $csv)])
        ->assertSessionHas('message', 'Impor pegawai: 0 baru, 0 diperbarui, 2 tidak berubah, 1 dilewati. Baris dilewati karena kolom nuptk atau name kosong. Periksa judul kolom file.');
    $this->assertDatabaseCount('pegawais', 2);
});

it('reports missing name headings instead of claiming a successful update', function () {
    $pegawai = Pegawai::factory()->create(['nuptk' => '12345', 'aktif' => 'Non-Aktif']);

    $this->actingAs(User::factory()->create(['role' => 'admin', 'is_active' => 1]))
        ->post(route('importpegawai'), ['file' => UploadedFile::fake()->createWithContent('pegawai.csv', "nuptk,nama,aktif\n12345,Nama Baru,Aktif\n")])
        ->assertSessionHas('type', 'warning')
        ->assertSessionHas('message', 'Impor pegawai: 0 baru, 0 diperbarui, 0 tidak berubah, 1 dilewati. Baris dilewati karena kolom nuptk atau name kosong. Periksa judul kolom file.');

    $this->assertDatabaseCount('pegawais', 1);
    $this->assertDatabaseHas('pegawais', ['id' => $pegawai->id, 'aktif' => 'Non-Aktif']);
});
