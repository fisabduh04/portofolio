<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Manajemen Jadwal', 'href' => route('attendance.rules.index')],
        ['name' => 'Jadwal Khusus', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        {{-- Internal Tab Navigation --}}
        <x-attendance.tab-nav active="overrides" />

        <div class="p-4 sm:p-6">
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                     <h2 class="text-lg font-medium text-gray-900 dark:text-white">Jadwal Khusus (Manual Exception)</h2>
                     <p class="text-sm text-gray-500">Atur jadwal khusus untuk pegawai tertentu pada tanggal tertentu.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('attendance.attendance.overrides.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-base hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Jadwal Khusus
                    </a>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-medium">Tanggal</th>
                            <th scope="col" class="px-6 py-3 font-medium">Rule yang Berlaku</th>
                            <th scope="col" class="px-6 py-3 font-medium">Alasan</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overrides as $override)
                        <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $override->pegawai->name }}</td>
                            <td class="px-6 py-4">{{ \Carbon\Carbon::parse($override->date)->isoFormat('D MMMM Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $override->attendanceRule->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $override->reason ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('attendance.attendance.overrides.destroy', $override->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jadwal khusus ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $overrides->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
