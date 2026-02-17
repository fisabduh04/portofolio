<x-layout.layout>
    <x-breadcrumb :title="'Profil Siswa'" :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Data Siswa', 'href' => route('siswa.index')],
        ['name' => $siswa->nama, 'href' => '#'],
    ]" />

    <div class="space-y-6">
        {{-- Profile Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="h-32 bg-gray-50 dark:bg-gray-700 relative"></div>
            <div class="px-6 pb-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="-mt-12 relative">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white dark:border-gray-800 bg-white dark:bg-gray-700 overflow-hidden shadow-md">
                            @if($siswa->foto)
                                <img src="{{ Storage::url($siswa->foto) }}" alt="{{ $siswa->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-600 text-gray-400">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 pt-6 md:pt-2">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text- white">{{ $siswa->nama }}</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                                    <span>{{ $siswa->nipd }}</span>
                                    <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                    <span>{{ $kelasAktif ?? 'Tanpa Rombel' }}</span>
                                    @if($siswa->nisn)
                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                        <span>NISN: {{ $siswa->nisn }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-{{ $siswa->aktif == 'Aktif' ? 'green' : 'red' }}-100 text-{{ $siswa->aktif == 'Aktif' ? 'green' : 'red' }}-800 text-xs font-medium px-2.5 py-0.5 rounded border border-{{ $siswa->aktif == 'Aktif' ? 'green' : 'red' }}-400">
                                    {{ $siswa->aktif }}
                                </span>
                                <a href="{{ route('siswa.edit', $siswa->id) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Tabs --}}
            <div class="border-t border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Detail Identitas</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="attendance-tab" data-tabs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">Laporan Kehadiran</button>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Tab Contents --}}
        <div id="default-tab-content">
            {{-- Tab 1: Identitas --}}
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pribadi</h3>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tempat, Tanggal Lahir</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $siswa->tempatlahir }}, {{ \Carbon\Carbon::parse($siswa->tanggallahir)->isoFormat('D MMMM Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jenis Kelamin</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $siswa->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Agama</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $siswa->agama }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $siswa->email ?? '-' }}</dd>
                                </div>
                                <div class="md:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">
                                        {{ $siswa->alamat }}, RT {{ $siswa->rt }}/RW {{ $siswa->rw }}, {{ $siswa->dusun }}, {{ $siswa->kelurahan }}, {{ $siswa->kecamatan }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Data Orang Tua / Wali</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4"> 
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 pb-2">Ayah</h4>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-xs text-gray-500 uppercase">Nama</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $siswa->ayah }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 uppercase">Pekerjaan</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $siswa->pekerjaanayah }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="space-y-4">
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-100 pb-2">Ibu</h4>
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-xs text-gray-500 uppercase">Nama</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $siswa->namaibu }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 uppercase">Pekerjaan</dt>
                                            <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $siswa->pekerjaanibu ?? '-' }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Lainnya</h3>
                            <ul class="space-y-4">
                                <li class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Transportasi</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $siswa->alat_transportasi }}</span>
                                </li>
                                <li class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Jenis Tinggal</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $siswa->jenis_tinggal }}</span>
                                </li>
                                <li class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Penerima KIP</span>
                                    <span class="font-medium {{ $siswa->penerimakip == 'Ya' ? 'text-green-600' : 'text-gray-500' }}">{{ $siswa->penerimakip }}</span>
                                </li>
                            </ul>
                        </div>
                         
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Fisik</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="block text-xs text-gray-500 uppercase">Tinggi</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $siswa->tinggibadan }} <span class="text-xs font-normal">cm</span></span>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <span class="block text-xs text-gray-500 uppercase">Berat</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $siswa->beratbadan }} <span class="text-xs font-normal">kg</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Kehadiran --}}
            <div class="hidden space-y-6" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $stats = [
                            ['label' => 'Hadir', 'value' => $summaryStats['Hadir'], 'color' => 'blue'],
                            ['label' => 'Sakit', 'value' => $summaryStats['Sakit'], 'color' => 'yellow'],
                            ['label' => 'Izin', 'value' => $summaryStats['Izin'], 'color' => 'indigo'],
                            ['label' => 'Alpha', 'value' => $summaryStats['Alpha'], 'color' => 'red'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</dt>
                            <dd class="mt-1 text-3xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {{-- Chart --}}
                    <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Grafik Kehadiran {{ $year }}</h3>
                        <div id="attendance-chart" class="w-full"></div>
                    </div>

                    {{-- Activity Log --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Aktivitas Terakhir</h3>
                        <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-3">                  
                            @forelse($recentLogs as $log)
                                <li class="mb-6 ml-6">
                                    <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                        <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $log->logbook->jadwal->mapel->mapel ?? 'Kegiatan' }} 
                                        @php
                                            $statusColor = match($log->status) {
                                                'Hadir' => 'bg-blue-100 text-blue-800',
                                                'Sakit' => 'bg-yellow-100 text-yellow-800',
                                                'Izin' => 'bg-indigo-100 text-indigo-800',
                                                'Alpha' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="{{ $statusColor }} text-xs font-medium px-2.5 py-0.5 rounded ml-3">
                                            {{ $log->status }}
                                        </span>
                                    </h3>
                                    <time class="block mb-2 text-xs font-normal leading-none text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($log->logbook->tanggal)->diffForHumans() }}</time>
                                    <p class="mb-2 text-sm font-normal text-gray-500 dark:text-gray-400">{{ $log->logbook->materi }}</p>
                                </li>
                            @empty
                                <li class="ml-6">
                                    <p class="text-sm text-gray-500 italic">Belum ada aktivitas.</p>
                                </li>
                            @endforelse
                        </ol>
                    </div>
                </div>

                {{-- Table --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rekapitulasi Bulanan</h3>
                    </div>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Bulan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-blue-600">Hadir</th>
                                    <th scope="col" class="px-6 py-3 text-center text-yellow-600">Sakit</th>
                                    <th scope="col" class="px-6 py-3 text-center text-indigo-600">Izin</th>
                                    <th scope="col" class="px-6 py-3 text-center text-red-600">Alpha</th>
                                    <th scope="col" class="px-6 py-3 text-center">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(range(0, 11) as $idx)
                                    @php 
                                        $h = $chartData['Hadir'][$idx] ?? 0;
                                        $s = $chartData['Sakit'][$idx] ?? 0;
                                        $i = $chartData['Izin'][$idx] ?? 0;
                                        $a = $chartData['Alpha'][$idx] ?? 0;
                                        $total = $h + $s + $i + $a;
                                    @endphp
                                    @if($total > 0)
                                        @php 
                                           $ratio = ($total > 0) ? round(($h / $total) * 100, 1) : 0;
                                        @endphp
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ \Carbon\Carbon::create(null, $idx + 1, 1)->isoFormat('MMMM') }}
                                            </th>
                                            <td class="px-6 py-4 text-center font-bold">{{ $h }}</td>
                                            <td class="px-6 py-4 text-center font-bold">{{ $s }}</td>
                                            <td class="px-6 py-4 text-center font-bold">{{ $i }}</td>
                                            <td class="px-6 py-4 text-center font-bold">{{ $a }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                                                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ $ratio }}%"> {{ $ratio }}%</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Flowbite Tabs handled via data attributes automatically, but we can add specific logic if needed
            
            // ApexCharts
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
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 4,
                        columnWidth: '60%',
                    },
                },
                dataLabels: { enabled: false },
                stroke: { width: 1, colors: ['#fff'] },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    labels: { style: { fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { fontSize: '12px' } }
                },
                colors: ['#2563eb', '#facc15', '#6366f1', '#ef4444'],
                fill: { opacity: 1 },
                legend: { position: 'top', horizontalAlign: 'right' }
            };

            const chart = new ApexCharts(document.querySelector("#attendance-chart"), chartOptions);
            chart.render();

             // Refesh chart on tab switch to ensure width is correct
             const attendanceTab = document.getElementById('attendance-tab');
             if(attendanceTab) {
                 attendanceTab.addEventListener('click', function() {
                     setTimeout(() => {
                         chart.updateOptions({});
                     }, 200);
                 });
             }
        });
    </script>
</x-layout.layout>
