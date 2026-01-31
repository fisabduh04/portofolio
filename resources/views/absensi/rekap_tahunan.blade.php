<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('home')],
                ['name' => 'Absensi', 'href' => route('absensi.index')],
                ['name' => 'Rekap Tahunan', 'href' => '#'],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Akumulasi Tahunan Siswa</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Laporan Akhir Tahun Ajaran Terpadu</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
        <form action="{{ route('absensi.rekap-tahunan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Pilih Kelas</label>
                <select name="kelas_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kelas...</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" @selected($kelasId == $lk->id)>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tahun Ajaran</label>
                <select name="tahun_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    @foreach($listTahun as $lt)
                        <option value="{{ $lt->id }}" @selected($tahunId == $lt->id)>TP {{ $lt->tahun }} ({{ $lt->semester }})</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-start-4 flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase py-3 rounded-lg shadow-md transition-all">Generate Laporan</button>
            </div>
        </form>
    </div>

    @if($kelasId && $selectedKelas)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-6 border-b border-gray-100 dark:border-gray-700 bg-blue-600 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xs font-bold text-blue-100 uppercase tracking-[0.2em] mb-1">Database Akademik</h3>
                    <h2 class="text-xl font-black uppercase tracking-tight">Rekapitulasi Akhir: {{ $selectedKelas->kelas }}</h2>
                </div>
                <div class="p-3 bg-white/10 rounded-lg backdrop-blur-md border border-white/20 text-center min-w-[120px]">
                    <div class="text-[10px] font-bold uppercase opacity-80 leading-none mb-1">Total Populasi</div>
                    <div class="text-lg font-black leading-none">{{ $rekapData->count() }} <span class="text-[10px] font-normal uppercase">Siswa</span></div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                        <tr>
                            <th class="px-6 py-4 text-center w-12">NO</th>
                            <th class="px-6 py-4">Siswa & Identitas</th>
                            <th class="px-4 py-4 text-center">Hadir (H)</th>
                            <th class="px-4 py-4 text-center">Sakit (S)</th>
                            <th class="px-4 py-4 text-center">Izin (I)</th>
                            <th class="px-4 py-4 text-center">Alpha (A)</th>
                            <th class="px-6 py-4 text-right">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($rekapData as $idx => $s)
                            @php 
                                $total = $s->hadir + $s->sakit + $s->izin + $s->alpha;
                                $rate = $total > 0 ? round(($s->hadir / $total) * 100) : 0; 
                            @endphp
                            <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-center text-xs font-bold text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white uppercase truncate">{{ $s->nama }}</div>
                                    <div class="text-[9px] text-gray-400 font-mono mt-0.5">{{ $s->nipd }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $s->hadir }}</span>
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-blue-600">{{ $s->sakit }}</td>
                                <td class="px-4 py-4 text-center font-bold text-yellow-600">{{ $s->izin }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $s->alpha }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-black {{ $rate > 90 ? 'text-green-600' : ($rate > 75 ? 'text-blue-600' : 'text-red-600') }}">
                                            {{ $rate }}%
                                        </span>
                                        <div class="w-12 bg-gray-100 dark:bg-gray-700 h-1 rounded-full mt-1.5 overflow-hidden">
                                            <div class="h-full {{ $rate > 90 ? 'bg-green-500' : ($rate > 75 ? 'bg-blue-500' : 'bg-red-500') }}" style="width: {{ $rate }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        <span class="text-[10px) font-bold text-gray-500 uppercase">Alpha</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-btn href="{{ route('absensi.export-tahunan', ['kelas_id' => $kelasId, 'tahun_id' => $tahunId]) }}" text="EXPORT EXCEL" color="green" icon="save" size="sm" />
                    <x-btn text="CETAK REKAP" color="blue" icon="save" size="sm" onclick="window.print()" />
                </div>
            </div>
        </div>
    @else
        <div class="p-16 text-center bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
             <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Silakan Tentukan Filter</h3>
        </div>
    @endif
</x-layout.layout>
