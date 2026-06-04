<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Presensi', 'href' => '#'],
        ['name' => 'Harian', 'href' => route('absensi.harian.index')],
        ['name' => 'Input ' . ucfirst($type), 'href' => '#'],
    ]" />

    <form action="{{ route('absensi.harian.store') }}" method="POST" enctype="multipart/form-data" id="absensiForm">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
        <input type="hidden" name="kategori" value="{{ $kategori }}">

        <!-- Session Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 md:p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-{{ $type == 'masuk' ? 'emerald' : 'pink' }}-50 dark:bg-{{ $type == 'masuk' ? 'emerald' : 'pink' }}-900/20 rounded-xl">
                        <svg class="w-8 h-8 text-{{ $type == 'masuk' ? 'emerald' : 'pink' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Absensi Harian: {{ ucfirst($type) }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $kelas->kelas }}
                            </span>
                            <span class="flex items-center gap-1 text-blue-600 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ now()->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                    <div class="flex items-center gap-2 text-xs font-bold">
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-md border border-emerald-200">
                            H: <span id="stat-hadir">0</span>
                        </span>
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-md border border-amber-200">
                            S: <span id="stat-sakit">0</span>
                        </span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md border border-blue-200">
                            I: <span id="stat-izin">0</span>
                        </span>
                        <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-md border border-rose-200">
                            A: <span id="stat-alpha">0</span>
                        </span>
                    </div>
                    <button type="button" onclick="setAll('Hadir')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800 transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Semua Hadir
                    </button>
                </div>
            </div>
        </div>

        @if($existingLogbook)
        <div class="mb-6 p-4 text-sm text-blue-800 border border-blue-300 rounded-2xl bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800" role="alert">
            <div class="flex items-center gap-2 font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Update Mode
            </div>
            <p class="mt-1">Anda sedang mengedit data absensi yang sudah diisi sebelumnya.</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Student List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900 dark:text-white">Daftar Kehadiran Siswa</h3>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-xs font-medium">
                            {{ $students->count() }} Siswa
                        </span>
                    </div>
                    <div class="overflow-x-visible">
                        <table class="w-full text-left border-collapse">
                            <thead class="hidden md:table-header-group bg-gray-50/50 dark:bg-gray-700/50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold rounded-tl-xl text-center w-10">No</th>
                                    <th class="px-6 py-4 font-semibold">Siswa</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                                    <th class="px-6 py-4 font-semibold rounded-tr-xl">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y md:divide-gray-100 dark:md:divide-gray-700">
                                @php
                                    $attendanceMap = ($existingLogbook && $existingLogbook->absensis) 
                                        ? $existingLogbook->absensis->keyBy('siswa_id') 
                                        : collect([]);
                                @endphp

                                @foreach($students as $index => $siswa)
                                @php
                                    $existingStatus = $attendanceMap[$siswa->id]->status ?? null;
                                    $existingNote = $attendanceMap[$siswa->id]->keterangan ?? '';
                                @endphp
                                <tr class="block md:table-row bg-white dark:bg-gray-800 border md:border-none border-gray-100 dark:border-gray-700 rounded-xl shadow-sm md:shadow-none p-4 md:p-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors text-sm">
                                    <td class="hidden md:table-cell px-6 py-4 text-center text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="block md:table-cell px-2 py-2 md:px-6 md:py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-700 dark:text-blue-400 font-bold text-sm flex-shrink-0">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $siswa->nama }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="block md:table-cell px-2 py-2 md:px-6 md:py-4 border-t border-gray-50 md:border-none dark:border-gray-700 mt-2 md:mt-0">
                                        <div class="flex items-center justify-between md:justify-center gap-2">
                                            <span class="text-xs font-semibold text-gray-400 md:hidden">Status:</span>
                                            <div class="flex gap-2">
                                                @foreach(['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpha' => 'A'] as $label => $short)
                                                <label class="cursor-pointer group">
                                                    <input type="radio" 
                                                           name="attendance[{{ $siswa->id }}][status]" 
                                                           value="{{ $label }}" 
                                                           class="hidden peer"
                                                           data-type="{{ $label }}"
                                                           {{ $existingStatus == $label ? 'checked' : '' }}>
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center border-2 transition-all
                                                        @if($label == 'Hadir') peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-emerald-200
                                                        @elseif($label == 'Sakit') peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-amber-200
                                                        @elseif($label == 'Izin') peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-blue-200
                                                        @elseif($label == 'Alpha') peer-checked:bg-rose-500 peer-checked:border-rose-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-rose-200
                                                        @endif">
                                                        <span class="text-xs font-bold">{{ $short }}</span>
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="block md:table-cell px-2 py-2 md:px-6 md:py-4 border-t border-gray-50 md:border-none dark:border-gray-700 mt-2 md:mt-0 text-center">
                                        <input type="text" 
                                               name="attendance[{{ $siswa->id }}][keterangan]" 
                                               value="{{ $existingNote }}"
                                               placeholder="Ket..." 
                                               class="w-full md:w-32 bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-xs focus:ring-2 focus:ring-blue-500 placeholder-gray-400">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Journal & Documentation -->
            <div class="space-y-6">
                <!-- Journal Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Catatan Harian
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Kejadian (Opsional)</label>
                            <textarea name="catatan" rows="4" placeholder="Ketik kejadian penting hari ini..." 
                                      class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan', $existingLogbook->catatan ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Documentation Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 012 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Dokumentasi Absensi
                    </h3>
                    
                    @if(isset($existingLogbook) && $existingLogbook->foto)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Tersimpan:</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(json_decode($existingLogbook->foto) as $foto)
                            <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group">
                                <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-cover">
                                <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs">
                                    Lihat Full
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload foto</span></p>
                                <p class="text-[10px] text-gray-400 uppercase tracking-wider">PNG, JPG atau JPEG (Max. 2MB)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="foto[]" multiple class="hidden" onchange="handleFiles(this.files)" accept="image/*" />
                        </label>
                    </div>
                    <!-- Preview Container -->
                    <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4"></div>
                </div>

                <!-- Submit Action -->
                <button type="submit" class="w-full flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2-2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Absensi Harian
                </button>
            </div>
        </div>
    </form>

<script>
    window.handleFiles = function(files) {
        const previewContainer = document.getElementById('image-preview-container');
        if (!previewContainer) return;
        previewContainer.innerHTML = ''; 
        if (files && files.length > 0) {
            Array.from(files).forEach(file => {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-white text-[10px] font-medium">${(file.size/1024).toFixed(1)} KB</span>
                            </div>
                        `;
                        previewContainer.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        function updateStats() {
            const counts = { 'Hadir': 0, 'Sakit': 0, 'Izin': 0, 'Alpha': 0 };
            document.querySelectorAll('input[name^="attendance"][name$="[status]"]:checked').forEach(radio => {
                if (counts.hasOwnProperty(radio.value)) { counts[radio.value]++; }
            });
            document.getElementById('stat-hadir').innerText = counts['Hadir'];
            document.getElementById('stat-sakit').innerText = counts['Sakit'];
            document.getElementById('stat-izin').innerText = counts['Izin'];
            document.getElementById('stat-alpha').innerText = counts['Alpha'];
        }

        window.setAll = function(status) {
            document.querySelectorAll('input[data-type="' + status + '"]').forEach(radio => {
                radio.checked = true;
            });
            updateStats();
        }

        document.querySelectorAll('input[name^="attendance"][name$="[status]"]').forEach(radio => {
            radio.addEventListener('change', updateStats);
        });

        updateStats();
    });
</script>

<style>
    /* Square Radio Styles matches the main input view */
    input[type="radio"]:checked + div {
        transform: scale(1.05);
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
</style>
</x-layout.layout>

