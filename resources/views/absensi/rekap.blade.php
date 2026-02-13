<x-layout.layout>
    @php
        $avgAttendance = ($dailyTrend && $dailyTrend->count() > 0) ? $dailyTrend->avg('total') : 0;
        $lastDay = $dailyTrend ? $dailyTrend->last() : null;
        $lastAttendance = $lastDay ? $lastDay->total : 0;
        $prevAttendance = ($dailyTrend && $dailyTrend->count() > 1) ? $dailyTrend->get($dailyTrend->count() - 2)->total : $avgAttendance;
        $trend = $lastAttendance - $prevAttendance;

        $startDate = request('from', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('to', now()->format('Y-m-d'));
        $kategori = request('kategori', 'mapel');
    @endphp

    {{-- Breadcrumb & Title --}}
    <div class="mb-6">
        <x-breadcrumb :breadcrumbs="[
            ['name' => 'Home', 'href' => route('dashboard.index')],
            ['name' => 'Rekapitulasi', 'href' => '#'],
            ['name' => 'Akumulasi Kehadiran', 'href' => '#'],
        ]" />
    </div>

    {{-- Filter Panel (Standard Flowbite Card) --}}
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mb-6">
        <form action="{{ route('absensi.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="hidden" name="view" value="{{ $view }}">
            
            <div>
                <label for="kategori" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                <select name="kategori" id="kategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="mapel" {{ $kategori == 'mapel' ? 'selected' : '' }}>GURU MAPEL</option>
                    <option value="piket" {{ $kategori == 'piket' ? 'selected' : '' }}>GURU PIKET</option>
                </select>
            </div>

            <div>
                <label for="kelas_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Kelas</label>
                <select name="kelas_id" id="kelas_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" {{ $filterKelasId == $lk->id ? 'selected' : '' }}>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="pegawai_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pegawai</label>
                <select name="pegawai_id" id="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Semua Guru</option>
                    @foreach($listPegawai as $lp)
                        <option value="{{ $lp->id }}" {{ $filterPegawaiId == $lp->id ? 'selected' : '' }}>{{ $lp->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rentang Waktu</label>
                <div class="flex items-center gap-1">
                    <input type="date" name="from" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <span class="text-gray-400">-</span>
                    <input type="date" name="to" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Update Data
                </button>
            </div>
        </form>
    </div>

    {{-- Main Dashboard Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Trend Chart Card --}}
        <div class="lg:col-span-2 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Tren Kehadiran Kolektif</h5>
                <div class="text-right">
                    <div class="text-3xl font-bold dark:text-white">{{ round($avgAttendance, 1) }}%</div>
                    <div class="text-sm font-medium {{ $trend >= 0 ? 'text-green-600 dark:text-green-500' : 'text-red-600 dark:text-red-500' }}">
                        {{ $trend >= 0 ? '+' : '' }}{{ round($trend, 1) }}% dari rata-rata
                    </div>
                </div>
            </div>
            <div id="rekap-chart-main" class="min-h-[350px]"></div>
        </div>

        {{-- Status Breakdown --}}
        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-6">Distribusi Status</h5>
            <div class="space-y-6">
                @foreach([
                    'Hadir' => ['color' => 'blue', 'text' => 'Kehadiran Normal'],
                    'Sakit' => ['color' => 'yellow', 'text' => 'Sakit'],
                    'Izin' => ['color' => 'indigo', 'text' => 'Izin'],
                    'Alpha' => ['color' => 'red', 'text' => 'Alpha']
                ] as $st => $meta)
                    @php 
                        $count = $stats[$st] ?? 0; 
                        $percent = $totalAbsensi > 0 ? ($count / $totalAbsensi) * 100 : 0; 
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $st }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $count }} ({{ round($percent, 1) }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-{{ $meta['color'] }}-600 h-2.5 rounded-full w-[{{ round($percent) }}%]"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['view' => 'detail'])) }}" class="inline-flex items-center text-blue-600 hover:underline">
                    Lihat detail jurnal pengisian
                    <svg class="w-3 h-3 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Bottom Grid Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        {{-- High Alpha Table --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white uppercase text-xs tracking-wider">Atensi Khusus (Alpha Tinggi)</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($topAlpha->take(5) as $s)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center font-bold text-gray-500 dark:text-gray-400">
                                {{ substr($s->siswa->nama, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $s->siswa->nama }}</h4>
                                <p class="text-xs text-gray-500">{{ $s->siswa->kelas->first()->kelas ?? '-' }}</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $s->total }} X</span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">Data tidak ditemukan</div>
                @endforelse
            </div>
        </div>

        {{-- Teacher Performance List --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white uppercase text-xs tracking-wider">Aktivitas Jurnal Terbanyak</h3>
            </div>
            <div class="p-4 space-y-5">
                @php $maxJurnal = $rekapGuru->max('total_jurnal') ?: 1; @endphp
                @foreach($rekapGuru->take(5) as $rg)
                    @php $progValue = ($rg->total_jurnal / $maxJurnal) * 100; @endphp
                    <div>
                        <div class="flex justify-between mb-1.5">
                            <span class="text-sm font-medium dark:text-gray-300 truncate max-w-[160px]">{{ $rg->name }}</span>
                            <span class="text-sm font-bold text-blue-600 dark:text-blue-500">{{ $rg->total_jurnal }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full w-[{{ round($progValue) }}%]"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Class Score Table --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white uppercase text-xs tracking-wider">Ranking Skor Kelas (%)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3 text-right">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($rekapKelas->take(8) as $rk)
                            @php $rate = $rk->total > 0 ? round(($rk->hadir / $rk->total) * 100) : 0; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 border-b dark:border-gray-700 last:border-0">
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $rk->kelas }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold {{ $rate >= 90 ? 'text-green-600' : ($rate >= 75 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $rate }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ApexCharts Library --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const options = {
                chart: { 
                    height: 350, 
                    type: 'area', 
                    fontFamily: 'Inter, sans-serif', 
                    toolbar: { show: false }
                },
                series: [{ 
                    name: 'Kehadiran (%)', 
                    data: @json($dailyTrend->pluck('total')) 
                }],
                fill: { 
                    type: 'gradient', 
                    gradient: { 
                        shadeIntensity: 1, 
                        opacityFrom: 0.4, 
                        opacityTo: 0.1,
                        stops: [0, 100]
                    } 
                },
                dataLabels: { enabled: false },
                stroke: { 
                    curve: 'smooth', 
                    width: 3, 
                    colors: ['#1d4ed8'] 
                },
                grid: { 
                    borderColor: '#f3f4f6', 
                    strokeDashArray: 4
                },
                xaxis: { 
                    categories: @json($dailyTrend->pluck('tanggal')),
                    labels: { 
                        style: { colors: '#9ca3af', fontSize: '10px' },
                        formatter: function(val) {
                            return val ? new Date(val).toLocaleDateString('id-ID', {day: '2-digit', month: 'short'}) : '';
                        }
                    }
                },
                yaxis: { 
                    max: 100, min: 0, 
                    labels: { 
                        style: { colors: '#9ca3af', fontSize: '10px' },
                        formatter: (val) => val + "%"
                    } 
                },
                colors: ['#1d4ed8'],
                tooltip: { theme: 'light' }
            };
            new ApexCharts(document.querySelector("#rekap-chart-main"), options).render();
        });
    </script>
</x-layout.layout>
