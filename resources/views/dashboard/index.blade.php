<x-layout.layout>
    <div class="space-y-6">

        {{-- Header & Breadcrumb --}}
        <div>
            <x-breadcrumb :breadcrumbs="[['name' => 'Home', 'href' => route('dashboard.index')], ['name' => 'Dashboard', 'href' => '#']]" />
            <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Dashboard Presensi</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ringkasan statistik akademik dan presensi sekolah.</p>
        </div>

        {{-- ==============================================
             DATA POKOK SEKOLAH (MASTER DATA)
             ============================================== --}}
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Data Pokok Sekolah</h2>
            <p class="text-sm text-gray-500">Populasi total peserta didik dan tenaga kependidikan.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- Card Total Siswa (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Siswa</h3>
                    <span class="p-2 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalSiswa }}</div>
                <div class="text-sm text-green-500 flex items-center mt-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>Aktif</span>
                </div>
            </div>

            {{-- Card Total Guru/Staf (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Guru/Staf</h3>
                    <span class="p-2 text-purple-600 bg-purple-100 rounded-lg dark:bg-purple-900/30 dark:text-purple-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPegawai }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Terdaftar</div>
            </div>

            {{-- Card Kelas (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rombel (Kelas)</h3>
                    <span class="p-2 text-teal-600 bg-teal-100 rounded-lg dark:bg-teal-900/30 dark:text-teal-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalKelas }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Dalam {{ $totalJurusan }} Jurusan</div>
            </div>

            {{-- Card Pegawai Aktif (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pegawai Aktif</h3>
                    <span class="p-2 text-indigo-600 bg-indigo-100 rounded-lg dark:bg-indigo-900/30 dark:text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPegawaiAktif }}</div>
                <div class="text-sm text-gray-500 mt-2">Orang</div>
            </div>

        </div>

        {{-- SEPARATOR --}}
        <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

        {{-- ==============================================
             STATUS KEHADIRAN & OPERASIONAL HARI INI
             ============================================== --}}
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Kehadiran Operasional Hari Ini</h2>
            <p class="text-sm text-gray-500">Monitor kehadiran siswa dan guru secara *real-time*.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- Card Kehadiran Hari Ini Siswa (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kehadiran Siswa</h3>
                    <span class="p-2 text-orange-600 bg-orange-100 rounded-lg dark:bg-orange-900/30 dark:text-orange-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $attendanceRate }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ $todayStats['Hadir'] + $todayStats['Pulang'] + $todayStats['Telat'] }} Hadir dari
                    {{ $totalSiswa }} Siswa
                </div>
            </div>

            {{-- Card Kehadiran Pegawai (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Presensi Guru</h3>
                    <span class="p-2 text-green-600 bg-green-100 rounded-lg dark:bg-green-900/30 dark:text-green-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pegawaiAttendanceRate }}%</div>
                <div class="text-sm text-gray-500 mt-2">
                    {{ $todayPegawaiStats['Hadir'] + $todayPegawaiStats['Telat'] }} Hadir Hari Ini</div>
            </div>

            {{-- Card Terlambat (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Guru Terlambat</h3>
                    <span class="p-2 text-red-600 bg-red-100 rounded-lg dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayPegawaiStats['Telat'] }}</div>
                <div class="text-sm text-gray-500 mt-2">Orang</div>
            </div>

            {{-- Card Izin/Sakit (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Guru Izin/Sakit</h3>
                    <span class="p-2 text-yellow-600 bg-yellow-100 rounded-lg dark:bg-yellow-900/30 dark:text-yellow-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                    </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $todayPegawaiStats['Izin'] + $todayPegawaiStats['Sakit'] }}</div>
                <div class="text-sm text-gray-500 mt-2">Orang</div>
            </div>

        </div>

        {{-- SEPARATOR --}}
        <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

        {{-- ==============================================
             ANALITIK & GRAFIK (CHARTS)
             ============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

            {{-- Chart Tren Kehadiran Siswa (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Tren Kehadiran Siswa (7 Hari)</h3>
                </div>
                <div id="attendance-trend-chart"></div>
            </div>

            {{-- Chart Tren Pegawai (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Tren Kehadiran Guru (7 Hari)</h3>
                </div>
                <div id="pegawai-trend-chart"></div>
            </div>

            {{-- Chart Komposisi Hari Ini (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Komposisi Presensi Siswa</h3>
                </div>
                <div class="py-4" id="today-composition-chart"></div>

                {{-- Legend Custom (Original) --}}
                <div class="grid grid-cols-3 gap-2 text-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Hadir</div>
                        <div class="text-lg font-bold text-blue-600">{{ $todayStats['Hadir'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pulang</div>
                        <div class="text-lg font-bold text-purple-600">{{ $todayStats['Pulang'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Telat</div>
                        <div class="text-lg font-bold text-indigo-600">{{ $todayStats['Telat'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Sakit</div>
                        <div class="text-lg font-bold text-orange-500">{{ $todayStats['Sakit'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Izin</div>
                        <div class="text-lg font-bold text-yellow-500">{{ $todayStats['Izin'] }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Alpha</div>
                        <div class="text-lg font-bold text-red-600">{{ $todayStats['Alpha'] }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ==============================================
             PAPAN PERINGATAN (LISTS)
             ============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Leaderboard Terlambat (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Terlambat Hari Ini</h3>
                @if ($lateEmployees->count() > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($lateEmployees as $late)
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-xs">
                                            {{ substr($late->pegawai->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                            {{ $late->pegawai->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                            Datang: {{ $late->jam_masuk }}
                                        </p>
                                    </div>
                                    <div
                                        class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500">
                                        Telat
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4 text-green-500 text-sm">
                        <p>Tidak ada yang terlambat hari ini. Luar biasa!</p>
                    </div>
                @endif
            </div>

            {{-- Early Bird List (Original) --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Early Birds (Datang Paling Awal) 🏆</h3>
                @if ($earlyBirds->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($earlyBirds as $early)
                            <div class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs">
                                    {{ $loop->iteration }}
                                </span>
                                <div class="ml-3">
                                    <span class="block text-sm">{{ $early->pegawai->name }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-300">{{ $early->jam_masuk }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada data kehadiran hari ini.</p>
                @endif
            </div>

        </div>

    </div>

    {{-- ApexCharts CDN & Logic (Original) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1. CHART AREA: TREND 7 HARI
            const trendOptions = {
                chart: {
                    height: 320,
                    type: "area",
                    fontFamily: "Inter, sans-serif",
                    dropShadow: { enabled: false },
                    toolbar: { show: false },
                },
                tooltip: { enabled: true, x: { show: false } },
                fill: {
                    type: "gradient",
                    gradient: {
                        opacityFrom: 0.55,
                        opacityTo: 0,
                        shade: "#1C64F2",
                        gradientToColors: ["#1C64F2"],
                    },
                },
                dataLabels: { enabled: false },
                stroke: { width: 6 },
                grid: {
                    show: false,
                    strokeDashArray: 4,
                    padding: { left: 2, right: 2, top: 0 }
                },
                series: [{
                    name: "Siswa Hadir",
                    data: @json($trendData),
                    color: "#1A56DB",
                }],
                xaxis: {
                    categories: @json($dates),
                    labels: { show: true, style: { fontFamily: "Inter, sans-serif", cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: { show: false },
            };

            if (document.getElementById("attendance-trend-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("attendance-trend-chart"), trendOptions);
                chart.render();
            }

            // 1.5 CHART AREA: TREND PEGAWAI
            const pegawaiTrendOptions = {
                chart: {
                    height: 320,
                    type: "area",
                    fontFamily: "Inter, sans-serif",
                    dropShadow: { enabled: false },
                    toolbar: { show: false },
                },
                tooltip: { enabled: true, x: { show: false } },
                fill: {
                    type: "gradient",
                    gradient: {
                        opacityFrom: 0.55,
                        opacityTo: 0,
                        shade: "#7E3AF2", // Purple
                        gradientToColors: ["#7E3AF2"],
                    },
                },
                dataLabels: { enabled: false },
                stroke: { width: 6, colors: ["#7E3AF2"] },
                grid: {
                    show: false,
                    strokeDashArray: 4,
                    padding: { left: 2, right: 2, top: 0 }
                },
                series: [{
                    name: "Guru Hadir",
                    data: @json($pegawaiTrendData),
                    color: "#7E3AF2",
                }],
                xaxis: {
                    categories: @json($dates),
                    labels: { show: true, style: { fontFamily: "Inter, sans-serif", cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: { show: false },
            };

            if (document.getElementById("pegawai-trend-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("pegawai-trend-chart"), pegawaiTrendOptions);
                chart.render();
            }

            // 2. CHART DONUT: KOMPOSISI HARI INI
            const compositionOptions = {
                series: [
                    {{ $todayStats['Hadir'] }},
                    {{ $todayStats['Pulang'] }},
                    {{ $todayStats['Telat'] }},
                    {{ $todayStats['Sakit'] }},
                    {{ $todayStats['Izin'] }},
                    {{ $todayStats['Alpha'] }}
                ],
                colors: ["#1C64F2", "#A855F7", "#6366f1", "#fdba74", "#fde047", "#F05252"], 
                chart: {
                    height: 320,
                    width: "100%",
                    type: "donut",
                },
                stroke: { colors: ["transparent"], lineCap: "" },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: { show: true, fontFamily: "Inter, sans-serif", offsetY: 20 },
                                total: {
                                    showAlways: true,
                                    show: true,
                                    label: "Total Peserta",
                                    fontFamily: "Inter, sans-serif",
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                    },
                                },
                                value: { show: true, fontFamily: "Inter, sans-serif", offsetY: -20, formatter: function(value) { return value } },
                            },
                            size: "80%",
                        },
                    },
                },
                grid: { padding: { top: -2 } },
                labels: ["Hadir", "Pulang", "Telat", "Sakit", "Izin", "Alpha"],
                dataLabels: { enabled: false },
                legend: { position: "bottom", fontFamily: "Inter, sans-serif" },
                yaxis: { labels: { formatter: function(value) { return value } } },
                xaxis: {
                    labels: { formatter: function(value) { return value } },
                    axisTicks: { show: false },
                    axisBorder: { show: false },
                },
            };

            if (document.getElementById("today-composition-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("today-composition-chart"), compositionOptions);
                chart.render();
            }
        });
    </script>
</x-layout.layout>
