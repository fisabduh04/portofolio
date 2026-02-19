<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Laporan', 'href' => route('attendance.report')],
        ['name' => 'Rekap Per Pegawai', 'href' => '#']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mt-4">
        {{-- Internal Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
             <x-layout.sidebar.sub-menu />
        </div>

        <div class="p-4 sm:p-6">
            <!-- Filter Section -->
            <form action="{{ route('attendance.report.employee') }}" method="GET" class="mb-8">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase mb-4">Filter Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label for="pegawai_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Pegawai</label>
                            <select name="pegawai_id" id="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                                <option value="">-- Pilih Nama Pegawai --</option>
                                @foreach($pegawais as $p)
                                    <option value="{{ $p->id }}" {{ $pegawaiId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
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
                <!-- Report Header -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Rekapitulasi Kehadiran</h2>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Nama: <span class="font-semibold text-gray-900 dark:text-white">{{ $selectedPegawai->nama }}</span> | 
                            Periode: <span class="font-semibold text-gray-900 dark:text-white">{{ $months[$month] }} {{ $year }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-btn type="button" color="green" icon="printer" onclick="window.print()">Cetak</x-btn>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 border-r dark:border-gray-600">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-center border-r dark:border-gray-600">Jam Masuk</th>
                                <th scope="col" class="px-6 py-3 text-center border-r dark:border-gray-600">Jam Pulang</th>
                                <th scope="col" class="px-6 py-3 text-center border-r dark:border-gray-600">Durasi</th>
                                <th scope="col" class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendanceData as $row)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 {{ $row['row_class'] }}">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white border-r dark:border-gray-600">
                                        {{ $row['date'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center border-r dark:border-gray-600">
                                        {{ $row['jam_masuk'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center border-r dark:border-gray-600">
                                        {{ $row['jam_pulang'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center border-r dark:border-gray-600">
                                        {{ $row['durasi'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold">
                                        {{ $row['status'] }}
                                        @if($row['keterangan'])
                                            <div class="text-xs font-normal text-gray-500 mt-1">({{ $row['keterangan'] }})</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-400">
                    <div class="flex items-center"><span class="w-3 h-3 bg-green-50 border border-green-200 mr-1"></span> Hadir</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-yellow-50 border border-yellow-200 mr-1"></span> Telat</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-red-50 border border-red-200 mr-1"></span> Alpha/Terlewat</div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-blue-50 border border-blue-200 mr-1"></span> Event/Kegiatan</div>
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
