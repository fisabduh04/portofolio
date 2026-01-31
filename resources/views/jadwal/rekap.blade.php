<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Akademik', 'href' => ''],
        ['name' => 'Jadwal', 'href' => route('jadwal.index')],
        ['name' => 'Rekapitulasi', 'href' => '#'],
    ]" />

    {{-- Main Content Card --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        
        {{-- Header Section --}}
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total jam mengajar efektif berdasarkan jadwal (1 jam = 60 menit durasi pelajaran).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <x-btn text="Kembali ke Jadwal" icon="calendar" color="light" href="{{ route('jadwal.index') }}" />
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <form action="{{ route('jadwal.rekap') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end flex-wrap">
                {{-- Filter Tahun --}}
                <div class="w-full md:w-64">
                    <label for="filter_tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun Ajaran</label>
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
                    <label for="filter_kelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas (Opsional)</label>
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

                {{-- Per Page --}}
                <div class="w-24">
                    <label for="perpage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Show</label>
                    <select name="perpage" id="perpage" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="10" {{ $perpage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perpage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perpage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perpage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                {{-- Search --}}
                <div class="w-full md:w-72">
                    <label for="search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cari Guru / NIP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ $search }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            placeholder="Ketik nama atau NIP..." />
                        <button type="submit" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-white bg-blue-700 rounded-r-base hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-16 text-center">NO</th>
                        <th scope="col" class="px-6 py-4">Nama Guru / NIP</th>
                        <th scope="col" class="px-6 py-4 text-center">Total Jam</th>
                        <th scope="col" class="px-6 py-4">Detail Mengajar (Mapel - Kelas : Jam)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pegawais as $index => $guru)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 text-center font-medium">
                                {{ $pegawais->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-base flex items-center justify-center font-bold text-xs">
                                        {{ substr($guru->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $guru->name }}</div>
                                        <div class="font-normal text-xs text-gray-500">NUPTK: {{ $guru->nuptk ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($guru->total_jam_mengajar > 0)
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        {{ $guru->total_jam_mengajar }} Jam
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(!empty($guru->detail_mengajar))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($guru->detail_mengajar as $label => $jam)
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600">
                                                {{ $label }} : <strong class="text-gray-900 dark:text-white">{{ $jam }}J</strong>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Tidak ada jadwal tercatat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">Tidak ada data ditemukan</p>
                                    <p class="text-sm">Coba ubah filter atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Table Footer Summary --}}
                @if($pegawais->isNotEmpty())
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white">TOTAL JAM (Halaman Ini):</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-indigo-100 text-indigo-800 text-base font-bold px-3 py-1 rounded-full dark:bg-indigo-900 dark:text-indigo-300">
                                    {{ $pegawais->sum('total_jam_mengajar') }} Jam
                                </span>
                            </td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $pegawais->links() }}
        </div>
    </div>
</x-layout.layout>
