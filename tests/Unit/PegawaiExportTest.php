<?php

use App\Exports\PegawaiExport;
use App\Models\Pegawai;

test('exports employment status and activity under their matching headings', function (string $activity) {
    $pegawai = new Pegawai(['status' => 'GTY/PTY', 'aktif' => $activity]);
    $export = new PegawaiExport;

    $row = array_combine($export->headings(), $export->map($pegawai));

    expect($row['status'])->toBe('GTY/PTY');
    expect($row['aktif'])->toBe($activity);
})->with(['Aktif', 'Non-Aktif', 'Cuti']);
