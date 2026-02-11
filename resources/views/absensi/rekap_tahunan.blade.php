<x-layout.layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :title="'Laporan Tahunan'" :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Rekapitulasi', 'href' => '#'],
                    ['name' => 'Laporan Tahunan', 'href' => route('absensi.rekap-tahunan')],
                ]" />
              
                <p class="text-gray-500 dark:text-gray-400">
                    Rekapitulasi kehadiran siswa dalam satu tahun akademik.
                </p>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter & Pencarian
                </h3>
            </div>
            <form action="{{ route('absensi.rekap-tahunan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
                {{-- Kelas --}}
                <div class="lg:col-span-2">
                    <label for="kelas_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kelas</label>
                    <select id="kelas_id" name="kelas_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $lk)
                            <option value="{{ $lk->id }}" @selected($kelasId == $lk->id)>{{ $lk->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="lg:col-span-1">
                    <label for="year" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tahun</label>
                    <select id="year" name="year"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach($years as $y)
                            <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Type Guru --}}
                <div class="lg:col-span-1">
                    <label for="type_guru" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Jenis Laporan</label>
                    <select id="type_guru" name="type_guru"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="mapel" @selected(($typeGuru ?? 'mapel') == 'mapel')>Guru Mata Pelajaran</option>
                        <option value="piket" @selected(($typeGuru ?? 'mapel') == 'piket')>Guru Piket</option>
                    </select>
                </div>

                {{-- Mode Tampilan --}}
                <div class="lg:col-span-1">
                    <label for="view_mode" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mode Tampilan</label>
                    <select id="view_mode" name="view_mode"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="detail_harian" @selected($viewMode == 'detail_harian')>Detail Harian (Sesi)</option>
                        <option value="detail" @selected($viewMode == 'detail')>Detail (Rincian Absensi)</option>
                    </select>
                </div>

                {{-- Buttons Group --}}
                <div class="lg:col-span-1 flex gap-2">
                     <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 w-full dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-shadow">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                        Tampilkan
                    </button>
                    
                    @if($selectedKelas)
                    <button id="dropdownExportButton" data-dropdown-toggle="dropdownExport" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center justify-center shadow-sm" type="button" title="Export Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div id="dropdownExport" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownExportButton">
                          <li>
                            <a href="{{ route('absensi.rekap-tahunan.export', ['format' => 'excel', 'type_guru' => $typeGuru ?? 'mapel'] + request()->except(['format', 'type_guru'])) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                                    Export Excel
                                </span>
                            </a>
                          </li>
                          <li>
                            <a href="{{ route('absensi.rekap-tahunan.export', ['format' => 'pdf', 'type_guru' => $typeGuru ?? 'mapel'] + request()->except(['format', 'type_guru'])) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                 <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak PDF
                                </span>
                            </a>
                          </li>
                        </ul>
                    </div>
                    @endif
                </div>
            </form>
        </div>

        @if($selectedKelas)
            {{-- Stats Cards --}}
             <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                 {{-- Total --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Siswa</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Total'] }}</h3>
                    </div>
                </div>
                 {{-- Hadir (Blue) --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Hadir</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Hadir'] }}</h3>
                    </div>
                </div>
                 {{-- Sakit (Orange) --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-orange-600 bg-orange-100 rounded-lg dark:bg-orange-900/30 dark:text-orange-300">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Sakit</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Sakit'] }}</h3>
                    </div>
                </div>
                 {{-- Izin (Yellow) --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Izin</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Izin'] }}</h3>
                    </div>
                </div>
                 {{-- Alpha (Red) --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-red-600 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-300">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Alpha</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Alpha'] }}</h3>
                    </div>
                </div>
            </div>

            @if($viewMode == 'detail')
                 {{-- Mode Detail: List Rincian Absensi per Siswa --}}
                 <div class="relative overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                     <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                         <h3 class="font-bold text-gray-900 dark:text-white">Rincian Ketidakhadiran Tahunan</h3>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left">Nama Siswa</th>
                                    <th scope="col" class="px-4 py-4 w-24">Total Sakit</th>
                                    <th scope="col" class="px-4 py-4 w-24">Total Izin</th>
                                    <th scope="col" class="px-4 py-4 w-24">Total Alpha</th>
                                    <th scope="col" class="px-6 py-4 text-left">Log Ketidakhadiran (Bulan & Tanggal)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <th scope="row" class="px-6 py-4 text-left font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $student->nama }}
                                        </th>
                                        <td class="px-4 py-4 font-bold text-orange-600">{{ $student->total['S'] ?: '-' }}</td>
                                        <td class="px-4 py-4 font-bold text-yellow-600">{{ $student->total['I'] ?: '-' }}</td>
                                        <td class="px-4 py-4 font-bold text-red-600">{{ $student->total['A'] ?: '-' }}</td>
                                        <td class="px-6 py-4 text-left text-xs">
                                            @if(count($student->details) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($student->details as $log)
                                                        @php
                                                            $bgColor = match($log['status']) {
                                                                'Alpha' => 'bg-red-100 text-red-800 border-red-200',
                                                                'Sakit' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                                'Izin' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                default => 'bg-gray-100'
                                                            };
                                                            $date = \Carbon\Carbon::parse($log['date']);
                                                        @endphp
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full border {{ $bgColor }}">
                                                            <span class="font-bold">{{ $date->format('d M') }}</span>: {{ $log['status'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-300 italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center">Data tidak ditemukan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                     </div>
                 </div>

            @else
                {{-- Mode Detail Harian (Sesi) - Existing View --}}
                <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left w-48 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10 border-r border-gray-200 dark:border-gray-600">Nama Siswa</th>
                                    @foreach($months as $m)
                                        <th scope="col" class="px-2 py-3 border-r border-gray-100 dark:border-gray-700">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('M') }}</th>
                                    @endforeach
                                    <th scope="col" class="px-2 py-3 bg-gray-100 text-gray-900 font-bold border-l-2 border-gray-300">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left sticky left-0 bg-white dark:bg-gray-800 z-10 border-r border-gray-200 dark:border-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        {{ $student->nama }}
                                    </th>
                                    @foreach($months as $m)
                                        @php
                                            $stats = $student->months[$m];
                                            $hasData = ($stats['H'] + $stats['S'] + $stats['I'] + $stats['A']) > 0;
                                        @endphp
                                        <td class="px-2 py-2 text-xs border-r border-gray-50 dark:border-gray-800">
                                            @if($hasData)
                                                <div class="flex flex-col gap-1 items-center">
                                                    @if($stats['H']) <span class="text-blue-600 font-bold">{{ $stats['H'] }}H</span> @endif
                                                    @if($stats['S']) <span class="text-orange-600 font-bold">{{ $stats['S'] }}S</span> @endif
                                                    @if($stats['I']) <span class="text-yellow-600 font-bold">{{ $stats['I'] }}I</span> @endif
                                                    @if($stats['A']) <span class="text-red-600 font-bold">{{ $stats['A'] }}A</span> @endif
                                                </div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2 bg-gray-50 text-center border-l-2 border-gray-200">
                                        <div class="flex flex-col gap-1 text-xs">
                                             <span class="text-green-700 font-bold">{{ $student->total['H'] }} H</span>
                                             <span class="text-red-700 font-bold">{{ $student->total['A'] }} A</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="14" class="px-6 py-4 text-center">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-layout.layout>
