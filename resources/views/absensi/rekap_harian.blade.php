<x-layout.layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :title="'Rekapitulasi Presensi Harian'" :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Rekapitulasi', 'href' => '#'],
                    ['name' => 'Presensi Harian', 'href' => route('absensi.rekap-harian')],
                ]" />
                <p class="text-gray-500 dark:text-gray-400">
                    Monitoring kehadiran siswa dengan tampilan timeline visual.
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
            <form action="{{ route('absensi.rekap-harian') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                {{-- Tanggal --}}
                <div class="lg:col-span-1">
                    <label for="date" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input type="date" name="date" id="date" value="{{ $date }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="lg:col-span-1">
                    <label for="kelas_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kelas</label>
                    <select id="kelas_id" name="kelas_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $lk)
                            <option value="{{ $lk->id }}" @selected($kelasId == $lk->id)>{{ $lk->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipe Guru --}}
                <div class="lg:col-span-1">
                    <label for="type_guru" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipe Guru</label>
                    <select id="type_guru" name="type_guru"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="mapel" @selected($typeGuru == 'mapel')>Guru Mata Pelajaran</option>
                        <option value="piket" @selected($typeGuru == 'piket')>Guru Piket Harian</option>
                    </select>
                </div>

                {{-- Guru (Opsional) --}}
                <div class="lg:col-span-1">
                    <label for="pegawai_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Guru</label>
                    <select id="pegawai_id" name="pegawai_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">-- Semua Guru --</option>
                        @foreach($listPegawai as $lp)
                            <option value="{{ $lp->id }}" @selected($pegawaiId == $lp->id)>{{ $lp->name }}</option>
                        @endforeach
                    </select>
                </div>

                 {{-- Tampilan (View Mode) --}}
                 <div class="lg:col-span-1">
                    <label for="view_mode" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mode Tampilan</label>
                    <select id="view_mode" name="view_mode"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="sederhana" @selected($viewMode == 'sederhana')>Sederhana (Visual)</option>
                        <option value="detail" @selected($viewMode == 'detail')>Detail (Statistik Siswa)</option>
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
                            <a href="{{ route('absensi.rekap-harian.export', ['format' => 'excel', 'type_guru' => $typeGuru] + request()->except(['format', 'type_guru'])) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                                    Export Excel
                                </span>
                            </a>
                            </li>
                            <li>
                            <a href="{{ route('absensi.rekap-harian.export', ['format' => 'pdf', 'type_guru' => $typeGuru] + request()->except(['format', 'type_guru'])) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
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
            {{-- Statistik Cards (Modern Design) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Total Siswa --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Total Siswa
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Total'] }}</h3>
                    </div>
                </div>
                
                {{-- Hadir --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-green-600 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Hadir
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Hadir'] }}</h3>
                    </div>
                </div>

                {{-- Sakit --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Sakit
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Sakit'] }}</h3>
                    </div>
                </div>

                {{-- Izin --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Izin
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Izin'] }}</h3>
                    </div>
                </div>

                {{-- Alpha --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-red-600 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Alpha
<x-layout.layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :title="'Rekapitulasi Presensi Harian'" :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Rekapitulasi', 'href' => '#'],
                    ['name' => 'Presensi Harian', 'href' => route('absensi.rekap-harian')],
                ]" />
                <p class="text-gray-500 dark:text-gray-400">
                    Monitoring kehadiran siswa dengan tampilan timeline visual.
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
            <form action="{{ route('absensi.rekap-harian') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                {{-- Tanggal --}}
                <div class="lg:col-span-1">
                    <label for="date" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input type="date" name="date" id="date" value="{{ $date }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="lg:col-span-1">
                    <label for="kelas_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Kelas</label>
                    <select id="kelas_id" name="kelas_id" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($listKelas as $lk)
                            <option value="{{ $lk->id }}" @selected($kelasId == $lk->id)>{{ $lk->kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipe Guru --}}
                <div class="lg:col-span-1">
                    <label for="type_guru" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Tipe Guru</label>
                    <select id="type_guru" name="type_guru"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="mapel" @selected($typeGuru == 'mapel')>Guru Mata Pelajaran</option>
                        <option value="piket" @selected($typeGuru == 'piket')>Guru Piket Harian</option>
                    </select>
                </div>

                {{-- Guru (Opsional) --}}
                <div class="lg:col-span-1">
                    <label for="pegawai_id" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Guru</label>
                    <select id="pegawai_id" name="pegawai_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">-- Semua Guru --</option>
                        @foreach($listPegawai as $lp)
                            <option value="{{ $lp->id }}" @selected($pegawaiId == $lp->id)>{{ $lp->name }}</option>
                        @endforeach
                    </select>
                </div>

                 {{-- Tampilan (View Mode) --}}
                 <div class="lg:col-span-1">
                    <label for="view_mode" class="block mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Mode Tampilan</label>
                    <select id="view_mode" name="view_mode"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="sederhana" @selected($viewMode == 'sederhana')>Sederhana (Visual)</option>
                        <option value="detail" @selected($viewMode == 'detail')>Detail (Statistik Siswa)</option>
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
                            <a href="{{ route('absensi.rekap-harian.export', ['format' => 'excel', 'type_guru' => $typeGuru] + request()->except(['format', 'type_guru'])) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6z"/></svg>
                                    Export Excel
                                </span>
                            </a>
                            </li>
                            <li>
                            <a href="{{ route('absensi.rekap-harian.export', ['format' => 'pdf', 'type_guru' => $typeGuru] + request()->except(['format', 'type_guru'])) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
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
            {{-- Statistik Cards (Modern Design) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Total Siswa --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Total Siswa
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Total'] }}</h3>
                    </div>
                </div>
                
                {{-- Hadir --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-green-600 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Hadir
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Hadir'] }}</h3>
                    </div>
                </div>

                {{-- Sakit --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Sakit
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Sakit'] }}</h3>
                    </div>
                </div>

                {{-- Izin --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Izin
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Izin'] }}</h3>
                    </div>
                </div>

                {{-- Alpha --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-red-600 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Alpha
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Alpha'] }}</h3>
                    </div>
                </div>

                {{-- Pulang --}}
                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="inline-flex flex-shrink-0 justify-center items-center w-12 h-12 text-purple-600 bg-purple-100 rounded-lg dark:bg-purple-900/30 dark:text-purple-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    <div class="ml-3">
                        <p class="mb-0.5 text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                            Pulang
                        </p>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Pulang'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            {{-- Main Logic Explanation (Dismissible Alert) --}}

            {{-- Info Hari Libur (Major Banner) --}}
            @if($holiday)
                <div class="p-6 mb-6 text-center bg-blue-50 border border-blue-200 rounded-xl dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-4 text-blue-600 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                         <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Hari Libur: {{ $holiday->keterangan }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-lg mx-auto">
                        Pada hari ini kegiatan belajar mengajar diliburkan sesuai jadwal akademik.
                    </p>
                    
                    @if($summaryStats['Hadir'] > 0)
                        <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200 inline-block text-left dark:bg-gray-800 dark:border-gray-700">
                             <p class="text-sm font-medium text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Aktivitas Terdeteksi:
                             </p>
                             <p class="text-xs text-gray-500 dark:text-gray-400">
                                Meskipun libur, terdapat <b>{{ $summaryStats['Hadir'] }} siswa</b> yang tercatat hadir (kegiatan ekstrakurikuler/tambahan).
                                <button type="button" onclick="document.getElementById('main-rekap-content').classList.toggle('hidden')" class="text-blue-600 hover:underline ml-1">Lihat Detail</button>
                             </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Main Content Wrapper --}}
            <div id="main-rekap-content" class="{{ ($holiday && $summaryStats['Hadir'] == 0) ? 'hidden' : '' }}">

            {{-- VIEW CONDITIONAL --}}
            @if($viewMode == 'detail')
                 {{-- MODE DETAIL: TABEL STATISTIK SISWA (Quantifiable) --}}
                 <div class="relative overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                        <h3 class="font-bold text-gray-900 dark:text-white">Rincian Statistik Kehadiran Siswa</h3>
                        <p class="text-xs text-gray-500">Jumlah kehadiran per status (Hadir, Sakit, Izin, Alpha) untuk hari ini.</p>
                    </div>
                     <table class="w-full text-sm text-center text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left font-semibold tracking-wider">Siswa</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Hadir</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Sakit</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Izin</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Alpha</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Pulang</th>
                                <th scope="col" class="px-4 py-4 font-semibold tracking-wider w-20">Telat</th>
                                <th scope="col" class="px-6 py-4 text-left font-semibold tracking-wider">Detail Ketidakhadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($rekapData as $data)
                                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200">
                                     <th scope="row" class="px-6 py-4 text-left font-medium text-gray-900 whitespace-nowrap dark:text-white uppercase">
                                        {{ $data->nama }}
                                    </th>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Hadir'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-green-800 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300 border border-green-200">
                                                {{ $data->stats['Hadir'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Sakit'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-yellow-800 bg-yellow-100 rounded-full dark:bg-yellow-900 dark:text-yellow-300 border border-yellow-200">
                                                {{ $data->stats['Sakit'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Izin'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300 border border-blue-200">
                                                {{ $data->stats['Izin'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Alpha'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-red-800 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300 border border-red-200 animate-pulse">
                                                {{ $data->stats['Alpha'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Pulang'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-purple-800 bg-purple-100 rounded-full dark:bg-purple-900 dark:text-purple-300 border border-purple-200">
                                                {{ $data->stats['Pulang'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($data->stats['Telat'] > 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-indigo-800 bg-indigo-100 rounded-full dark:bg-indigo-900 dark:text-indigo-300 border border-indigo-200">
                                                {{ $data->stats['Telat'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-left text-xs">
                                        @php
                                            $alphaDetails = $data->details->where('status', 'Alpha');
                                            $sakitDetails = $data->details->where('status', 'Sakit');
                                            $izinDetails  = $data->details->where('status', 'Izin');
                                            $pulangDetails = $data->details->where('status', 'Pulang');
                                            $telatDetails = $data->details->where('status', 'Telat');
                                            $hasDetails   = $alphaDetails->count() > 0 || $sakitDetails->count() > 0 || $izinDetails->count() > 0 || $pulangDetails->count() > 0 || $telatDetails->count() > 0;
                                        @endphp

                                        @if($hasDetails)
                                            <div class="flex flex-col gap-2 items-start">
                                                {{-- Alpha --}}
                                                @foreach($alphaDetails as $log)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                                                        <span class="font-bold">{{ $log->mapel }}</span>
                                                        <span class="font-normal opacity-80">({{ $log->guru }})</span>
                                                    </span>
                                                @endforeach

                                                {{-- Sakit --}}
                                                @foreach($sakitDetails as $log)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800">
                                                        <span class="font-bold">{{ $log->mapel }}</span>
                                                        <span class="font-normal opacity-80">({{ $log->guru }})</span>
                                                    </span>
                                                @endforeach

                                                {{-- Izin --}}
                                                @foreach($izinDetails as $log)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                                        <span class="font-bold">{{ $log->mapel }}</span>
                                                        <span class="font-normal opacity-80">({{ $log->guru }})</span>
                                                    </span>
                                                @endforeach

                                                {{-- Pulang --}}
                                                @foreach($pulangDetails as $log)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
                                                        <span class="font-bold">{{ $log->mapel }}</span>
                                                        <span class="font-normal opacity-80">({{ $log->guru }})</span>
                                                    </span>
                                                @endforeach

                                                {{-- Telat --}}
                                                @foreach($telatDetails as $log)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                                                        <span class="font-bold">{{ $log->mapel }}</span>
                                                        <span class="font-normal opacity-80">({{ $log->guru }})</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-300 text-xs italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-white dark:bg-gray-800">
                                        Tidak ada data siswa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else
                {{-- MODE SEDERHANA: VISUAL TIMELINE TABLE (Flowbite v4 Premium Style) --}}
                <div class="relative overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Siswa</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-center w-32">Status</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider">Timeline Aktivitas</th>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wider text-right">Resume</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($rekapData as $data)
                                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors duration-200">
                                    {{-- Nama Siswa --}}
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold">{{ $data->nama }}</span>
                                                @if($data->daily_status == 'Alpha')
                                                    <span class="inline-flex items-center gap-1 mt-0.5 text-[10px] font-bold text-red-600 uppercase tracking-wide">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                                                        Perlu Tindakan
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </th>
                                    
                                     {{-- Status Verdict Badge --}}
                                    <td class="px-6 py-4 text-center">
                                         @php
                                             $displayStatus = $data->daily_status == 'Telat' ? 'Hadir' : $data->daily_status;
                                             $badgeClasses = match($displayStatus) {
                                                 'Hadir' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                                 'Alpha' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                                 'Sakit' => 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:border-orange-800',
                                                 'Izin' => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800',
                                                 'Pulang' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                                 default => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'
                                             };
                                         @endphp
                                         <span class="{{ $badgeClasses }} text-xs font-semibold px-3 py-1 rounded-full border shadow-sm">
                                            {{ $displayStatus }}
                                         </span>
                                    </td>

                                    {{-- Visual Timeline --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @forelse($data->details as $index => $log)
                                                @php
                                                     $sessionColor = match($log->status) {
                                                         'Hadir' => 'bg-blue-500 hover:bg-blue-600 shadow-blue-200',
                                                         'Alpha' => 'bg-red-500 hover:bg-red-600 shadow-red-200',
                                                         'Sakit' => 'bg-orange-400 hover:bg-orange-500 shadow-orange-200',
                                                         'Izin' => 'bg-yellow-400 hover:bg-yellow-500 shadow-yellow-200',
                                                         'Pulang' => 'bg-purple-500 hover:bg-purple-600 shadow-purple-200',
                                                         'Telat' => 'bg-indigo-500 hover:bg-indigo-600 shadow-indigo-200',
                                                         default => 'bg-gray-400'
                                                     };
                                                    // Unique ID for Tooltip
                                                    $tooltipId = 'tooltip-'.$data->id.'-'.$index;
                                                @endphp
                                                
                                                {{-- Interactive Dot/Block --}}
                                                <div data-tooltip-target="{{ $tooltipId }}" class="group relative cursor-pointer"
                                                     onclick="showSessionDetail('{{ addslashes($log->mapel) }}', '{{ addslashes($log->guru) }}', '{{ $log->jam_ke }}', '{{ $log->status }}', '{{ addslashes(str_replace(["\r", "\n"], ' ', $log->catatan ?? '')) }}', '{{ rawurlencode($log->foto ?? '') }}')">
                                                    <div class="h-8 w-2 md:w-8 md:h-8 rounded-md {{ $sessionColor }} flex items-center justify-center text-white text-[10px] font-bold shadow-sm transition-all duration-200 ease-in-out hover:scale-110 hover:shadow-md ring-1 ring-white/20">
                                                        <span class="hidden md:inline">{{ substr($log->status, 0, 1) }}</span>
                                                        @if(isset($log->foto) && !empty(json_decode($log->foto)))
                                                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-400 rounded-full border border-white dark:border-gray-800" title="Ada dokumentasi foto"></span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Tooltip Content --}}
                                                <div id="{{ $tooltipId }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                                    <div class="font-bold border-b border-gray-600 pb-1 mb-1">{{ $log->mapel }}</div>
                                                    <div class="text-xs space-y-0.5">
                                                        <p class="opacity-90">Guru: {{ $log->guru }}</p>
                                                        <p class="opacity-90">Jam: {{ $log->jam_ke }}</p>
                                                        <p>Status: <span class="font-bold {{ $log->status == 'Alpha' ? 'text-red-300' : 'text-blue-300' }}">{{ $log->status }}</span></p>
                                                        @if($log->is_piket_sub)
                                                            <p class="text-purple-300 mt-1 italic text-[10px]">Note: Guru Pengganti</p>
                                                        @endif
                                                        @if(isset($log->foto) && !empty(json_decode($log->foto)))
                                                            <div class="pt-1.5 border-t border-gray-600 mt-1.5 flex items-center gap-1 text-emerald-300 font-semibold text-[10px] uppercase tracking-wider">
                                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 012 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                                {{ count(json_decode($log->foto)) }} Foto Dokumentasi (Klik detail)
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                                </div>
                                            @empty
                                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-md text-gray-400 text-xs dark:bg-gray-700/50 dark:border-gray-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span>Belum ada sesi</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </td>

                                    {{-- Ringkasan Text --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-col items-end gap-1.5 text-xs font-medium">
                                            @if($data->stats['Alpha'] > 0) 
                                                <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded border border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900">
                                                    {{ $data->stats['Alpha'] }} Alpha
                                                </span>
                                            @endif
                                            @if($data->stats['Sakit'] > 0) 
                                                <span class="px-2 py-0.5 bg-orange-50 text-orange-600 rounded border border-orange-100 dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-900">
                                                    {{ $data->stats['Sakit'] }} Sakit
                                                </span>
                                            @endif
                                            @if($data->stats['Izin'] > 0) 
                                                <span class="px-2 py-0.5 bg-yellow-50 text-yellow-600 rounded border border-yellow-100 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-900">
                                                    {{ $data->stats['Izin'] }} Izin
                                                 </span>
                                            @endif
                                            @if($data->stats['Pulang'] > 0) 
                                                <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded border border-purple-100 dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-900">
                                                    {{ $data->stats['Pulang'] }} Pulang
                                                 </span>
                                            @endif
                                            @php
                                                $totalHadir = ($data->stats['Hadir'] ?? 0) + ($data->stats['Telat'] ?? 0);
                                            @endphp
                                            @if($totalHadir > 0)
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900">{{ $totalHadir }} Hadir</span>
                                            @endif
                                            @if(array_sum($data->stats) == 0)
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 bg-white dark:bg-gray-800">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-base font-medium">Tidak ada data ditampilkan</p>
                                            <p class="text-sm text-gray-400">Pilih kelas atau sesuaikan filter di atas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap justify-end gap-x-6 gap-y-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                     <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-blue-500 rounded-sm"></span> Hadir</div>
                     <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-red-500 rounded-sm"></span> Alpha</div>
                     <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-orange-400 rounded-sm"></span> Sakit</div>
                     <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-yellow-400 rounded-sm"></span> Izin</div>
                     <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-purple-500 rounded-sm"></span> Pulang</div>
             if (encodedFoto) {
                 try {
                     const fotoStr = decodeURIComponent(encodedFoto);
                     const fotos = JSON.parse(fotoStr);
                     
                     if (Array.isArray(fotos) && fotos.length > 0) {
                         noPhotos.classList.add('hidden');
                         gallery.classList.remove('hidden');
                         fotos.forEach(foto => {
                             const path = "{{ asset('storage') }}/" + foto;
                             const div = document.createElement('div');
                             div.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 cursor-pointer shadow-sm hover:scale-105 transition-all duration-200 group';
                             div.onclick = function() { openGlobalLightbox(path); };
                             
                             div.innerHTML = `
                                 <img src="${path}" class="w-full h-full object-cover animate-fade-in">
                                 <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                     <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                                 </div>
                             `;
                             gallery.appendChild(div);
                         });
                     } else {
                         gallery.classList.add('hidden');
                         noPhotos.classList.remove('hidden');
                     }
                 } catch (e) {
                     console.error('Failed to parse photos json', e);
                     gallery.classList.add('hidden');
                     noPhotos.classList.remove('hidden');
                 }
             } else {
                 gallery.classList.add('hidden');
                 noPhotos.classList.remove('hidden');
             }

             // Open modal
             const modal = document.getElementById('sessionDetailModal');
             modal.classList.remove('hidden');
             modal.classList.add('flex');
             setTimeout(() => {
                 modal.classList.remove('opacity-0');
                 modal.classList.add('opacity-100');
             }, 10);
         }

         function closeSessionDetailModal() {
             const modal = document.getElementById('sessionDetailModal');
             if (modal) {
                 modal.classList.remove('opacity-100');
                 modal.classList.add('opacity-0');
                 setTimeout(() => {
                     modal.classList.remove('flex');
                     modal.classList.add('hidden');
                 }, 300);
             }
         }

         // Lightbox modal functions
         function openGlobalLightbox(src) {
             const lightbox = document.getElementById('globalLightbox');
             const img = document.getElementById('lightboxImage');
             if (lightbox && img) {
                 img.src = src;
                 lightbox.classList.remove('hidden');
                 lightbox.classList.add('flex');
                 setTimeout(() => {
                     lightbox.classList.remove('opacity-0');
                     lightbox.classList.add('opacity-100');
                 }, 10);
             }
         }

         function closeGlobalLightbox() {
             const lightbox = document.getElementById('globalLightbox');
             if (lightbox) {
                 lightbox.classList.remove('opacity-100');
                 lightbox.classList.add('opacity-0');
                 setTimeout(() => {
                     lightbox.classList.remove('flex');
                     lightbox.classList.add('hidden');
                 }, 300);
             }
         }

         document.addEventListener('keydown', function(e) {
             if (e.key === 'Escape') {
                 closeSessionDetailModal();
                 closeGlobalLightbox();
             }
         });
     </script>
 </x-layout.layout>
