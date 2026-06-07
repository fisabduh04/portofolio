<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Bulanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
        body { font-family: 'Times New Roman', Times, serif; }
        .table-print { width: 100%; border-collapse: collapse; font-size: 10px; }
        .table-print th, .table-print td { border: 1px solid black; padding: 4px; text-align: center; }
        .text-left { text-align: left !important; }
    </style>
</head>
<body class="bg-white p-4">

    <!-- Header / Kop Surat -->
    <div class="flex items-center gap-4 mb-4 border-b-4 border-double border-black pb-2">
        <img src="{{ $logo ?? asset('img/logo.png') }}" class="w-20 h-20 object-contain" alt="Logo">
        <div class="flex-1 text-center">
            <h1 class="text-2xl font-bold uppercase tracking-wider">SMK AL-MIFTAH</h1>
            <p class="text-sm">Jln. Pesantren No. 03 , Desa Panyeppen , Kec. Mumbulsari , Kab. Jember , Prov. Jawa Timur</p>
            <p class="text-sm">Telp: 085231456789 | Email: smkalmiftah@example.com</p>
        </div>
    </div>

    <!-- Judul Laporan -->
    <div class="text-center mb-6">
        <h2 class="text-lg font-bold underline uppercase">LAPORAN REKAPITULASI PRESENSI BULANAN</h2>
        <div class="flex justify-center gap-8 text-sm mt-2">
            <p><strong>Kelas:</strong> {{ $kelas }}</p>
            <p><strong>Periode:</strong> {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <table class="table-print">
        <thead>
            <tr class="bg-gray-200">
                <th rowspan="2" class="w-8">No</th>
                <th rowspan="2" class="text-left" style="min-width: 150px;">Nama Siswa</th>
                <th colspan="{{ count($dates) }}">Tanggal</th>
                <th colspan="5">Total</th>
            </tr>
            <tr class="bg-gray-100">
                @foreach($dates as $d)
                    <th class="w-5">{{ $d }}</th>
                @endforeach
                <th class="bg-green-100 w-8">H</th>
                <th class="bg-yellow-100 w-8">S</th>
                <th class="bg-blue-100 w-8">I</th>
                <th class="bg-red-100 w-8">A</th>
                <th class="bg-purple-100 w-8">P</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left font-bold">{{ $student->nama }}</td>
                @foreach($dates as $d)
                    @php
                        $dayData = $student->statuses[$d] ?? ['code' => '-', 'is_libur' => false];
                        $code = $dayData['code'];
                        $bg = '';
                        if($code == 'H' || $code == 'T') $bg = 'bg-green-100';
                        elseif($code == 'S') $bg = 'bg-yellow-100';
                        elseif($code == 'I') $bg = 'bg-blue-100';
                        elseif($code == 'A') $bg = 'bg-red-100';
                        elseif($code == 'P') $bg = 'bg-purple-100';
                    @endphp
                    <td class="{{ $bg }}">{{ $code !== '-' ? ($code == 'T' ? 'H' : $code) : '' }}</td>
                @endforeach
                <td class="font-bold">{{ ($student->stats['H'] ?? 0) + ($student->stats['T'] ?? 0) }}</td>
                <td class="font-bold">{{ $student->stats['S'] }}</td>
                <td class="font-bold">{{ $student->stats['I'] }}</td>
                <td class="font-bold">{{ $student->stats['A'] ?? 0 }}</td>
                <td class="font-bold">{{ $student->stats['P'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="flex justify-end mt-12 mr-12 text-sm">
        <div class="text-center">
            <p>Jember, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Wali Kelas / Guru Piket</p>
            <br><br><br>
            <p class="font-bold underline">_________________________</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
