<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Konfigurasi', 'href' => '']
    ]" />

    <x-layout.sidebar.sub-menu :items="[
        ['name' => 'Dashboard', 'href' => route('attendance.index')],
        ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
        ['name' => 'Laporan Individu', 'href' => route('attendance.report.employee')],
        ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
        ['name' => 'Events', 'href' => route('attendance.events.index')],
        ['name' => 'Konfigurasi', 'href' => route('attendance.setting'), 'active' => true],
        ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <form action="{{ route('attendance.updateSetting') }}" method="POST">
                @csrf
                
                <!-- Top Controls -->
                <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-base hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Simpan Perubahan
                    </button>
                </div>

                <!-- Table Container -->
                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Pegawai</th>
                                <th scope="col" class="px-6 py-3 font-medium">ID Fingerprint</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawais as $key => $pegawai)
                            <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-heading">
                                    <input type="hidden" name="settings[{{ $key }}][id]" value="{{ $pegawai->id }}">
                                    <div class="flex flex-col">
                                        <span class="text-base font-semibold">{{ $pegawai->name }}</span>
                                        <span class="text-xs text-body-subtle">{{ $pegawai->nip ?? $pegawai->nuptk ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <input type="text" name="settings[{{ $key }}][fingerprint_id]" value="{{ $pegawai->fingerprint_id }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="ID Mesin">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-12 text-center text-body-subtle">
                                    Belum ada data pegawai.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $pegawais->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
