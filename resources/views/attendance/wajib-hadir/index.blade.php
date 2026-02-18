<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Jadwal Wajib Hadir', 'href' => '']
    ]" />


    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Jadwal Wajib Hadir</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Hari yang <span class="font-medium text-blue-600">terkunci</span> otomatis dari jadwal mengajar/piket.
                        Operator hanya bisa mengubah hari yang <span class="font-medium text-gray-700 dark:text-gray-300">tidak terkunci</span>.
                        Pegawai yang tidak hadir pada hari wajib akan berstatus <span class="font-medium text-red-600">Alpha</span>.
                    </p>
                </div>
                <form action="{{ route('attendance.wajib-hadir.store') }}" method="POST" id="wajib-hadir-form">
                    @csrf
                    {{-- Hidden inputs for auto-checked days will be injected by JS --}}
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            @if(session('type') === 'success')
                <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                    {{ session('message') }}
                </div>
            @endif
            @if(session('type') === 'error')
                <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/></svg>
                    {{ session('message') }}
                </div>
            @endif

            {{-- Legend --}}
            <div class="flex items-center gap-6 mb-4 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border-2 border-blue-500 bg-blue-100 flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <span>Otomatis (Jadwal Mengajar / Piket) — Terkunci</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border-2 border-gray-300 bg-white flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <span>Manual (Bisa diubah operator)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded border-2 border-gray-300 bg-white"></div>
                    <span>Tidak Wajib Hadir</span>
                </div>
            </div>

            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <table class="w-full text-sm text-left rtl:text-right text-body">
                    <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium min-w-[220px]">Pegawai</th>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <th scope="col" class="px-4 py-3 font-medium text-center">{{ $hari }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawais as $pegawai)
                            <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                    {{ $pegawai->name }}
                                    <div class="text-xs text-body-subtle font-normal">{{ $pegawai->nuptk ?? $pegawai->nip ?? '-' }}</div>
                                </td>

                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    @php
                                        // Auto: from teaching or piket schedule
                                        $isAuto = isset($autoSchedules[$pegawai->id . '-' . $hari]);

                                        // Manual: stored in DB (excluding auto ones)
                                        $isManualChecked = !$isAuto && $pegawai->wajibHadirs->contains('hari', $hari);
                                    @endphp

                                    <td class="px-4 py-4 text-center">
                                        @if($isAuto)
                                            {{-- Auto-locked: checked, disabled, hidden input to still submit --}}
                                            <div class="flex flex-col items-center gap-1">
                                                <input type="checkbox" checked disabled
                                                    class="w-4 h-4 text-blue-600 bg-blue-50 border-blue-400 rounded cursor-not-allowed opacity-80 dark:bg-gray-700 dark:border-blue-500">
                                                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 leading-none">Jadwal</span>
                                            </div>
                                            {{-- Hidden input so it's submitted even though checkbox is disabled --}}
                                            <input type="hidden" form="wajib-hadir-form" name="manual_days[{{ $pegawai->id }}][]" value="{{ $hari }}">
                                        @else
                                            {{-- Manual: editable by operator --}}
                                            <input type="checkbox"
                                                form="wajib-hadir-form"
                                                name="manual_days[{{ $pegawai->id }}][]"
                                                value="{{ $hari }}"
                                                @checked($isManualChecked)
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-body-subtle">
                                    Tidak ada data pegawai aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="submit" form="wajib-hadir-form"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</x-layout.layout>
