<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Absensi Pegawai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { size: portrait; margin: 10mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-4 font-serif text-black">

    <!-- Header / Kop Surat -->
    <x-print.letterhead :sekolah="$sekolah" :logo="$logo ?? null" size="lg" />

    <!-- Judul Laporan -->
    <div class="text-center mb-6">
        <h2 class="text-lg font-bold underline uppercase">LAPORAN KEHADIRAN PEGAWAI</h2>
        
        <div class="mt-4 border border-black p-2 mx-auto w-full md:w-3/4 text-sm text-left">
            <table class="w-full">
                <tr>
                    <td class="p-1 font-bold w-24">Nama</td>
                    <td class="p-1 w-2">:</td>
                    <td class="p-1">{{ $pegawai->name }}</td>
                    <td class="p-1 font-bold w-24">NIP/NIPPK</td>
                    <td class="p-1 w-2">:</td>
                    <td class="p-1">{{ $pegawai->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="p-1 font-bold">Jabatan</td>
                    <td class="p-1 w-2">:</td>
                    <td class="p-1">{{ $pegawai->role }}</td>
                    <td class="p-1 font-bold">Periode</td>
                    <td class="p-1 w-2">:</td>
                    <td class="p-1">{{ $months[$month] ?? '' }} {{ $year }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="w-full">
        <table class="w-full border-collapse border border-black text-[11px]">
            <thead>
                <tr class="bg-gray-200 print:bg-gray-200">
                    <th class="border border-black p-1 w-8">No</th>
                    <th class="border border-black p-1">Tanggal</th>
                    <th class="border border-black p-1">Jam Masuk</th>
                    <th class="border border-black p-1">Jam Pulang</th>
                    <th class="border border-black p-1">Status</th>
                    <th class="border border-black p-1">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dates as $index => $date)
                    @php
                        $attendance = $attendanceData->firstWhere('date', $date->format('Y-m-d'));
                        $isHoliday = \App\Models\HariLibur::isLibur($date->format('Y-m-d'));
                        
                        $bgClass = '';
                        $status = '-';
                        
                        if ($attendance) {
                            $status = $attendance->status;
                            if ($status == 'Hadir') $bgClass = '';
                            elseif ($status == 'Sakit') $bgClass = 'bg-yellow-50 print:bg-yellow-50';
                            elseif ($status == 'Izin') $bgClass = 'bg-blue-50 print:bg-blue-50';
                            elseif ($status == 'Alpha') $bgClass = 'bg-red-50 print:bg-red-50';
                            elseif ($status == 'Telat') $bgClass = 'bg-orange-50 print:bg-orange-50';
                        } elseif ($isHoliday) {
                            $status = 'Libur';
                            $bgClass = 'bg-gray-200 print:bg-gray-200';
                        } else {
                            if ($date->dayOfWeek == 0) { // 0 = Sunday
                                 $status = 'Libur';
                                 $bgClass = 'bg-gray-200 print:bg-gray-200';
                            }
                        }
                    @endphp
                <tr class="{{ $bgClass }}">
                    <td class="border border-black p-1 text-center">{{ $index + 1 }}</td>
                    <td class="border border-black p-1">{{ $date->isoFormat('dddd, D MMMM Y') }}</td>
                    <td class="border border-black p-1 text-center">{{ $attendance->clock_in ?? '-' }}</td>
                    <td class="border border-black p-1 text-center">{{ $attendance->clock_out ?? '-' }}</td>
                    <td class="border border-black p-1 text-center font-bold">{{ $status }}</td>
                    <td class="border border-black p-1 text-left">{{ $attendance->notes ?? ($isHoliday ? 'Hari Libur' : '-') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

     <!-- Ringkasan Absensi -->
    <div class="mt-4 flex justify-start">
        <table class="text-sm border-collapse border border-black text-center">
             <tr>
                 <td class="border border-black px-2 py-1 font-bold bg-green-100 print:bg-green-100">Hadir</td>
                 <td class="border border-black px-2 py-1 font-bold bg-yellow-100 print:bg-yellow-100">Sakit</td>
                 <td class="border border-black px-2 py-1 font-bold bg-blue-100 print:bg-blue-100">Izin</td>
                 <td class="border border-black px-2 py-1 font-bold bg-red-100 print:bg-red-100">Alpha</td>
                 <td class="border border-black px-2 py-1 font-bold bg-orange-100 print:bg-orange-100">Telat</td>
                 <td class="border border-black px-2 py-1 font-bold bg-gray-100 print:bg-gray-100">Total</td>
             </tr>
             <tr>
                <td class="border border-black px-2 py-1">{{ $attendanceData->where('status', 'Hadir')->count() }}</td>
                <td class="border border-black px-2 py-1">{{ $attendanceData->where('status', 'Sakit')->count() }}</td>
                <td class="border border-black px-2 py-1">{{ $attendanceData->where('status', 'Izin')->count() }}</td>
                <td class="border border-black px-2 py-1">{{ $attendanceData->where('status', 'Alpha')->count() }}</td>
                <td class="border border-black px-2 py-1">{{ $attendanceData->where('status', 'Telat')->count() }}</td>
                <td class="border border-black px-2 py-1">{{ $attendanceData->filter(fn($a) => $a->status != 'Libur')->count() }}</td>
             </tr>
        </table>
    </div>


    <!-- Tanda Tangan -->
    <div class="flex justify-end mt-12 mr-12 text-sm">
        <div class="text-center">
            <p>{{ $sekolah->kabupaten ?? 'Jember' }}, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Mengetahui,</p>
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
