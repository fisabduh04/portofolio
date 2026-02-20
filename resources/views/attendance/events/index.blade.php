@extends('layouts.app')

@section('content')
<div class="px-4 pt-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 text-white ">
        <div>
            <h1 class="text-2xl font-bold flex gap-2 items-center">
                <span class="p-2 border border-blue-400 bg-blue-400/10 rounded-xl ">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </span>
                Event Khusus
            </h1>
            <p class="text-sm text-gray-400 mt-2">Kelola kegiatan di luar jadwal rutin dan hitung otomatis absensi peserta.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('attendance.events.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Buat Event Baru
            </a>
        </div>
    </div>

    <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 overflow-hidden shadow-sm sm:rounded-2xl">
        <div class="p-6">
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Nama Event</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Tanggal & Waktu</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Insentif</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Peserta</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($events as $event)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">{{ $event->name }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $event->description ?? 'Tidak ada deskripsi' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">{{ $event->date->isoFormat('dddd, D MMMM Y') }}</div>
                                <div class="text-xs text-blue-400">{{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-emerald-400">
                                Rp {{ number_format($event->bantuan_hadir, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-900/30 text-blue-400 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-blue-800/50">
                                    {{ $event->participants_count }} Orang
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('attendance.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini? Absensi peserta akan dihitung ulang ke jadwal normal.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all" title="Hapus Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 00-2 2H6a2 2 0 00-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <span class="text-gray-500">Belum ada event khusus yang dibuat.</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
