<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Mesin Fingerprint', 'href' => '']
    ]" />

    <x-layout.sidebar.sub-menu :items="[
        ['name' => 'Dashboard', 'href' => route('attendance.index')],
        ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index'), 'active' => true],
        ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
        ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
        ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <!-- Top Controls -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('attendance.fingerprint.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-base hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Mesin
                    </a>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Nama</th>
                            <th scope="col" class="px-6 py-3 font-medium">IP Address</th>
                            <th scope="col" class="px-6 py-3 font-medium">Port</th>
                            <th scope="col" class="px-6 py-3 font-medium">Lokasi</th>
                            <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            <th scope="col" class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($machines as $machine)
                        <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $machine->name }}</td>
                            <td class="px-6 py-4">{{ $machine->ip_address }}</td>
                            <td class="px-6 py-4">{{ $machine->port }}</td>
                            <td class="px-6 py-4">{{ $machine->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($machine->status)
                                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-green-900 dark:text-green-300">
                                        <span class="w-2 h-2 me-1 bg-green-500 rounded-full animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-red-900 dark:text-red-300">
                                        <span class="w-2 h-2 me-1 bg-red-500 rounded-full"></span>
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('attendance.fingerprint.edit', $machine->id) }}" class="p-2 text-blue-600 bg-blue-50 rounded-base hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <form action="{{ route('attendance.fingerprint.destroy', $machine->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus mesin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-body-subtle">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <h3 class="text-lg font-medium text-heading">Belum ada data mesin fingerprint</h3>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $machines->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
