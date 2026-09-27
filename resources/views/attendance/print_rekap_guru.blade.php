<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Presensi Guru {{ $startDate }} — {{ $endDate }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 24px; font-size: 11px; }
        h1, h2, .period { text-align: center; }
        h1 { font-size: 20px; } h2 { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #9ca3af; padding: 6px; }
        th { background: #f3f4f6; } tr { break-inside: avoid; }
        thead { display: table-header-group; }
        @media print { @page { size: A4 landscape; margin: 10mm; } body { margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <button type="button" onclick="window.print()" class="no-print">Cetak / Simpan PDF</button>
    <h1>{{ $sekolah->nama_sekolah ?? 'Sekolah' }}</h1>
    <h2>Rekap Presensi Guru — {{ ucfirst($mode) }}</h2>
    <p class="period">{{ $startDate }} s.d. {{ $endDate }}</p>
    @include('attendance.report.guru_table')
    <p>H: Hadir, S: Sakit, I: Izin, A: Alpha, P: Pulang, T: Telat. -: Belum ada catatan. Persentase hadir = (H + T) / jumlah catatan status × 100%.</p>
</body>
</html>
