<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Manajemen Jadwal', 'href' => route('attendance.rules.index')],
        ['name' => 'Events', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mt-4">
        {{-- Internal Tab Navigation using existing component scheme --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
             <x-layout.sidebar.sub-menu />
        </div>

        <div class="p-4 sm:p-6">
            <!-- Top Controls -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Events & Kegiatan Khusus</h2>
                     <p class="text-sm text-gray-500 dark:text-gray-400">Kelola jadwal rapat, kegiatan sekolah, atau event lainnya.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-btn href="{{ route('attendance.events.create') }}" color="blue" icon="plus">
                        Tambah Event
                    </x-btn>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Event</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Waktu</th>
                            <th scope="col" class="px-6 py-3">Peserta</th>
                            <th scope="col" class="px-6 py-3">Bantuan Hadir</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($events as $event)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $event->name }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($event->date)->locale('id')->isoFormat('D MMMM Y') }}</td>
                            <td class="px-6 py-4">{{ $event->start_time }} - {{ $event->end_time }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    {{ $event->participants_count }} Peserta
                                </span>
                            </td>
                            <td class="px-6 py-4">Rp {{ number_format($event->bantuan_hadir, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                     <form action="{{ route('attendance.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">Belum ada event.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
