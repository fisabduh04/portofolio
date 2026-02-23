<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Events', 'href' => '']
    ]" />

    <x-layout.sidebar.sub-menu :items="[
        ['name' => 'Dashboard', 'href' => route('attendance.index')],
        ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
        ['name' => 'Laporan Individu', 'href' => route('attendance.report.employee')],
        ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
        ['name' => 'Events', 'href' => route('attendance.events.index'), 'active' => true],
        ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
        ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Event Khusus</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola kegiatan di luar jadwal rutin dan hitung otomatis absensi peserta.</p>
                </div>
                <a href="{{ route('attendance.events.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-base hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 shadow-sm whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Buat Event Baru
                </a>
            </div>

            <div class="relative overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Event</th>
                            <th scope="col" class="px-6 py-3">Tanggal & Waktu</th>
                            <th scope="col" class="px-6 py-3">Insentif</th>
                            <th scope="col" class="px-6 py-3">Peserta</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $event->name }}</div>
                                <div class="text-xs text-gray-400 truncate max-w-xs">{{ $event->description ?? 'Tidak ada deskripsi' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $event->date->isoFormat('dddd, D MMMM Y') }}</div>
                                <div class="text-xs text-blue-600 dark:text-blue-400">{{ substr($event->start_time, 0, 5) }} - {{ substr($event->end_time, 0, 5) }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($event->bantuan_hadir, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                    {{ $event->participants_count }} Orang
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('attendance.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini? Absensi peserta akan dihitung ulang ke jadwal normal.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition-all" title="Hapus Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                Belum ada event khusus yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
