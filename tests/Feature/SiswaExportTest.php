<?php

use App\Exports\SiswaExport;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'database.connections.sqlite.url' => null]);
    DB::purge('sqlite');
    $this->artisan('migrate', ['--path' => 'database/migrations/2024_05_04_110145_create_siswas_table.php', '--no-interaction' => true])->assertExitCode(0);
});

afterEach(function () {
    DB::purge('sqlite');
});

it('preserves student identifiers as text in the exported xlsx while keeping measurements numeric', function (?string $identifier) {
    $fields = [
        'B' => 'nipd', 'D' => 'nisn', 'I' => 'nik', 'T' => 'telepon', 'U' => 'hp',
        'W' => 'skhun', 'Y' => 'nokps', 'AE' => 'nikayah', 'AK' => 'nikibu', 'AQ' => 'nikwali',
        'AS' => 'nopesertaunas', 'AT' => 'noijazah', 'AV' => 'nomorkip', 'AX' => 'nomorkks',
        'AY' => 'noaktalahir', 'BA' => 'nomor_rekening_bank', 'BJ' => 'nokk',
    ];
    $attributes = array_fill_keys(array_values($fields), $identifier);
    $attributes['nipd'] = $identifier ?? '12345';
    $siswa = Siswa::factory()->create([...$attributes, 'beratbadan' => 45]);
    $file = tmpfile();

    try {
        fwrite($file, Excel::raw(new SiswaExport([$siswa->id]), \Maatwebsite\Excel\Excel::XLSX));
        $workbook = (new Xlsx)->load(stream_get_meta_data($file)['uri']);
        $sheet = $workbook->getActiveSheet();

        foreach ($fields as $column => $field) {
            $cell = $sheet->getCell($column.'2');
            expect($cell->getValue())->toBe($attributes[$field]);
            if ($attributes[$field] !== null) {
                expect($cell->getDataType())->toBe(DataType::TYPE_STRING);
                expect($cell->getFormattedValue())->toBe($attributes[$field]);
                expect($cell->getStyle()->getNumberFormat()->getFormatCode())->toBe(NumberFormat::FORMAT_TEXT);
            }
        }
        expect($sheet->getCell('BK2')->getValue())->toBe(45);
        expect($sheet->getCell('BK2')->getDataType())->toBe(DataType::TYPE_NUMERIC);
        $workbook->disconnectWorksheets();
    } finally {
        fclose($file);
    }
})->with(['16 digits' => '3578123456789123', 'leading zeros' => '0012345678901234', 'empty identifiers' => null]);
