<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Laporan', 'href' => route('attendance.report')],
        ['name' => 'Rekap Per Pegawai', 'href' => '#']
    ]" />

@push('styles')
<style>
    @media print {
        @page { margin: 1cm; size: A4; }
        body { background: white; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        nav, header, aside, .no-print, .breadcrumbs, form, .pagination { display: none !important; }
        .print-only { display: block !important; }
        .main-content { margin: 0; padding: 0; box-shadow: none; border: none; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 4px 8px; color: black !important; }
        th { background-color: #f3f4f6 !important; font-weight: bold; text-align: center; }
        .badge { border: none !important; background: transparent !important; color: black !important; font-weight: normal; }
    }
    .print-only { display: none; }
</style>
@endpush

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mt-4 main-content">
        {{-- Internal Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700 no-print">
             <x-layout.sidebar.sub-menu :items="[
                ['name' => 'Dashboard', 'href' => route('attendance.index')],
                ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
                ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
                ['name' => 'Events', 'href' => route('attendance.events.index')],
                ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
                ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
                ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
            ]" />
        </div>

        <div class="p-4 sm:p-6">
            <!-- Filter Section -->
            <form action="{{ route('attendance.report.employee') }}" method="GET" class="mb-8 no-print">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase mb-4">Filter Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="pegawai_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Pegawai</label>
                            <select name="pegawai_id" id="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                                <option value="">-- Pilih Nama Pegawai --</option>
                                @foreach($pegawais as $p)
                                    <option value="{{ $p->id }}" {{ $pegawaiId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="month" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bulan</label>
                            <select name="month" id="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                @foreach($months as $k => $v)
                                    <option value="{{ $k }}" {{ $month == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun</label>
                            <select name="year" id="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="mt-4 flex justify-end">
                        <x-btn type="submit" color="blue" icon="search">Tampilkan Laporan</x-btn>
                    </div>
                </div>
            </form>

            @if($selectedPegawai)
                <!-- Professional Print Header -->
                <div class="print-only mb-8 text-black">
                    <div class="text-center border-b-2 border-black pb-4 mb-4">
                        <h1 class="text-2xl font-bold uppercase">{{ cache('global_sekolah_data')->nama_sekolah ?? 'NAMA SEKOLAH' }}</h1>
                        <p class="text-sm">{{ cache('global_sekolah_data')->alamat_sekolah ?? 'Alamat Sekolah' }}</p>
                    </div>
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold uppercase underline">LAPORAN KEHADIRAN PEGAWAI</h2>
                    </div>
                    <table class="w-full text-sm mb-4 bg-transparent border-none">
                        <tr class="border-none">
                            <td class="w-32 font-bold border-none p-1">Nama Pegawai</td>
                            <td class="w-2 border-none p-1">:</td>
                            <td class="border-none p-1">{{ $selectedPegawai->name }}</td>
                            <td class="w-32 font-bold border-none p-1">Jabatan</td>
                            <td class="w-2 border-none p-1">:</td>
                            <td class="border-none p-1">{{ $selectedPegawai->jenisptk ?? '-' }}</td>
                        </tr>
                        <tr class="border-none">
                            <td class="font-bold border-none p-1">NIP/NUPTK</td>
                            <td class="border-none p-1">:</td>
                            <td class="border-none p-1">{{ $selectedPegawai->nip ?? $selectedPegawai->nuptk ?? '-' }}</td>
                            <td class="font-bold border-none p-1">Periode</td>
                            <td class="border-none p-1">:</td>
                            <td class="border-none p-1">{{ $months[$month] }} {{ $year }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Report Header (Web View) -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4 no-print">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Rekapitulasi Kehadiran</h2>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nama: <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedPegawai->name }}</span> | 
                            Periode: <span class="font-semibold text-gray-900 dark:text-white">{{ $months[$month] }} {{ $year }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('attendance.report.employee.export', ['type' => 'pdf', 'pegawai_id' => $pegawaiId, 'month' => $month, 'year' => $year, 't' => time()]) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-base hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Export PDF
                        </a>
                        <a href="{{ route('attendance.report.employee.export', ['type' => 'excel', 'pegawai_id' => $pegawaiId, 'month' => $month, 'year' => $year, 't' => time()]) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-base hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7-4h14M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z" /></svg>
                            Export Excel
                        </a>
                        <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-base hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Cetak
                        </button>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Tanggal</th>
                                <th scope="col" class="px-6 py-3 font-medium text-center">Jam Masuk</th>
                                <th scope="col" class="px-6 py-3 font-medium text-center">Jam Pulang</th>
                                <th scope="col" class="px-6 py-3 font-medium text-center">Durasi</th>
                                <th scope="col" class="px-6 py-3 font-medium text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceData as $row)
                                <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                    <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                        {{ $row['date'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-body-subtle">
                                        {{ $row['jam_masuk'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-body-subtle">
                                        {{ $row['jam_pulang'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-body-subtle">
                                        {{ $row['durasi'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 badge';
                                            if ($row['status'] == 'Hadir') $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 badge';
                                            elseif ($row['status'] == 'Telat') $badgeClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 badge';
                                            elseif ($row['status'] == 'Alpha' || $row['status'] == 'Terlewat') $badgeClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 badge';
                                            elseif ($row['status'] == 'Libur' || str_contains($row['status'], 'Libur')) $badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 badge';
                                            else $badgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 badge'; 
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs font-medium rounded-full {{ $badgeClass }}">
                                            {{ $row['status'] }}
                                        </span>
                                        @if($row['keterangan'])
                                            <div class="text-xs font-normal text-gray-500 mt-1">({{ $row['keterangan'] }})</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Legend (Hide in Print) -->
                <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-400 no-print">
                    <div class="flex items-center"><span class="w-3 h-3 bg-green-50 border border-green-200 mr-1"></span> Hadir</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-yellow-50 border border-yellow-200 mr-1"></span> Telat</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-red-50 border border-red-200 mr-1"></span> Alpha/Terlewat</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-blue-50 border border-blue-200 mr-1"></span> Event/Kegiatan</div>
                </div>

                <!-- Signature Section (Print Only) -->
                <div class="print-only mt-8 flex justify-end text-black text-sm">
                    <div class="text-center w-64">
                        <p class="mb-20">Mengetahui,<br>Kepala Sekolah</p>
                        <p class="font-bold border-b border-black inline-block min-w-[150px]">{{ cache('global_sekolah_data')->kepala_sekolah ?? '......................' }}</p>
                        <p>NIP. {{ cache('global_sekolah_data')->nip_kepala_sekolah ?? '......................' }}</p>
                    </div>
                </div>

            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                    <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="text-base font-medium">Silakan pilih Pegawai, Bulan, dan Tahun untuk menampilkan laporan.</p>
                </div>
            @endif
        </div>
    </div>
</x-layout.layout>

@if(session('print_auto'))
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
@endif
