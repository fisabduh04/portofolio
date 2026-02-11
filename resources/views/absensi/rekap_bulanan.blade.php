<x-layout.layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :title="'Laporan Presensi Bulanan'" :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Rekapitulasi', 'href' => '#'],
                    ['name' => 'Laporan Presensi Bulanan', 'href' => route('absensi.rekap-bulanan')],
                ]" />
               
                <p class="text-gray-500 dark:text-gray-400">
                    Rekapitulasi kehadiran siswa dalam satu bulan penuh.
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
            <form action="{{ route('absensi.rekap-bulanan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4 items-end">
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

                {{-- Bulan --}}
                <div class="lg:col-span-1">
                    <label for="month" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bulan</label>
                    <select id="month" name="month"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach($months as $k => $v)
                            <option value="{{ $k }}" @selected($month == $k)>{{ $v }}</option>
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
                        <option value="sederhana" @selected($viewMode == 'sederhana')>Sederhana (Matrix)</option>
                        <option value="detail" @selected($viewMode == 'detail')>Detail (Rincian Absensi)</option>
                        <option value="detail_harian" @selected($viewMode == 'detail_harian')>Detail Harian (Sesi)</option>
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
                            <a href="{{ route('absensi.rekap-bulanan.export', ['format' => 'excel', 'type_guru' => $typeGuru ?? 'mapel'] + request()->except(['format', 'type_guru'])) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                                    Export Excel
                                </span>
                            </a>
                          </li>
                          <li>
                            <a href="{{ route('absensi.rekap-bulanan.export', ['format' => 'pdf', 'type_guru' => $typeGuru ?? 'mapel'] + request()->except(['format', 'type_guru'])) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
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
                 {{-- Hadir --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Hadir</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Hadir'] }}</h3>
                    </div>
                </div>
                 {{-- Sakit --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-orange-600 bg-orange-100 rounded-lg dark:bg-orange-900/30 dark:text-orange-300">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Sakit</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Sakit'] }}</h3>
                    </div>
                </div>
                 {{-- Izin --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                     <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">Izin</p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Izin'] }}</h3>
                    </div>
                </div>
                 {{-- Alpha --}}
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

            @if($viewMode == 'sederhana')
                {{-- Legend (Simple Mode) --}}
                <div class="flex flex-wrap items-center justify-end gap-4 mb-2 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Hadir</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span> Sakit</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span> Izin</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Alpha</div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-200 border border-gray-300"></span> Libur/Kosong</div>
                </div>

                {{-- Table (Simple Mode - Dot Matrix) --}}
                <div class="relative overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400 border-collapse">
                            <thead class="text-xs text-center text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-20 shadow-sm">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left sticky left-0 bg-gray-100 dark:bg-gray-700 z-30 w-48 border-r border-gray-200 dark:border-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        Nama Siswa
                                    </th>
                                    @foreach($dates as $d)
                                        <th scope="col" class="px-0.5 py-3 w-8 min-w-[32px] font-medium border-r border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-default" title="Tanggal {{ $d }}">
                                            {{ $d }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-2 py-3 bg-gray-100 dark:bg-gray-800 border-l-2 border-gray-300 dark:border-gray-600 font-bold w-10 text-blue-700">H</th>
                                    <th scope="col" class="px-2 py-3 bg-gray-100 dark:bg-gray-800 font-bold w-10 text-orange-700">S</th>
                                    <th scope="col" class="px-2 py-3 bg-gray-100 dark:bg-gray-800 font-bold w-10 text-yellow-700">I</th>
                                    <th scope="col" class="px-2 py-3 bg-gray-100 dark:bg-gray-800 font-bold w-10 text-red-700">A</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                                    <th scope="row" class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white sticky left-0 bg-white dark:bg-gray-800 z-20 border-r border-gray-200 dark:border-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] group-hover:bg-slate-50 dark:group-hover:bg-gray-700">
                                        {{ $student->nama }}
                                    </th>
                                    @foreach($dates as $d)
                                        @php
                                            $dayData = $student->statuses[$d] ?? ['code' => '-', 'is_libur' => false];
                                            $status = $dayData['code'];
                                            $dotClass = 'bg-gray-100 border border-gray-200'; // Default empty
                                            $tooltipText = "Tgl $d: Tidak Ada Data";
                                            
                                            if($dayData['is_libur']) {
                                                $dotClass = 'bg-gray-300 border-gray-400 opacity-50';
                                                $tooltipText = "Tgl $d: Libur";
                                            }

                                            if($status == 'H') {
                                                $dotClass = 'bg-blue-500 border-blue-500 shadow-sm shadow-blue-200'; 
                                                $tooltipText = "Tgl $d: Hadir";
                                            }
                                            elseif($status == 'S') {
                                                $dotClass = 'bg-orange-400 border-orange-400 shadow-sm shadow-orange-200'; 
                                                $tooltipText = "Tgl $d: Sakit";
                                            }
                                            elseif($status == 'I') {
                                                $dotClass = 'bg-yellow-400 border-yellow-400 shadow-sm shadow-yellow-200'; 
                                                $tooltipText = "Tgl $d: Izin";
                                            }
                                            elseif($status == 'A') {
                                                $dotClass = 'bg-red-500 border-red-500 shadow-sm shadow-red-200 animate-pulse'; 
                                                $tooltipText = "Tgl $d: Alpha";
                                            }
                                        @endphp
                                        <td class="px-0.5 py-2 text-center relative group/cell border-r border-gray-50 dark:border-gray-800 {{ $dayData['is_libur'] ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                                            <div class="flex items-center justify-center">
                                                <div class="w-3 h-3 rounded-full {{ $dotClass }} transition-transform hover:scale-150 cursor-pointer" title="{{ $tooltipText }}"></div>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-2 text-center font-bold text-blue-700 dark:text-blue-400 bg-gray-50 dark:bg-gray-800 border-l-2 border-gray-200 dark:border-gray-700">{{ $student->stats['H'] }}</td>
                                    <td class="px-2 py-2 text-center font-bold text-orange-700 dark:text-orange-400 bg-gray-50 dark:bg-gray-800">{{ $student->stats['S'] }}</td>
                                    <td class="px-2 py-2 text-center font-bold text-yellow-700 dark:text-yellow-400 bg-gray-50 dark:bg-gray-800">{{ $student->stats['I'] }}</td>
                                    <td class="px-2 py-2 text-center font-bold text-red-700 dark:text-red-400 bg-gray-50 dark:bg-gray-800">{{ $student->stats['A'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ count($dates) + 5 }}" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>Data presensi tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif($viewMode == 'detail_harian')
                {{-- Detail Harian (Sesi Count) View --}}
                <div class="relative overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400 border-collapse">
                            <thead class="text-xs text-center text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-20 shadow-sm">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left sticky left-0 bg-gray-100 dark:bg-gray-700 z-30 w-48 border-r border-gray-200 dark:border-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        Nama Siswa
                                    </th>
                                    @foreach($dates as $d)
                                        <th scope="col" class="px-1 py-3 min-w-[60px] font-medium border-r border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                                            {{ $d }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-2 py-3 bg-blue-50 dark:bg-blue-900/20 border-l-2 border-gray-300 dark:border-gray-600 font-bold w-12 text-blue-700">H</th>
                                    <th scope="col" class="px-2 py-3 bg-red-50 dark:bg-red-900/20 font-bold w-12 text-red-700">A</th>
                                    <th scope="col" class="px-2 py-3 bg-gray-100 dark:bg-gray-800 font-bold w-12 text-gray-700">T</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                <tr class="bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                                    <th scope="row" class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white sticky left-0 bg-white dark:bg-gray-800 z-20 border-r border-gray-200 dark:border-gray-600 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        {{ $student->nama }}
                                    </th>
                                    @foreach($dates as $d)
                                        @php
                                            $dayData = $student->statuses[$d] ?? ['counts' => [], 'is_libur' => false];
                                            $counts = $dayData['counts'];
                                            $isLibur = $dayData['is_libur'];
                                            $hasData = array_sum($counts) > 0;
                                        @endphp
                                        <td class="px-1 py-2 text-center text-[10px] sm:text-xs border-r border-gray-100 dark:border-gray-700 {{ $isLibur ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                                            @if($isLibur && !$hasData)
                                                <span class="text-gray-400 font-bold">L</span>
                                            @elseif($hasData)
                                                <div class="flex flex-col items-center gap-0.5 leading-none">
                                                    @if($counts['H'] > 0) <span class="text-blue-600 font-bold whitespace-nowrap">{{ $counts['H'] }}H</span> @endif
                                                    @if($counts['A'] > 0) <span class="text-red-600 font-bold whitespace-nowrap">{{ $counts['A'] }}A</span> @endif
                                                    @if($counts['S'] > 0) <span class="text-orange-600 font-bold whitespace-nowrap">{{ $counts['S'] }}S</span> @endif
                                                    @if($counts['I'] > 0) <span class="text-yellow-600 font-bold whitespace-nowrap">{{ $counts['I'] }}I</span> @endif
                                                </div>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    {{-- Total Columns --}}
                                    <td class="px-2 py-2 text-center font-bold text-blue-700 bg-blue-50 dark:bg-blue-900/20 border-l-2 border-gray-200">{{ $student->stats['H'] }}</td>
                                    <td class="px-2 py-2 text-center font-bold text-red-700 bg-red-50 dark:bg-red-900/20">{{ $student->stats['A'] }}</td>
                                    <td class="px-2 py-2 text-center font-bold text-gray-700 bg-gray-100 dark:bg-gray-800">{{ array_sum($student->stats) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ count($dates) + 4 }}" class="px-6 py-8 text-center text-gray-500">
                                        Data tidak tersedia
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- Detail (Detail Mode - Rincian Absensi Existing) --}}
                <div class="relative overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">Rincian Ketidakhadiran Siswa</h3>
                            <p class="text-xs text-gray-500">Menampilkan detail tanggal dan alasan ketidakhadiran.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                             <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 text-center">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left font-semibold tracking-wider">Siswa</th>
                                    <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20 text-blue-700">Hadir</th>
                                    <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20 text-orange-700">Sakit</th>
                                    <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20 text-yellow-700">Izin</th>
                                    <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20 text-red-700">Alpha</th>
                                    <th scope="col" class="px-6 py-4 text-left font-semibold tracking-wider">Detail Ketidakhadiran (Tanggal)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($rekapData as $student)
                                    <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200">
                                         <th scope="row" class="px-6 py-4 text-left font-medium text-gray-900 whitespace-nowrap dark:text-white uppercase">
                                            {{ $student->nama }}
                                        </th>
                                        <td class="px-4 py-4 text-center font-bold text-blue-700">{{ $student->stats['H'] > 0 ? $student->stats['H'] : '-' }}</td>
                                        <td class="px-4 py-4 text-center font-bold text-orange-700">{{ $student->stats['S'] > 0 ? $student->stats['S'] : '-' }}</td>
                                        <td class="px-4 py-4 text-center font-bold text-yellow-700">{{ $student->stats['I'] > 0 ? $student->stats['I'] : '-' }}</td>
                                        <td class="px-4 py-4 text-center font-bold text-red-700">{{ $student->stats['A'] > 0 ? $student->stats['A'] : '-' }}</td>
                                        <td class="px-6 py-4 text-left text-xs">
                                            @php
                                                $absentDates = [];
                                                foreach($student->statuses as $date => $data) {
                                                    $status = $data['code']; // Access code from array
                                                    if(in_array($status, ['S', 'I', 'A'])) {
                                                        $absentDates[] = ['date' => $date, 'status' => $status];
                                                    }
                                                }
                                            @endphp
                                            
                                            @if(count($absentDates) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($absentDates as $log)
                                                        @php
                                                            $badgeColor = 'bg-gray-100 text-gray-800';
                                                            if($log['status'] == 'S') $badgeColor = 'bg-orange-100 text-orange-800 border-orange-200';
                                                            elseif($log['status'] == 'I') $badgeColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                                            elseif($log['status'] == 'A') $badgeColor = 'bg-red-100 text-red-800 border-red-200';
                                                        @endphp
                                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-medium rounded-full border {{ $badgeColor }}">
                                                            <span class="font-bold">{{ $log['date'] }} {{ $months[$month] }}</span>: {{ $log['status'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-300 italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada data siswa.
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
