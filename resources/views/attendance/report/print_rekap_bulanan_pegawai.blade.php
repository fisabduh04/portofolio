<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Absensi Pegawai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { size: landscape; margin: 5mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-4 font-serif text-black">

    <!-- Header / Kop Surat -->
    <x-print.letterhead :sekolah="$sekolah" :logo="$logo ?? null" size="md" />

    <!-- Judul Laporan -->
    <div class="text-center mb-6">
        <h2 class="text-lg font-bold underline uppercase">REKAPITULASI KEHADIRAN PEGAWAI</h2>
        <div class="flex justify-center gap-8 text-sm mt-2">
            <p><strong>Bulan:</strong> {{ \Carbon\Carbon::create()->month($month)->locale('id')->translatedFormat('F') }} {{ $year }}</p>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="w-full">
        <table class="w-full border-collapse border border-black text-[10px]">
            <thead>
                <tr class="bg-gray-200 print:bg-gray-200">
                    <th rowspan="2" class="border border-black p-1 w-6">No</th>
                    <th rowspan="2" class="border border-black p-1 text-left min-w-[150px]">Nama Pegawai</th>
                    <th colspan="{{ count($dates) }}" class="border border-black p-1">Tanggal</th>
                    <th colspan="4" class="border border-black p-1">Total</th>
                </tr>
                <tr class="bg-gray-100 print:bg-gray-100">
                    @foreach($dates as $d)
                        <th class="border border-black p-1 w-5 text-center">{{ $d->day }}</th>
                    @endforeach
                    <th class="border border-black p-1 w-6 bg-green-100 print:bg-green-100">H</th>
                    <th class="border border-black p-1 w-6 bg-yellow-100 print:bg-yellow-100">S</th>
                    <th class="border border-black p-1 w-6 bg-blue-100 print:bg-blue-100">I</th>
                    <th class="border border-black p-1 w-6 bg-red-100 print:bg-red-100">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapData as $index => $row)
                <tr>
                    <td class="border border-black p-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black p-1 text-left font-bold truncate max-w-[200px]">{{ $row->name }}</td>
                    @foreach($dates as $d)
                        @php
                            $dateStr = $d->format('Y-m-d');
                            $statusData = $row->attendance[$dateStr] ?? null;
                            $code = ''; // Default Kosong
                            $bgClass = '';
                            
                            if ($statusData) {
                                $s = $statusData->status;
                                if ($s == 'Hadir' || $s == 'Telat') { $code = 'H'; $bgClass = 'bg-green-100 print:bg-green-100'; }
                                elseif ($s == 'Sakit') { $code = 'S'; $bgClass = 'bg-yellow-100 print:bg-yellow-100'; }
                                elseif ($s == 'Izin') { $code = 'I'; $bgClass = 'bg-blue-100 print:bg-blue-100'; }
                                elseif ($s == 'Alpha') { $code = 'A'; $bgClass = 'bg-red-100 print:bg-red-100'; }
                                else { $code = substr($s,0,1); } 
                            } elseif (\App\Models\HariLibur::isLibur($dateStr) || $d->dayOfWeek == 0) {
                                 $code = '';
                                 $bgClass = 'bg-gray-200 print:bg-gray-200';
                            }
                        @endphp
                        <td class="border border-black p-1 text-center {{ $bgClass }}">{{ $code }}</td>
                    @endforeach
                    
                    @php
                        $h = $row->attendance->filter(fn($a) => in_array($a->status, ['Hadir', 'Telat']))->count();
                        $s = $row->attendance->where('status', 'Sakit')->count();
                        $i = $row->attendance->where('status', 'Izin')->count();
                        $a = $row->attendance->where('status', 'Alpha')->count();
                    @endphp
                    
                    <td class="border border-black p-1 text-center font-bold">{{ $h }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $s }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $i }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $a }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tanda Tangan -->
    <div class="flex justify-end mt-8 mr-12 text-sm">
        <div class="text-center">
            <p>{{ $sekolah->kabupaten ?? 'Jember' }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Sekolah</p>
            <br><br><br>
            <p class="font-bold underline">{{ $sekolah->kepala_sekolah ?? '_________________________' }}</p>
            <p>NIP. {{ $sekolah->nip_kepala_sekolah ?? '-' }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
