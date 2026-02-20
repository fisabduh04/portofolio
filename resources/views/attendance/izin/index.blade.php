<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Perizinan', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm mt-4">
        {{-- Internal Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
             <x-layout.sidebar.sub-menu :items="[
                ['name' => 'Dashboard', 'href' => route('attendance.index')],
                ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
                ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
                ['name' => 'Events', 'href' => route('attendance.events.index')],
                ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
                ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
                ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
            ]" />
        </div>

        <div class="p-4 sm:p-6">
            <!-- Top Controls -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                     <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Izin & Cuti</h2>
                     <p class="text-sm text-gray-500 dark:text-gray-400">Kelola perizinan, sakit, dan cuti pegawai.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-btn href="{{ route('attendance.izin.create') }}" color="blue" icon="plus">
                        Tambah Izin/Cuti
                    </x-btn>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Pegawai</th>
                            <th scope="col" class="px-6 py-3">Jenis</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Lama</th>
                            <th scope="col" class="px-6 py-3">Keterangan</th>
                            <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($izins as $izin)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $izin->pegawai->nama }}
                            </th>
                            <td class="px-6 py-4">
                                @php
                                    $color = match($izin->jenis_izin) {
                                        'Sakit' => 'yellow',
                                        'Izin' => 'red',
                                        'Cuti' => 'blue',
                                        'Dinas Luar' => 'green',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-{{ $color }}-900 dark:text-{{ $color }}-300">
                                    {{ $izin->jenis_izin }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ $izin->tanggal_mulai->locale('id')->isoFormat('D MMM Y') }} 
                                @if($izin->tanggal_mulai != $izin->tanggal_akhir)
                                    - {{ $izin->tanggal_akhir->locale('id')->isoFormat('D MMM Y') }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $izin->tanggal_mulai->diffInDays($izin->tanggal_akhir) + 1 }} Hari
                            </td>
                            <td class="px-6 py-4 truncate max-w-xs">
                                {{ $izin->keterangan ?? '-' }}
                                @if($izin->bukti_dokumen)
                                    <a href="{{ Storage::url($izin->bukti_dokumen) }}" target="_blank" class="text-blue-600 hover:underline ml-1 text-xs">[Lihat Bukti]</a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                     <form action="{{ route('attendance.izin.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Hapus data izin ini? Data absensi terkait akan dihapus.');">
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
                            <td colspan="6" class="px-6 py-4 text-center">Belum ada data izin.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $izins->links() }}
            </div>
        </div>
    </div>
</x-layout.layout>
