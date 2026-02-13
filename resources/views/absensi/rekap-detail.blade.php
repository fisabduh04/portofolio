<x-layout.layout>
    {{-- Header Section --}}
    <div class="mb-6">
        <x-breadcrumb :breadcrumbs="[
            ['name' => 'Home', 'href' => route('dashboard.index')],
            ['name' => 'Rekapitulasi', 'href' => route('absensi.rekap')],
            ['name' => 'Detail Jurnal', 'href' => '#'],
        ]" />
        <div class="flex flex-col md:flex-row md:items-center justify-end gap-4 mt-2">
            <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['view' => 'summary'])) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center gap-2">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Lihat Ringkasan
            </a>
        </div>
    </div>

    {{-- Filter Panel (Standard Flowbite Card) --}}
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mb-6">
        <form action="{{ route('absensi.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <input type="hidden" name="view" value="detail">
            
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
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rentang Waktu</label>
                <div class="flex items-center gap-1">
                    <input type="date" name="from" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="date" name="to" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Filter Jurnal
                </button>
            </div>
        </form>
    </div>

    {{-- Detail Table (Flowbite Standard) --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-10">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal & Sesi</th>
                    <th scope="col" class="px-6 py-3">Pelajaran / Unit</th>
                    <th scope="col" class="px-6 py-3">Guru / Petugas</th>
                    <th scope="col" class="px-6 py-3">Materi</th>
                    <th scope="col" class="px-6 py-3 text-center">Absensi</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-xs text-gray-700">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</div>
                        <div class="text-gray-400">{{ $log->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($log->kategori == 'mapel')
                            <div class="font-bold text-blue-600 dark:text-blue-400">{{ $log->jadwal->mapel->mapel ?? '-' }}</div>
                            <div class="text-gray-400 font-medium">{{ $log->kelas->kelas ?? '-' }}</div>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                {{ strtoupper(str_replace('_', ' ', $log->kategori)) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ $log->pegawai->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="truncate max-w-xs font-medium">{{ $log->materi ?? '-' }}</div>
                        <div class="truncate max-w-xs text-xs text-gray-400 italic">{{ $log->catatan ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($log->absensis_count > 0)
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-green-600 font-bold">{{ $log->absensis->where('status', 'Hadir')->count() }}H</span>
                                <span class="text-red-600 font-bold">{{ $log->absensis->where('status', 'Alpha')->count() }}A</span>
                            </div>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-center">
                    <td colspan="6" class="px-6 py-12 text-gray-400">Record tidak tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="mb-10">
        {{ $logs->links() }}
    </div>
    @endif
</x-layout.layout>
