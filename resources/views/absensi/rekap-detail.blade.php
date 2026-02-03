<x-layout.layout>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Rekapitulasi', 'href' => route('absensi.rekap')],
                ['name' => 'Detail Journal', 'href' => '#'],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Detail Jurnal & Presensi</h1>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['view' => 'summary'])) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Lihat Ringkasan
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-8">
        <form action="{{ route('absensi.rekap') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="hidden" name="view" value="detail">
            
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Kategori</label>
                <select name="kategori" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-3 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    <option value="mapel" {{ $kategori == 'mapel' ? 'selected' : '' }}>GURU MAPEL</option>
                    <option value="piket" {{ $kategori == 'piket' ? 'selected' : '' }}>GURU PIKET</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Kelas</label>
                <select name="kelas_id" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-3 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" {{ $filterKelasId == $lk->id ? 'selected' : '' }}>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Rentang Tanggal</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="from" value="{{ $startDate }}" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    <input type="date" name="to" value="{{ $endDate }}" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-lg shadow-primary-500/20">
                    Filter Laporan
                </button>
            </div>
        </form>
    </div>

    {{-- Detail Logs Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-[10px] text-gray-400 uppercase font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Waktu & Sesi</th>
                        <th class="px-6 py-4">Kelas & Mapel</th>
                        <th class="px-6 py-4">Pengisi (Guru)</th>
                        <th class="px-6 py-4">Materi & Jurnal</th>
                        <th class="px-6 py-4 text-center">Kehadiran</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($log->tanggal)->format('d M Y') }}</div>
                            <div class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">{{ $log->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->kategori == 'mapel')
                                <div class="font-bold text-primary-600 truncate max-w-[150px] uppercase">{{ $log->jadwal->mapel->mapel ?? 'Mata Pelajaran' }}</div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $log->kelas->kelas ?? '-' }}</div>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-widest {{ $log->kategori == 'piket_masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ str_replace('_', ' ', $log->kategori) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                    {{ substr($log->pegawai->name ?? '?', 0, 1) }}
                                </div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate max-w-[120px]">{{ $log->pegawai->name ?? '-' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-gray-900 dark:text-white line-clamp-1">{{ $log->materi ?? '-' }}</div>
                            <div class="text-[10px] text-gray-400 italic line-clamp-1 mt-1">{{ $log->catatan ?? 'Tidak ada catatan' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($log->absensis_count > 0)
                                    <span class="text-xs font-bold text-emerald-600">{{ $log->absensis->where('status', 'Hadir')->count() }}H</span>
                                    <span class="text-xs font-bold text-rose-600">{{ $log->absensis->where('status', 'Alpha')->count() }}A</span>
                                @else
                                    <span class="text-[10px] text-gray-300">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="p-2 text-gray-400 hover:text-primary-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">Tidak ada data ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="p-6 bg-gray-50/50 dark:bg-gray-700/50 border-t border-gray-100 dark:border-gray-700">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>


@push('styles')
<style>
    .pagination { @apply flex items-center gap-1; }
    .page-item { @apply inline-flex; }
    .page-link { @apply px-3 py-1 text-xs font-bold rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50; }
    .page-item.active .page-link { @apply bg-primary-600 border-primary-600 text-white; }
</style>
@endpush
</x-layout.layout>
