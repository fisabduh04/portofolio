<x-layout.layout>
    @php
        $avgAttendance = ($dailyTrend && $dailyTrend->count() > 0) ? $dailyTrend->avg('total') : 0;
        $lastDay = $dailyTrend ? $dailyTrend->last() : null;
        $lastAttendance = $lastDay ? $lastDay->total : 0;
        $prevAttendance = ($dailyTrend && $dailyTrend->count() > 1) ? $dailyTrend->get($dailyTrend->count() - 2)->total : $avgAttendance;
        $trend = $lastAttendance - $prevAttendance;

        $from = request('from', now()->startOfMonth()->format('Y-m-d'));
        $to = request('to', now()->format('Y-m-d'));
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('home')],
                ['name' => 'Absensi', 'href' => route('absensi.index')],
                ['name' => 'Rekapitulasi', 'href' => '#'],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Akumulasi Kehadiran Siswa</h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Live Reporting System</span>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-8">
        <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-gray-50 dark:border-gray-700 pb-4">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">Quick Filter:</span>
            <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['from' => now()->toDateString(), 'to' => now()->toDateString()])) }}" 
               class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-full border transition-all {{ $from == now()->toDateString() && $to == now()->toDateString() ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
               Hari Ini
            </a>
            <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['from' => now()->startOfWeek()->toDateString(), 'to' => now()->endOfWeek()->toDateString()])) }}" 
               class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-full border transition-all {{ $from == now()->startOfWeek()->toDateString() ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
               Minggu Ini
            </a>
            <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['from' => now()->startOfMonth()->toDateString(), 'to' => now()->endOfMonth()->toDateString()])) }}" 
               class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-full border transition-all {{ $from == now()->startOfMonth()->toDateString() ? 'bg-primary-600 border-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
               Bulan Ini
            </a>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['view' => 'detail'])) }}" 
                   class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                   Lihat Detail Jurnal
                </a>
            </div>
        </div>

        <form action="{{ route('absensi.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="hidden" name="view" value="{{ $view }}">
            
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Kategori</label>
                <select name="kategori" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="mapel" {{ $kategori == 'mapel' ? 'selected' : '' }}>GURU MAPEL</option>
                    <option value="piket" {{ $kategori == 'piket' ? 'selected' : '' }}>GURU PIKET</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Kelas</label>
                <select name="kelas_id" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" {{ $filterKelasId == $lk->id ? 'selected' : '' }}>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Guru</label>
                <select name="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Guru</option>
                    @foreach($listPegawai as $lp)
                        <option value="{{ $lp->id }}" {{ $filterPegawaiId == $lp->id ? 'selected' : '' }}>{{ $lp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Rentang Tanggal</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="from" value="{{ $from }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-primary-500 block w-full p-2 dark:bg-gray-700 dark:text-white">
                    <input type="date" name="to" value="{{ $to }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-primary-500 block w-full p-2 dark:bg-gray-700 dark:text-white">
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase py-3 rounded-xl transition-all shadow-lg shadow-primary-500/20">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Rerata Kehadiran</h3>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ round($avgAttendance, 1) }}%</div>
                </div>
                @if($trend != 0)
                <div class="flex items-center text-sm font-bold {{ $trend > 0 ? 'text-green-600' : 'text-red-500' }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $trend > 0 ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>
                    {{ abs(round($trend, 1)) }}%
                </div>
                @endif
            </div>
            <div id="rekap-chart-main" class="w-full"></div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-6">Breakdown Status</h3>
                <div class="space-y-5">
                    @foreach(['Hadir' => 'blue', 'Sakit' => 'green', 'Izin' => 'yellow', 'Alpha' => 'red'] as $st => $clr)
                        @php $count = $stats[$st] ?? 0; $p = $totalAbsensi > 0 ? ($count / $totalAbsensi) * 100 : 0; @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1.5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $st }}</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $count }} <span class="text-gray-400 font-normal">Siswa</span></span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-{{ $clr }}-500 h-1.5 rounded-full" style="width: {{ $p }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-8 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">Total Record: <span class="text-gray-900 dark:text-white font-black">{{ $totalAbsensi }}</span> Entri</p>
            </div>
        </div>
    </div>

    {{-- Lists Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Siswa Dengan Alpha Tinggi</h3>
            <div class="space-y-3">
                @forelse($topAlpha->take(5) as $s)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase truncate">{{ $s->siswa->nama }}</p>
                            <p class="text-[9px] text-gray-400 font-semibold lowercase tracking-widest">{{ $s->siswa->kelas }}</p>
                        </div>
                        <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded">{{ $s->total }} x</span>
                    </div>
                @empty
                    <p class="text-center py-4 text-xs text-gray-400 italic">Data tidak ditemukan.</p>
                @endforelse
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Log Pengisian Jurnal</h3>
            <div class="space-y-4">
                @php $maxJurnal = $rekapGuru->max('total_jurnal') ?: 1; @endphp
                @foreach($rekapGuru->take(5) as $rg)
                @php $progValue = ($rg->total_jurnal / $maxJurnal) * 100; @endphp
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300 truncate max-w-[120px]">{{ $rg->name }}</span>
                        <span class="text-[10px] font-bold text-blue-600">{{ $rg->total_jurnal }} Log</span>
                    </div>
                    <div class="w-full bg-gray-50 dark:bg-gray-700 rounded-full h-1">
                        <div class="bg-blue-500 h-1 rounded-full" style="width: {{ $progValue }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="p-6 bg-blue-600 rounded-lg shadow-lg flex flex-col justify-between text-white overflow-hidden relative group">
            <svg class="absolute -right-10 -bottom-10 w-48 h-48 text-white/10 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            <div>
                <h3 class="text-xl font-bold uppercase italic tracking-tight">Export Laporan</h3>
                <p class="text-xs text-blue-100 mt-2 font-medium opacity-80 leading-relaxed uppercase">Generate laporan lengkap periode ini dalam format PDF atau Excel.</p>
            </div>
            <div class="mt-8">
                <button class="w-full bg-white text-blue-700 py-3 rounded-lg text-xs font-bold uppercase tracking-widest shadow-xl hover:bg-gray-100 transition-all active:scale-95">Download Analysis</button>
            </div>
        </div>
    </div>

    {{-- Class Table --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50">
            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Performa Kehadiran Kelas</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                    <tr>
                        <th class="px-6 py-3">Nama Kelas</th>
                        <th class="px-6 py-3 text-center">Hadir</th>
                        <th class="px-6 py-3 text-center">Alpha</th>
                        <th class="px-6 py-3 text-right">Label Performa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($rekapKelas as $rk)
                        @php $rate = $rk->total > 0 ? round(($rk->hadir / $rk->total) * 100) : 0; @endphp
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-blue-600 dark:text-blue-400 uppercase">{{ $rk->kelas }}</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-700 dark:text-gray-300">{{ $rk->hadir }}</td>
                            <td class="px-6 py-4 text-center font-bold text-red-500">{{ $rk->alpha }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rate > 90 ? 'bg-green-100 text-green-800' : ($rate > 75 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $rate }}% Performance
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = {
                chart: { height: 250, type: 'area', fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                series: [{ name: 'Kehadiran (%)', data: @json($dailyTrend->pluck('total')) }],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0 } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2, colors: ['#2563eb'] },
                grid: { borderColor: '#f1f1f1', strokeDashArray: 3 },
                xaxis: { 
                    categories: @json($dailyTrend->pluck('tanggal')),
                    labels: { style: { colors: '#9ca3af', fontSize: '9px', fontWeight: 600 } }
                },
                yaxis: { max: 100, min: 0, labels: { style: { colors: '#9ca3af', fontSize: '9px', fontWeight: 600 } } },
                colors: ['#2563eb']
            };
            const chart = new ApexCharts(document.querySelector("#rekap-chart-main"), options);
            chart.render();
            if (document.documentElement.classList.contains('dark')) {
                chart.updateOptions({ grid: { borderColor: '#374151' }, tooltip: { theme: 'dark' } });
            }
        });
    </script>
</x-layout.layout>
