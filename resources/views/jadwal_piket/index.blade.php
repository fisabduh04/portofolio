<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('home')],
                ['name' => 'Akademik', 'href' => '#'],
                ['name' => 'Jadwal Guru Piket', 'href' => route('jadwal-piket.index')],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight ml-1">Jadwal Petugas Piket</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1 mt-1">Tahun Ajaran: {{ $tahunAktif->tahun ?? '-' }} {{ $tahunAktif->semester ?? '' }}</p>
        </div>
    </div>

    @if(!$tahunAktif)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200" role="alert">
            <span class="font-bold uppercase tracking-widest">Peringatan:</span> Tidak ada Tahun Ajaran aktif ditemukan.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($days as $day)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 dark:text-white uppercase tracking-wider">{{ $day }}</h3>
                        <span class="text-[10px] font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/40 dark:text-blue-300 px-2 py-0.5 rounded uppercase">
                            {{ isset($piketSchedule[$day]) ? $piketSchedule[$day]->count() : 0 }} Petugas
                        </span>
                    </div>
                    
                    <div class="p-4 flex-1 space-y-3">
                        @if(isset($piketSchedule[$day]) && $piketSchedule[$day]->count() > 0)
                            @foreach($piketSchedule[$day] as $sched)
                                <div class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg group hover:border-blue-400 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-300 flex items-center justify-center font-bold text-xs">
                                            {{ substr($sched->pegawai->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-900 dark:text-white uppercase truncate max-w-[120px]">{{ $sched->pegawai->name }}</p>
                                            <p class="text-[9px] text-gray-400 font-mono">{{ $sched->pegawai->nip ?? 'NIP -' }}</p>
                                        </div>
                                    </div>
                                    <button type="button" data-modal-target="popup-modal-piket-{{ $sched->id }}" data-modal-toggle="popup-modal-piket-{{ $sched->id }}"
                                        class="text-gray-300 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <x-modal.hapus :id="'piket-' . $sched->id" :action="route('jadwal-piket.destroy', $sched->id)" :message="'Hapus ' . $sched->pegawai->name . ' dari jadwal?'" />
                                </div>
                            @endforeach
                        @else
                            <div class="py-10 text-center">
                                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold italic">Belum Ada Petugas</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-700/20">
                        <form action="{{ route('jadwal-piket.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="hari" value="{{ $day }}">
                            <div class="flex gap-2">
                                <select name="pegawai_id" required class="flex-1 bg-white border border-gray-300 text-gray-900 text-[10px] font-bold uppercase rounded-lg focus:ring-blue-500 block p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">+ Tambah Petugas</option>
                                    @foreach($allPegawai as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-2.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.layout>
