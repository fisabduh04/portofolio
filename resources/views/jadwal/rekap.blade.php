<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Akademik', 'href' => ''],
        ['name' => 'Jadwal', 'href' => route('jadwal.index')],
        ['name' => 'Rekapitulasi', 'href' => '#'],
    ]" />

    {{-- Main Content Card --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Rekapitulasi Jam Mengajar</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total jam mengajar efektif berdasarkan jadwal aktif.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <x-btn text="Kembali ke Jadwal" icon="calendar" color="light" href="{{ route('jadwal.index') }}" />
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="mb-6">
                <form action="{{ route('jadwal.rekap') }}" method="GET" class="flex flex-wrap items-end gap-3">
                    {{-- Filter Tahun --}}
                    <div class="w-full md:w-64">
                        <label for="filter_tahun" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tahun Ajaran</label>
                        <select name="filter_tahun" id="filter_tahun" onchange="this.form.submit()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            @foreach($tahun as $t)
                                <option value="{{ $t->id }}" {{ $filter_tahun == $t->id ? 'selected' : '' }}>
                                    {{ $t->tahun }} - {{ $t->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kelas --}}
                    <div class="w-full md:w-48">
                        <label for="filter_kelas" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kelas</label>
                        <select name="filter_kelas" id="filter_kelas" onchange="this.form.submit()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $filter_kelas == $k->id ? 'selected' : '' }}>
                                    {{ $k->kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="flex-1 min-w-[300px]">
                        <label for="search" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cari Guru / NIP</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ $search }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="Ketik nama atau NIP..." />
                        </div>
                    </div>

                    {{-- Per Page --}}
                    <div class="w-20">
                        <label for="perpage" class="block mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Show</label>
                        <select name="perpage" id="perpage" onchange="this.form.submit()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="10" {{ $perpage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perpage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perpage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="relative overflow-x-auto bg-white dark:bg-gray-800 shadow-xs rounded-base border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center text-gray-500">NO</th>
                            <th scope="col" class="px-6 py-4">Nama Guru / NIP</th>
                            <th scope="col" class="px-6 py-4 text-center">Total Jam</th>
                            <th scope="col" class="px-6 py-4">Detail Mengajar (Mapel - Kelas : Jam)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($pegawais as $index => $guru)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-medium text-gray-400">
                                    {{ $pegawais->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative w-9 h-9 overflow-hidden rounded-full bg-blue-50 border border-blue-100 shadow-sm flex-shrink-0 flex items-center justify-center font-bold text-blue-600 text-xs">
                                            {{ substr($guru->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $guru->name }}</span>
                                            <span class="text-xs text-gray-500">NUPTK: {{ $guru->nuptk ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($guru->total_jam_mengajar > 0)
                                        <span class="inline-flex items-center bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                            {{ $guru->total_jam_mengajar }} Jam
                                        </span>
                                    @else
                                        <span class="text-gray-300 italic text-xs">0 Jam</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(!empty($guru->detail_mengajar))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($guru->detail_mengajar as $label => $jam)
                                                <span class="inline-flex items-center bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[10px] font-medium px-2 py-0.5 rounded-base border border-gray-200 dark:border-gray-600 shadow-xs">
                                                    <span class="mr-1.5 opacity-60">{{ $label }}</span>
                                                    <span class="font-bold text-gray-900 dark:text-white border-l border-gray-100 dark:border-gray-600 pl-1.5">{{ $jam }}J</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada jadwal aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </div>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">Tidak ada data ditemukan</p>
                                        <p class="text-xs mt-1">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    {{-- Table Footer Summary --}}
                    @if($pegawais->isNotEmpty())
                        <tfoot class="bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">TOTAL JAM (Halaman Ini) :</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                        {{ $pegawais->sum('total_jam_mengajar') }} Jam
                                    </span>
                                </td>
                                <td class="px-6 py-4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="mt-6">
                {{ $pegawais->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
