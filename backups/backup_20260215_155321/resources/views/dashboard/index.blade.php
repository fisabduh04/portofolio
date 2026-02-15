<x-layout.layout>
    <div class="space-y-6">
        
        {{-- Header & Breadcrumb --}}
        <div>
             <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Dashboard', 'href' => '#'],
            ]" />
            <p class="text-body-secondary">Ringkasan statistik akademik dan presensi sekolah.</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            
            {{-- Card Siswa --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Siswa</h3>
                     <span class="p-2 text-blue-600 bg-blue-100 rounded-lg dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                     </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalSiswa }}</div>
                <div class="text-sm text-green-500 flex items-center mt-2">
                     <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                     <span>Aktif</span>
                </div>
            </div>

            {{-- Card Pegawai --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Guru/Staf</h3>
                      <span class="p-2 text-purple-600 bg-purple-100 rounded-lg dark:bg-purple-900/30 dark:text-purple-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                     </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPegawai }}</div>
                 <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Terdaftar</div>
            </div>

            {{-- Card Kelas --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rombel (Kelas)</h3>
                      <span class="p-2 text-teal-600 bg-teal-100 rounded-lg dark:bg-teal-900/30 dark:text-teal-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                     </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalKelas }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">Dalam {{ $totalJurusan }} Jurusan</div>
            </div>

            {{-- Card Kehadiran Hari Ini --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                     <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kehadiran Hari Ini</h3>
                      <span class="p-2 text-orange-600 bg-orange-100 rounded-lg dark:bg-orange-900/30 dark:text-orange-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                     </span>
                </div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $attendanceRate }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    {{ $todayStats['Hadir'] }} Hadir dari {{ $totalSiswa }} Siswa
                </div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            {{-- Chart Tren Kehadiran --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Tren Kehadiran (7 Hari)</h3>
                </div>
                <div id="attendance-trend-chart"></div>
            </div>

            {{-- Chart Komposisi Hari Ini --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                 <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Komposisi Presensi Hari Ini</h3>
                </div>
                <div class="py-4" id="today-composition-chart"></div>
                
                {{-- Legend Custom --}}
                <div class="grid grid-cols-4 gap-2 text-center mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Hadir</div>
                        <div class="text-lg font-bold text-blue-600">{{ $todayStats['Hadir'] }}</div>
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

        {{-- (Existing) --}}
        <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
             
        </div>

    </div>

    {{-- ApexCharts CDN & Logic --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
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
                series: [
                    {
                        name: "Siswa Hadir",
                        data: @json($trendData),
                        color: "#1A56DB",
                    },
                ],
                xaxis: {
                    categories: @json($dates),
                    labels: {
                        show: true,
                        style: {
                            fontFamily: "Inter, sans-serif",
                            cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: { show: false },
            };

            if (document.getElementById("attendance-trend-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("attendance-trend-chart"), trendOptions);
                chart.render();
            }

            // 2. CHART DONUT: KOMPOSISI HARI INI
            const compositionOptions = {
                series: [
                    {{ $todayStats['Hadir'] }}, 
                    {{ $todayStats['Sakit'] }}, 
                    {{ $todayStats['Izin'] }}, 
                    {{ $todayStats['Alpha'] }}
                ],
                colors: ["#1C64F2", "#fdba74", "#fde047", "#F05252"], // Blue, Orange (S), Yellow (I), Red (A)
                chart: {
                    height: 320,
                    width: "100%",
                    type: "donut",
                },
                stroke: {
                    colors: ["transparent"],
                    lineCap: "",
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontFamily: "Inter, sans-serif",
                                    offsetY: 20,
                                },
                                total: {
                                    showAlways: true,
                                    show: true,
                                    label: "Total Peserta",
                                    fontFamily: "Inter, sans-serif",
                                    formatter: function (w) {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => {
                                            return a + b
                                        }, 0)
                                        return sum
                                    },
                                },
                                value: {
                                    show: true,
                                    fontFamily: "Inter, sans-serif",
                                    offsetY: -20,
                                    formatter: function (value) {
                                        return value
                                    },
                                },
                            },
                            size: "80%",
                        },
                    },
                },
                grid: {
                    padding: { top: -2 },
                },
                labels: ["Hadir", "Sakit", "Izin", "Alpha"],
                dataLabels: { enabled: false },
                legend: {
                    position: "bottom",
                    fontFamily: "Inter, sans-serif",
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value
                        },
                    },
                },
                xaxis: {
                    labels: {
                        formatter: function (value) {
                            return value
                        },
                    },
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
