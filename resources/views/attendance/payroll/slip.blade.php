<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $pegawai->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
        }
        .details-table, .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 5px;
        }
        .salary-table th, .salary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .salary-table th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature p {
            margin-top: 60px;
            border-top: 1px solid #333;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak / Simpan PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer;">Tutup</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>SLIP GAJI PEGAWAI</h1>
            <p>Periode: {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
        </div>

        <table class="details-table">
            <tr>
                <td width="150">Nama</td>
                <td>: {{ $pegawai->name }}</td>
                <td width="150">Jabatan</td>
                <td>: {{ $pegawai->jenis_ptk ?? '-' }}</td>
            </tr>
            <tr>
                <td>NIP/NUPTK</td>
                <td>: {{ $pegawai->nuptk }}</td>
                <td>Status</td>
                <td>: {{ $pegawai->status_kepegawaian ?? '-' }}</td>
            </tr>
        </table>

        <h3>Rincian Kehadiran</h3>
        <table class="salary-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th class="text-right">Honor Harian</th>
                    <th class="text-right">Uang Makan</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absensi as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $log->jam_masuk ?? '-' }}</td>
                    <td>{{ $log->jam_pulang ?? '-' }}</td>
                    <td>{{ $log->status }}</td>
                    <td class="text-right">{{ number_format($log->nominal_gaji, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($log->nominal_makan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($log->total_honor, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h3>Ringkasan Penerimaan</h3>
        <table class="salary-table">
            <tr>
                <td>Total Gaji Pokok</td>
                <td class="text-right">{{ number_format($summary['total_gaji'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Uang Makan</td>
                <td class="text-right">{{ number_format($summary['total_makan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan (Keterlambatan dll)</td>
                <td class="text-right text-red-600">
                    - {{ number_format(($summary['total_gaji'] + $summary['total_makan']) - $summary['grand_total'], 0, ',', '.') }}
                </td>
            </tr>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td>TOTAL DITERIMA (THP)</td>
                <td class="text-right">Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature">
                <br>
                Penerima,
                <p>{{ $pegawai->name }}</p>
            </div>
            <div class="signature">
                {{ now()->format('d F Y') }}<br>
                Bendahara / Kepala Sekolah,
                <p>( ................................. )</p>
            </div>
        </div>
    </div>

</body>
</html>
