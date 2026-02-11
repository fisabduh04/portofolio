<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi - {{ $date }} - {{ $kelas }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page { margin: 10mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8">
    <div class="max-w-4xl mx-auto">
        {{-- Kop Surat / Header --}}
        <div class="flex items-center gap-4 mb-6 border-b-2 border-gray-800 pb-4">
            <div class="w-16 h-16 shrink-0">
                <img src="{{ $logo ?? asset('img/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="flex-1 text-center">
                <h1 class="text-2xl font-bold uppercase tracking-wider text-gray-900 leading-tight">SMK AL-MIFTAH</h1>
                <h2 class="text-lg font-semibold uppercase text-gray-700 tracking-wide mt-1">
                    Laporan Rekapitulasi Presensi Harian
                    @if(isset($typeGuru) && $typeGuru)
                        <span class="text-blue-600">({{ $typeGuru == 'piket' ? 'Guru Piket' : 'Guru Mapel' }})</span>
                    @endif
                </h2>
                <div class="flex justify-center gap-4 text-sm text-gray-600 mt-2">
                    <p>Kelas: <span class="font-bold text-gray-800">{{ $kelas }}</span></p>
                    <p>|</p>
                    <p>Tanggal: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span></p>
                </div>
            </div>
            <div class="w-16 text-right no-print">
                 <button onclick="window.print()" class="bg-gray-800 text-white p-2 rounded hover:bg-black" title="Cetak / Simpan PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </button>
            </div>
        </div>

        <table class="w-full text-sm text-left border-collapse border border-gray-300">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border border-gray-300 w-10 text-center">No</th>
                    <th class="px-3 py-2 border border-gray-300">Nama Siswa</th>
                    <th class="px-2 py-2 border border-gray-300 text-center w-12">H</th>
                    <th class="px-2 py-2 border border-gray-300 text-center w-12">S</th>
                    <th class="px-2 py-2 border border-gray-300 text-center w-12">I</th>
                    <th class="px-2 py-2 border border-gray-300 text-center w-12 bg-red-50 text-red-700">A</th>
                    <th class="px-3 py-2 border border-gray-300">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekapData as $index => $data)
                <tr class="break-inside-avoid">
                    <td class="px-3 py-2 border border-gray-300 text-center">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 border border-gray-300 font-medium">{{ $data->nama }}</td>
                    <td class="px-2 py-2 border border-gray-300 text-center {{ $data->stats['Hadir'] > 0 ? 'bg-green-50 font-bold' : 'text-gray-300' }}">
                        {{ $data->stats['Hadir'] > 0 ? $data->stats['Hadir'] : '-' }}
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center {{ $data->stats['Sakit'] > 0 ? 'bg-yellow-50 font-bold' : 'text-gray-300' }}">
                        {{ $data->stats['Sakit'] > 0 ? $data->stats['Sakit'] : '-' }}
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center {{ $data->stats['Izin'] > 0 ? 'bg-blue-50 font-bold' : 'text-gray-300' }}">
                        {{ $data->stats['Izin'] > 0 ? $data->stats['Izin'] : '-' }}
                    </td>
                    <td class="px-2 py-2 border border-gray-300 text-center {{ $data->stats['Alpha'] > 0 ? 'bg-red-50 text-red-600 font-bold' : 'text-gray-300' }}">
                        {{ $data->stats['Alpha'] > 0 ? $data->stats['Alpha'] : '-' }}
                    </td>
                    <td class="px-3 py-2 border border-gray-300 text-xs text-gray-600">
                        @if($data->details->count() > 0)
                            <div class="flex flex-col gap-0.5">
                                @foreach($data->details as $log)
                                   @if ($log->status !== 'Hadir')
                                    <span>
                                        <b class="{{ $log->status == 'Alpha' ? 'text-red-700' : ($log->status == 'Sakit' ? 'text-yellow-700' : 'text-blue-700') }}">[{{ strtoupper(substr($log->status, 0, 1)) }}]</b> 
                                        {{ $log->mapel }} ({{ $log->guru }})
                                    </span>
                                   @endif
                                @endforeach
                            </div>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-8 text-xs text-gray-400 no-print">
            Dicetak otomatis oleh Sistem pada {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
    <script>
        // Auto print usually annoying on dev, lets just let user click the button or auto print if param set
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
