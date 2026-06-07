<x-layout.layout>   

    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <x-breadcrumb :title="'Laporan Presensi Periode'" :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Rekapitulasi', 'href' => '#'],
                    ['name' => 'Laporan Presensi Periode', 'href' => route('absensi.rekap-periode')],
                ]" />
                <p class="text-gray-500 dark:text-gray-400">
                    Rekapitulasi kehadiran siswa dalam rentang tanggal tertentu.
                </p>
            </div>
            
            {{-- Filter Section --}}
            <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700 mb-4">
                <form method="GET" action="{{ route('absensi.rekap-periode') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 w-full items-end">
                    {{-- Kelas --}}
                    <div class="lg:col-span-1">
                        <label for="kelas_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Pilih Kelas</label>
                        <select id="kelas_id" name="kelas_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" disabled selected>-- Pilih Kelas --</option>
                            @foreach($listKelas as $k)
                                <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Awal --}}
                    <div class="lg:col-span-1">
                        <x-form.input type="date" id="start_date" name="start_date" label="Tanggal Awal" :value="$startDate" />
                    </div>

                    {{-- Tanggal Akhir --}}
                    <div class="lg:col-span-1">
                        <x-form.input type="date" id="end_date" name="end_date" label="Tanggal Akhir" :value="$endDate" />
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
                            <option value="ringkasan" @selected($viewMode == 'ringkasan')>Ringkasan (Sesi)</option>
                            <option value="ringkasan_harian" @selected($viewMode == 'ringkasan_harian')>Ringkasan (Harian)</option>
                            <option value="detail" @selected($viewMode == 'detail')>Rincian (Log Absensi)</option>
                            <option value="detail_harian" @selected($viewMode == 'detail_harian')>Detail Harian (Status)</option>
                            <option value="detail_harian_sesi" @selected($viewMode == 'detail_harian_sesi')>Detail Harian (Sesi)</option>
                        </select>
                    </div>

                    {{-- Buttons Group --}}
                    <div class="lg:col-span-1 flex gap-2">
                         <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 w-full dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-shadow">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Tampilkan
                        </button>
                        
                        @if($kelasId)
                        <a href="{{ route('absensi.export-periode', ['kelas_id' => $kelasId, 'start_date' => $startDate, 'end_date' => $endDate, 'type_guru' => $typeGuru ?? 'mapel']) }}" target="_blank" title="Export Excel" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2.5 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 flex items-center justify-center shadow-sm">
                             <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-4">
        @if(!$kelasId)
            <div class="flex items-center p-4 mb-4 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">Info!</span> Silakan pilih kelas dan rentang tanggal terlebih dahulu untuk menampilkan data.
                </div>
            </div>
        @else
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4 mb-6">
                <!-- Total Siswa -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                         <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Siswa</h3>
                         <span class="p-2 text-blue-500 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                         </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Total'] }}</span>
                </div>
                <!-- Hadir -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Hadir</h3>
                        <span class="p-2 text-green-500 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Hadir'] }}</span>
                </div>
                 <!-- Sakit -->
                 <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Sakit</h3>
                        <span class="p-2 text-orange-500 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Sakit'] }}</span>
                </div>
                <!-- Izin -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Izin</h3>
                        <span class="p-2 text-yellow-500 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Izin'] }}</span>
                </div>
                <!-- Alpha -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Alpha</h3>
                        <span class="p-2 text-red-500 rounded-lg bg-red-50 dark:bg-red-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Alpha'] }}</span>
                </div>
                <!-- Pulang -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Pulang</h3>
                        <span class="p-2 text-purple-500 rounded-lg bg-purple-50 dark:bg-purple-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Pulang'] ?? 0 }}</span>
                </div>
                <!-- Telat -->
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Telat</h3>
                        <span class="p-2 text-indigo-500 rounded-lg bg-indigo-50 dark:bg-indigo-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold leading-none text-gray-900 sm:text-3xl dark:text-white">{{ $summaryStats['Telat'] ?? 0 }}</span>
                </div>
            </div>

            @if($viewMode == 'ringkasan' || $viewMode == 'sederhana')
                {{-- Mode Ringkasan (Sesi) --}}
                <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                         <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Ringkasan Berdasarkan Sesi Mata Pelajaran</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left w-64">Nama Siswa</th>
                                    <th scope="col" class="px-4 py-3 text-blue-700">Hadir</th>
                                    <th scope="col" class="px-4 py-3 text-orange-700">Sakit</th>
                                    <th scope="col" class="px-4 py-3 text-yellow-700">Izin</th>
                                    <th scope="col" class="px-4 py-3 text-red-700">Alpha</th>
                                    <th scope="col" class="px-4 py-3 text-purple-700">Pulang</th>
                                    <th scope="col" class="px-4 py-3 text-indigo-700">Telat</th>
                                    <th scope="col" class="px-4 py-3">Total Absen (S/I/A/P/T)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">
                                        {{ $student->nama }}
                                    </th>
                                    <td class="px-4 py-4 font-bold text-blue-600">{{ $student->total['H'] }}</td>
                                    <td class="px-4 py-4 font-bold text-orange-600">{{ $student->total['S'] }}</td>
                                    <td class="px-4 py-4 font-bold text-yellow-600">{{ $student->total['I'] }}</td>
                                    <td class="px-4 py-4 font-bold text-red-600">{{ $student->total['A'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-purple-600">{{ $student->total['P'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-indigo-600">{{ $student->total['T'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-800 dark:text-gray-200">
                                        {{ ($student->total['S'] ?? 0) + ($student->total['I'] ?? 0) + ($student->total['A'] ?? 0) + ($student->total['P'] ?? 0) + ($student->total['T'] ?? 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center">Data tidak ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($viewMode == 'ringkasan_harian')
                {{-- Mode Ringkasan (Harian) --}}
                <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                         <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Ringkasan Berdasarkan Jumlah Hari</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left w-64">Nama Siswa</th>
                                    <th scope="col" class="px-4 py-3 text-blue-700">Hari Hadir</th>
                                    <th scope="col" class="px-4 py-3 text-orange-700">Hari Sakit</th>
                                    <th scope="col" class="px-4 py-3 text-yellow-700">Hari Izin</th>
                                    <th scope="col" class="px-4 py-3 text-red-700">Hari Alpha</th>
                                    <th scope="col" class="px-4 py-3 text-purple-700">Hari Pulang</th>
                                    <th scope="col" class="px-4 py-3 text-indigo-700">Hari Telat</th>
                                    <th scope="col" class="px-4 py-3">Total Hari Absen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left">
                                        {{ $student->nama }}
                                    </th>
                                    <td class="px-4 py-4 font-bold text-blue-600">{{ $student->daily_total['H'] }}</td>
                                    <td class="px-4 py-4 font-bold text-orange-600">{{ $student->daily_total['S'] }}</td>
                                    <td class="px-4 py-4 font-bold text-yellow-600">{{ $student->daily_total['I'] }}</td>
                                    <td class="px-4 py-4 font-bold text-red-600">{{ $student->daily_total['A'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-purple-600">{{ $student->daily_total['P'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-indigo-600">{{ $student->daily_total['T'] ?? 0 }}</td>
                                    <td class="px-4 py-4 font-bold text-gray-800 dark:text-gray-200">
                                        {{ ($student->daily_total['S'] ?? 0) + ($student->daily_total['I'] ?? 0) + ($student->daily_total['A'] ?? 0) + ($student->daily_total['P'] ?? 0) + ($student->daily_total['T'] ?? 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center">Data tidak ditemukan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($viewMode == 'detail_harian' || $viewMode == 'detail_harian_sesi')
                 {{-- Mode Detail Harian (Status & Sesi) --}}
                 <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left sticky left-0 bg-gray-50 dark:bg-gray-700 z-10 w-48 border-r dark:border-gray-600">Nama Siswa</th>
                                    @foreach($dates as $dateStr)
                                        @php
                                            $date = \Carbon\Carbon::parse($dateStr);
                                            $isSunday = $date->dayOfWeek == 0;
                                        @endphp
                                        <th scope="col" class="px-2 py-3 min-w-[40px] {{ $isSunday ? 'text-red-500' : '' }}">
                                            {{ $date->format('d') }}<br>
                                            <span class="text-[10px]">{{ $date->format('M') }}</span>
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-2 py-3 w-16 bg-blue-50 dark:bg-blue-900/10 text-blue-700 dark:text-blue-400 border-l dark:border-gray-600">H</th>
                                    <th scope="col" class="px-2 py-3 w-16 bg-orange-50 dark:bg-orange-900/10 text-orange-700 dark:text-orange-400">S</th>
                                    <th scope="col" class="px-2 py-3 w-16 bg-yellow-50 dark:bg-yellow-900/10 text-yellow-700 dark:text-yellow-400">I</th>
                                    <th scope="col" class="px-2 py-3 w-16 bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400">A</th>
                                    <th scope="col" class="px-2 py-3 w-16 bg-purple-50 dark:bg-purple-900/10 text-purple-700 dark:text-purple-400">P</th>
                                    <th scope="col" class="px-2 py-3 w-16 bg-indigo-50 dark:bg-indigo-900/10 text-indigo-700 dark:text-indigo-400">T</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white text-left sticky left-0 bg-white dark:bg-gray-800 z-10 border-r dark:border-gray-600">
                                            {{ $student->nama }}
                                        </th>
                                        
                                        @foreach($dates as $dateStr)
                                            @php
                                                $log = $student->daily_logs[$dateStr] ?? ['code' => '-', 'is_libur' => false, 'counts' => []];
                                                $code = $log['code'];
                                                $isLibur = $log['is_libur'];
                                                
                                                // Determine Cell Color
                                                $cellClass = '';
                                                $textClass = 'text-gray-300';
                                                
                                                if ($code == 'H') { $cellClass = 'bg-blue-50 dark:bg-blue-900/20'; $textClass = 'text-blue-600 font-bold'; }
                                                elseif ($code == 'S') { $cellClass = 'bg-orange-50 dark:bg-orange-900/20'; $textClass = 'text-orange-600 font-bold'; }
                                                elseif ($code == 'I') { $cellClass = 'bg-yellow-50 dark:bg-yellow-900/20'; $textClass = 'text-yellow-600 font-bold'; }
                                                elseif ($code == 'A') { $cellClass = 'bg-red-50 dark:bg-red-900/20'; $textClass = 'text-red-600 font-bold'; }
                                                elseif ($code == 'P') { $cellClass = 'bg-purple-50 dark:bg-purple-900/20'; $textClass = 'text-purple-600 font-bold'; }
                                                elseif ($code == 'T') { $cellClass = 'bg-indigo-50 dark:bg-indigo-900/20'; $textClass = 'text-indigo-600 font-bold'; }
                                                elseif ($code == 'L' || $isLibur) { $cellClass = 'bg-gray-200 dark:bg-gray-600'; $textClass = 'text-gray-500'; $code = ''; }
                                                
                                                // Prepare Content
                                                $content = $code;
                                                if ($viewMode == 'detail_harian_sesi' && isset($log['counts'])) {
                                                     $counts = $log['counts'];
                                                     $summary = [];
                                                     if (($counts['H'] ?? 0) > 0) $summary[] = $counts['H'] . 'H';
                                                     if (($counts['S'] ?? 0) > 0) $summary[] = $counts['S'] . 'S';
                                                     if (($counts['I'] ?? 0) > 0) $summary[] = $counts['I'] . 'I';
                                                     if (($counts['A'] ?? 0) > 0) $summary[] = $counts['A'] . 'A';
                                                     if (($counts['P'] ?? 0) > 0) $summary[] = $counts['P'] . 'P';
                                                     if (($counts['T'] ?? 0) > 0) $summary[] = $counts['T'] . 'T';
                                                     
                                                     if (!empty($summary)) {
                                                         $content = implode(' ', $summary);
                                                         $textClass .= ' text-[10px]'; // Smaller font for session details
                                                     }
                                                }
                                            @endphp
                                            <td class="px-1 py-2 text-center {{ $cellClass }} {{ $textClass }} border-r border-gray-100 dark:border-gray-700 last:border-0">
                                                {{ $content }}
                                            </td>
                                        @endforeach

                                        {{-- Totals --}}
                                        <td class="px-2 py-2 font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/10 border-l dark:border-gray-600">{{ $student->total['H'] ?? 0 }}</td>
                                        <td class="px-2 py-2 font-bold text-orange-600 bg-orange-50 dark:bg-orange-900/10">{{ $student->total['S'] ?? 0 }}</td>
                                        <td class="px-2 py-2 font-bold text-yellow-600 bg-yellow-50 dark:bg-yellow-900/10">{{ $student->total['I'] ?? 0 }}</td>
                                        <td class="px-2 py-2 font-bold text-red-600 bg-red-50 dark:bg-red-900/10">{{ $student->total['A'] ?? 0 }}</td>
                                        <td class="px-2 py-2 font-bold text-purple-600 bg-purple-50 dark:bg-purple-900/10">{{ $student->total['P'] ?? 0 }}</td>
                                        <td class="px-2 py-2 font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/10">{{ $student->total['T'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="{{ count($dates) + 7 }}" class="px-6 py-4 text-center">Data tidak ditemukan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                 </div>

             @else
                  {{-- Mode Detail: List Rincian Absensi (Grouped by Mapel) --}}
                  <div class="relative overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
                          <h3 class="font-bold text-gray-900 dark:text-white">Rincian Ketidakhadiran Berdasarkan Mata Pelajaran</h3>
                          <span class="text-xs text-gray-500 dark:text-gray-400 italic">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                      </div>
                      <div class="overflow-x-auto">
                         <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                             <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                 <tr>
                                     <th scope="col" class="px-6 py-4 w-64">Nama Siswa</th>
                                     <th scope="col" class="px-6 py-4">Daftar Ketidakhadiran (Mapel : Status & Tanggal)</th>
                                 </tr>
                             </thead>
                             <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                 @forelse($rekapData as $student)
                                     <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700">
                                         <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white align-top">
                                             {{ $student->nama }}
                                         </th>
                                         <td class="px-6 py-4">
                                             @if(count($student->details) > 0)
                                                 <div class="space-y-3">
                                                     @php
                                                         $groupedDetails = collect($student->details)->groupBy('mapel');
                                                     @endphp
                                                     
                                                     @foreach($groupedDetails as $mapelName => $mapelLogs)
                                                         <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4">
                                                             <span class="text-xs font-bold text-gray-700 dark:text-gray-300 min-w-[150px]">{{ $mapelName }} :</span>
                                                             <div class="flex flex-wrap gap-2">
                                                                 @php
                                                                     $statusLogs = $mapelLogs->groupBy('status');
                                                                 @endphp
                                                                 @foreach($statusLogs as $status => $logs)
                                                                     @php
                                                                         $badgeColor = match($status) {
                                                                             'Alpha' => 'bg-red-100 text-red-800 border-red-200',
                                                                             'Sakit' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                                             'Izin' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                                             'Pulang' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                                             'Telat' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                                             default => 'bg-gray-100'
                                                                         };
                                                                     @endphp
                                                                     <div class="flex items-center">
                                                                         <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $badgeColor }}">
                                                                             {{ $status }} ({{ $logs->map(fn($l) => \Carbon\Carbon::parse($l['date'])->format('d M'))->join(', ') }})
                                                                         </span>
                                                                     </div>
                                                                 @endforeach
                                                             </div>
                                                         </div>
                                                     @endforeach
                                                 </div>
                                             @else
                                                 <span class="text-gray-400 italic text-xs">Tidak ada data ketidakhadiran</span>
                                             @endif
                                         </td>
                                     </tr>
                                 @empty
                                     <tr><td colspan="2" class="px-6 py-4 text-center">Data tidak ditemukan</td></tr>
                                 @endforelse
                             </tbody>
                         </table>
                      </div>
                  </div>
             @endif
        @endif
    </div>
</x-layout.layout>
