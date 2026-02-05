<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Jadwal', 'href' => '#'],
                ['name' => 'Jadwal Piket', 'href' => route('jadwal-piket.index')],
            ]" />
            <p class="text-xs font-medium text-gray-500 ml-1 mt-1">Tahun Ajaran: {{ $tahunAktif->tahun ?? '-' }} {{ $tahunAktif->semester ?? '' }}</p>
        </div>
    </div>

    @if(!$tahunAktif)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200" role="alert">
            <span class="font-bold">Peringatan:</span> Tidak ada Tahun Ajaran aktif ditemukan.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($days as $day)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 dark:text-white uppercase">{{ $day }}</h3>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/40 dark:text-blue-300 px-2.5 py-0.5 rounded">
                            {{ isset($piketSchedule[$day]) ? $piketSchedule[$day]->count() : 0 }} Petugas
                        </span>
                    </div>
                    
                    <div class="p-4 flex-1 space-y-3">
                        @if(isset($piketSchedule[$day]) && $piketSchedule[$day]->count() > 0)
                            @foreach($piketSchedule[$day] as $sched)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg group hover:border-blue-400 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 flex items-center justify-center font-bold text-xs">
                                            {{ substr($sched->pegawai->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $sched->pegawai->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $sched->pegawai->nip ?? 'NIP -' }}</p>
                                        </div>
                                    </div>
                                    <button type="button" data-modal-target="popup-modal-piket-{{ $sched->id }}" data-modal-toggle="popup-modal-piket-{{ $sched->id }}"
                                        class="text-gray-400 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <x-modal.hapus :id="'piket-' . $sched->id" :action="route('jadwal-piket.destroy', $sched->id)" :message="'Hapus ' . $sched->pegawai->name . ' dari jadwal?'" />
                                </div>
                            @endforeach
                        @else
                            <div class="py-8 text-center">
                                <p class="text-xs text-gray-400 italic">Belum Ada Petugas</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/20">
                        <form action="{{ route('jadwal-piket.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="hari" value="{{ $day }}">
                            <div class="flex gap-2">
                                <select name="pegawai_id" required class="flex-1 bg-white border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">+ Tambah Petugas</option>
                                    @foreach($allPegawai as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.layout>
