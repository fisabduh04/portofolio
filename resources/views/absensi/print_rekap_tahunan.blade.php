<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Tahunan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { size: landscape; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
        body { font-family: 'Times New Roman', Times, serif; }
        .table-print { width: 100%; border-collapse: collapse; font-size: 11px; }
        .table-print th, .table-print td { border: 1px solid black; padding: 4px; text-align: center; vertical-align: middle; }
        .text-left { text-align: left !important; }
        .stat-item { display: inline-block; margin: 0 1px; font-weight: bold; }
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
        <h2 class="text-lg font-bold underline uppercase">LAPORAN REKAPITULASI PRESENSI TAHUNAN</h2>
        <div class="flex justify-center gap-8 text-sm mt-2">
            <p><strong>Kelas:</strong> {{ $kelas }}</p>
            <p><strong>Tahun:</strong> {{ $year }}</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <table class="table-print">
        <thead>
            <tr class="bg-gray-200">
                <th rowspan="2" class="w-8">No</th>
                <th rowspan="2" class="text-left" style="min-width: 150px;">Nama Siswa</th>
                <th colspan="12">Bulan</th>
                <th colspan="5" class="bg-gray-300">Total Akhir</th>
            </tr>
            <tr class="bg-gray-100">
                @for($m=1; $m<=12; $m++)
                    <th>{{ \Carbon\Carbon::create(null, $m)->translatedFormat('M') }}</th>
                @endfor
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
                @for($m=1; $m<=12; $m++)
                    @php
                        $stats = $student->months[$m];
                        $hasData = (($stats['H'] ?? 0) + ($stats['S'] ?? 0) + ($stats['I'] ?? 0) + ($stats['A'] ?? 0) + ($stats['P'] ?? 0) + ($stats['T'] ?? 0)) > 0;
                    @endphp
                    <td>
                        @if($hasData)
                            <div class="flex flex-col text-[9px] gap-0.5">
                                @if((($stats['H'] ?? 0) + ($stats['T'] ?? 0)) > 0) <span class="text-green-700">{{ (($stats['H'] ?? 0) + ($stats['T'] ?? 0)) }}H</span> @endif
                                @if($stats['S'] ?? 0) <span class="text-yellow-700">{{ $stats['S'] }}S</span> @endif
                                @if($stats['I'] ?? 0) <span class="text-blue-700">{{ $stats['I'] }}I</span> @endif
                                @if($stats['A'] ?? 0) <span class="text-red-700 font-bold">{{ $stats['A'] }}A</span> @endif
                                @if($stats['P'] ?? 0) <span class="text-purple-700 font-bold">{{ $stats['P'] }}P</span> @endif
                            </div>
                        @else
                            -
                        @endif
                    </td>
                @endfor
                <td class="font-bold">{{ ($student->total['H'] ?? 0) + ($student->total['T'] ?? 0) }}</td>
                <td class="font-bold">{{ $student->total['S'] ?? 0 }}</td>
                <td class="font-bold">{{ $student->total['I'] ?? 0 }}</td>
                <td class="font-bold">{{ $student->total['A'] ?? 0 }}</td>
                <td class="font-bold">{{ $student->total['P'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="flex justify-end mt-12 mr-12 text-sm">
        <div class="text-center">
            <p>Jember, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Wali Kelas / Kepala Sekolah</p>
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
