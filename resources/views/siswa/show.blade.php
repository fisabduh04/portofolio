<x-layout.layout>
    <x-breadcrumb :title="'Detail Siswa'" :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Data Siswa', 'href' => route('siswa.index')],
        ['name' => $siswa->nama, 'href' => '#'],
    ]" />

    {{-- Profile Header --}}
    <div class="mb-6 mx-auto bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        <div class="px-6 pb-6">
            <div class="relative flex items-end -mt-12 mb-4">
                <div class="relative w-24 h-24 rounded-xl border-4 border-white shadow-md overflow-hidden bg-gray-100 dark:border-gray-800">
                    @if($siswa->foto)
                        <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400 dark:bg-gray-700">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif
                </div>
                <div class="ml-4 mb-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $siswa->nama }}</h1>
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            {{ $siswa->nipd }}
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Kelas {{ $kelasAktif ?? 'Belum ada kelas' }}
                        </span>
                    </div>
                </div>
                <div class="ml-auto mb-1 hidden md:block">
                     @if($siswa->status == 'aktif')
                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-green-900 dark:text-green-300">Aktif</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-red-900 dark:text-red-300">Non-Aktif</span>
                    @endif
                </div>
            </div>

            {{-- Tabs --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Detail Profil</button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="attendance-tab" data-tabs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">
                            Rekap Absensi
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Tab Content --}}
    <div id="default-tab-content">
        {{-- Profile Tab --}}
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pribadi</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIPD</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->nipd }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NISN</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->nisn ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->tmpt_lahir }}, {{ $siswa->tgl_lahir }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $siswa->email ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Attendance Tab --}}
        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
            
            {{-- Smart Prediction Alert --}}
            @if(isset($prediction))
                <div class="p-4 mb-6 text-sm rounded-lg border flex items-start gap-3 shadow-sm
                    @if($prediction['color'] == 'red') bg-red-50 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800
                    @elseif($prediction['color'] == 'yellow') bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800
                    @else bg-green-50 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800 @endif" role="alert">
                    
                    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full 
                        @if($prediction['color'] == 'red') bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-200
                        @elseif($prediction['color'] == 'yellow') bg-yellow-100 text-yellow-600 dark:bg-yellow-800 dark:text-yellow-200
                        @else bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-200 @endif">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-base block mb-0.5">Analisis Kehadiran: {{ $prediction['status'] }}</span>
                        <p>{{ $prediction['message'] }}</p>
                        @if($prediction['trend'] == 'turun')
                            <p class="mt-1 text-xs font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                Perhatian: Tren kehadiran menurun.
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Hadir -->
                <div class="p-4 bg-white rounded-lg border border-blue-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-50 rounded-lg dark:bg-blue-900/20">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hadir</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Hadir'] }}</p>
                        </div>
                    </div>
                </div>
                 <!-- Sakit -->
                <div class="p-4 bg-white rounded-lg border border-yellow-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                         <div class="p-2 bg-yellow-50 rounded-lg dark:bg-yellow-900/20">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sakit</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Sakit'] }}</p>
                        </div>
                    </div>
                </div>
                 <!-- Izin -->
                <div class="p-4 bg-white rounded-lg border border-indigo-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                         <div class="p-2 bg-indigo-50 rounded-lg dark:bg-indigo-900/20">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Izin</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Izin'] }}</p>
                        </div>
                    </div>
                </div>
                 <!-- Alpha -->
                <div class="p-4 bg-white rounded-lg border border-red-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                         <div class="p-2 bg-red-50 rounded-lg dark:bg-red-900/20">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Alpha</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summaryStats['Alpha'] }}</p>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            {{-- Full Width Grid for Chart and History --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Chart Section (wider on desktop) --}}
                <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Statistik Kehadiran Bulanan ({{ $year }})</h3>
                    <div id="attendance-chart" class="w-full h-96"></div>
                </div>

                {{-- Recent Logs --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6 h-full">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Riwayat Terakhir</h3>
                    <div class="flow-root overflow-y-auto max-h-[384px] pr-2">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recentLogs as $log)
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ $log->logbook->jadwal->mapel->mapel ?? 'Mapel/Kegiatan' }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($log->logbook->tanggal)->isoFormat('dddd, D MMM Y') }} • {{ $log->logbook->jadwal->mulai ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-sm font-semibold">
                                            @if($log->status == 'Hadir')
                                                <span class="text-blue-600">Hadir</span>
                                            @elseif($log->status == 'Sakit')
                                                <span class="text-yellow-600">Sakit</span>
                                            @elseif($log->status == 'Izin')
                                                <span class="text-indigo-600">Izin</span>
                                            @elseif($log->status == 'Alpha')
                                                <span class="text-red-600">Alpha</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500 italic">Belum ada data absensi.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Academic History --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Riwayat Akademik</h3>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Tahun Ajaran</th>
                                    <th scope="col" class="px-6 py-3">Kelas</th>
                                    <th scope="col" class="px-6 py-3">Kehadiran (%)</th>
                                    <th scope="col" class="px-6 py-3 text-center">Rincian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $h)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $h->tahun->tahun ?? '-' }} 
                                            <span class="text-xs font-normal text-gray-500 ml-1">({{ $h->tahun->semester ?? '-' }})</span>
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ $h->kelas->kelas ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 max-w-[100px]">
                                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $h->stats['HadirPercent'] }}%"></div>
                                                </div>
                                                <span class="text-xs font-medium">{{ $h->stats['HadirPercent'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex gap-1 text-xs">
                                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800" title="Hadir">H: {{ $h->stats['H'] }}</span>
                                                <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-800" title="Sakit">S: {{ $h->stats['S'] }}</span>
                                                <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800" title="Izin">I: {{ $h->stats['I'] }}</span>
                                                <span class="px-2 py-0.5 rounded bg-red-100 text-red-800" title="Alpha">A: {{ $h->stats['A'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada riwayat akademik.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ApexCharts Script --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const chartOptions = {
                series: [
                    { name: 'Hadir', data: @json($chartData['Hadir']) },
                    { name: 'Sakit', data: @json($chartData['Sakit']) },
                    { name: 'Izin', data: @json($chartData['Izin']) },
                    { name: 'Alpha', data: @json($chartData['Alpha']) }
                ],
                chart: {
                    type: 'bar',
                    height: 320,
                    stacked: true,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                },
                colors: ['#3b82f6', '#eab308', '#6366f1', '#ef4444'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4,
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Sesi"
                        }
                    }
                },
                legend: { position: 'top' }
            };

            const chart = new ApexCharts(document.querySelector("#attendance-chart"), chartOptions);
            chart.render();
            
            // Tab Toggle Logic (Simple Helper since we use Flowbite attributes but might need manual toggle if js missing)
            const tabs = document.querySelectorAll('[role="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.getAttribute('data-tabs-target');
                    
                    // Hide all contents
                    document.querySelectorAll('[role="tabpanel"]').forEach(el => el.classList.add('hidden'));
                    // Show target
                    document.querySelector(targetId).classList.remove('hidden');
                    
                    // Reset tab styles
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-600', 'text-blue-600');
                        t.classList.add('hover:text-gray-600', 'hover:border-gray-300');
                    });
                    // Active tab style
                    tab.classList.remove('hover:text-gray-600', 'hover:border-gray-300');
                    tab.classList.add('border-blue-600', 'text-blue-600');
                    
                    // Trigger Chart resize if tab opened is attendance
                    if(targetId === '#attendance') {
                        setTimeout(() => { chart.updateOptions({}); }, 100); 
                    }
                });
            });
            
            // Activate first tab by default
            document.getElementById('attendance-tab').click();
        });
    </script>
</x-layout.layout>
