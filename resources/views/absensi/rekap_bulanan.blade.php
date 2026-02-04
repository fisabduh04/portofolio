<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Absensi', 'href' => route('absensi.index')],
                ['name' => 'Jurnal Bulanan', 'href' => route('absensi.rekap-bulanan')],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Rekapitulasi Bulanan</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Laporan Akumulasi Kehadiran Per Siswa</p>
        </div>
        <div class="flex items-center gap-3">
             <x-btn href="{{ route('absensi.export-bulanan', request()->query()) }}" text="Export Excel" color="green" icon="save" size="sm" />
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-6">
        <form action="{{ route('absensi.rekap-bulanan') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Kelas</label>
                <select name="kelas_id" onchange="this.form.submit()" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                    <option value="">Pilih Kelas...</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" @selected($kelasId == $lk->id)>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Bulan</label>
                    <select name="month" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" @selected($month == $m)>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Tahun</label>
                    <select name="year" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                        @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                            <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="lg:col-span-1 flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase py-3 rounded-lg transition-all shadow-md">Tampilkan Data</button>
            </div>
        </form>
    </div>

    @if($kelasId && $selectedKelas)
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest">Matriks Kehadiran: {{ $selectedKelas->kelas }} ({{ date('F Y', mktime(0,0,0,$month,1,$year)) }})</h3>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Data Prioritas (A > I > S > H)</span>
                </div>
            </div>
            
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border-collapse">
                    <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                        <tr>
                            <th class="px-4 py-3 sticky left-0 bg-gray-50 dark:bg-gray-700 z-10 border-r border-gray-200 dark:border-gray-600 min-w-[50px] text-center">NO</th>
                            <th class="px-6 py-3 sticky left-[50px] bg-gray-50 dark:bg-gray-700 z-10 min-w-[200px] border-r border-gray-200 dark:border-gray-600">NAMA SISWA</th>
                            @for($d=1; $d<=$daysInMonth; $d++)
                                @php $isSun = date('N', mktime(0,0,0,$month,$d,$year)) == 7; @endphp
                                <th class="px-1 py-3 text-center border-r border-gray-100 dark:border-gray-700 min-w-[32px] {{ $isSun ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : '' }}">
                                    {{ $d }}
                                </th>
                            @endfor
                            <th class="px-4 py-3 text-center bg-gray-100 dark:bg-gray-600 font-black min-w-[80px]">REKAP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($rekapData as $idx => $row)
                            <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 sticky left-0 bg-white dark:bg-gray-800 z-10 border-r border-gray-200 dark:border-gray-600 text-center font-bold text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-6 py-3 sticky left-[50px] bg-white dark:bg-gray-800 z-10 border-r border-gray-200 dark:border-gray-600 font-bold text-gray-900 dark:text-white uppercase truncate">{{ $row['nama'] }}</td>
                                @for($d=1; $d<=$daysInMonth; $d++)
                                    @php 
                                        $st = $row['days'][$d]['main_status'];
                                        $color = match($st) {
                                            'H' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'S' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'I' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'A' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            default => ''
                                        };
                                    @endphp
                                    <td class="px-1 py-3 text-center border-r border-gray-50 dark:border-gray-700">
                                        @if($st)
                                            <span class="{{ $color }} text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">{{ $st }}</span>
                                        @endif
                                    </td>
                                @endfor
                                <td class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex justify-between text-[9px] font-bold"><span class="text-green-600">H:</span> <span class="text-gray-900 dark:text-white">{{ $row['daily_totals']['H'] }}</span></div>
                                        <div class="flex justify-between text-[9px] font-bold"><span class="text-red-600">A:</span> <span class="text-gray-900 dark:text-white">{{ $row['daily_totals']['A'] }}</span></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 flex items-center justify-between border-t dark:border-gray-700">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-800 rounded"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Hadir</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-800 rounded"></span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Alpha</span>
                    </div>
                </div>
                <button type="button" class="text-blue-600 hover:underline text-[10px] font-bold uppercase tracking-widest">Download PDF</button>
            </div>
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-16 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-600 dark:text-gray-300 uppercase">Pilih Kelas & Periode</h3>
            <p class="text-gray-400 text-sm mt-1 uppercase tracking-tight">Gunakan filter di atas untuk melihat akumulasi bulanan.</p>
        </div>
    @endif
</x-layout.layout>
