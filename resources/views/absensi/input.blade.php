<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Presensi', 'href' => '#'],
        ['name' => 'Presensi Siswa', 'href' => route('jadwal.presensiHarian')],
        ['name' => 'Input Presensi', 'href' => '#'],
    ]" />

    <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="absensiForm">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
        <input type="hidden" name="redirect_to" value="{{ old('redirect_to', url()->previous()) }}">
        <input type="hidden" name="kategori" value="{{ $kategori }}">
        <input type="hidden" name="tanggal" value="{{ $date }}">

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mb-6 p-4 text-sm text-red-800 border border-red-300 rounded-2xl bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
            <div class="flex items-center gap-2 font-bold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Terdapat kesalahan pada input Anda:
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Session Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 md:p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                             <h2 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight">{{ $jadwal->mapel->mapel }}</h2>
                             @if(isset($existingLogbook))
                                <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-md border border-amber-200 uppercase tracking-wider w-fit">Mode Edit</span>
                             @endif
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                             {{-- Date Picker --}}
                             <div class="w-full sm:w-auto relative mb-2 sm:mb-0">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="date" id="inputDate" value="{{ $date }}" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-pointer shadow-sm" 
                                    onchange="changeDate(this.value)">
                             </div>
                             
                             <div class="hidden sm:block w-px h-3 bg-gray-300 dark:bg-gray-600"></div>

                            <span class="flex items-center gap-1 w-full sm:w-auto">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="truncate">{{ $jadwal->kelas->kelas }}</span>
                            </span>
                            <span class="flex items-center gap-1 w-full sm:w-auto mt-1 sm:mt-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="truncate">{{ $jadwal->pegawai->name }}</span>
                            </span>
                             <span class="flex items-center gap-1 text-blue-600 font-medium w-full sm:w-auto mt-1 sm:mt-0">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $jadwal->mulai }} - {{ $jadwal->akhir }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto mt-4 md:mt-0">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-bold w-full sm:w-auto">
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
                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-md border border-purple-200">
                            P: <span id="stat-pulang">0</span>
                        </span>
                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-md border border-indigo-200">
                            T: <span id="stat-telat">0</span>
                        </span>
                    </div>
                    <button type="button" onclick="setAll('Hadir')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800 transition-colors whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Semua Hadir
                    </button>
                </div>
            </div>
        </div>

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
                    <div>
                        <table class="w-full text-left border-collapse">
                            <thead class="hidden md:table-header-group bg-gray-50/50 dark:bg-gray-700/50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold rounded-tl-xl">Siswa</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                                    <th class="px-6 py-4 font-semibold rounded-tr-xl">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group space-y-4 md:space-y-0 divide-y md:divide-gray-100 dark:md:divide-gray-700">
                                @php
                                    $attendanceMap = isset($existingLogbook) 
                                        ? $existingLogbook->absensis->keyBy('siswa_id') 
                                        : collect([]);
                                @endphp

                                @foreach($students as $siswa)
                                @php
                                    $existingStatus = $attendanceMap[$siswa->id]->status ?? null;
                                    $existingNote = $attendanceMap[$siswa->id]->keterangan ?? '';
                                @endphp
                                <tr class="block md:table-row bg-white dark:bg-gray-800 border md:border-none border-gray-100 dark:border-gray-700 rounded-xl shadow-sm md:shadow-none p-4 md:p-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="block md:table-cell px-2 py-2 md:px-6 md:py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-700 dark:text-blue-400 font-bold text-sm flex-shrink-0">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $siswa->nama }}</div>
                                                <div class="text-xs text-gray-500">{{ $siswa->nipd }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="block md:table-cell px-2 md:px-6 md:py-4 border-none dark:border-gray-700 mt-0">
                                        <div class="flex items-center justify-start md:justify-center">
                                            <div class="flex flex-wrap md:flex-nowrap gap-2">
                                                @foreach(['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpha' => 'A', 'Pulang' => 'P', 'Telat' => 'T'] as $label => $short)
                                                <label class="cursor-pointer group">
                                                    <input type="radio" 
                                                           name="attendance[{{ $siswa->id }}][status]" 
                                                           value="{{ $label }}" 
                                                           class="hidden peer"
                                                           data-type="{{ $label }}"
                                                           {{ $existingStatus == $label ? 'checked' : '' }}>
                                                    <div class="w-10 h-10 rounded-full flex shrink-0 items-center justify-center border-2 transition-all
                                                        @if($label == 'Hadir') peer-checked:bg-emerald-500 peer-checked:border-emerald-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-emerald-200
                                                        @elseif($label == 'Sakit') peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-amber-200
                                                        @elseif($label == 'Izin') peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-blue-200
                                                        @elseif($label == 'Alpha') peer-checked:bg-rose-500 peer-checked:border-rose-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-rose-200
                                                        @elseif($label == 'Pulang') peer-checked:bg-purple-500 peer-checked:border-purple-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-purple-200
                                                        @elseif($label == 'Telat') peer-checked:bg-indigo-500 peer-checked:border-indigo-500 peer-checked:text-white border-gray-100 dark:border-gray-700 text-gray-400 group-hover:border-indigo-200
                                                        @endif">
                                                        <span class="text-xs font-bold">{{ $short }}</span>
                                                    </div>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="block md:table-cell px-2 py-2 md:px-6 md:py-4 border-t border-gray-50 md:border-none dark:border-gray-700 mt-2 md:mt-0">
                                        <input type="text" 
                                               name="attendance[{{ $siswa->id }}][keterangan]" 
                                               value="{{ $existingNote }}"
                                               placeholder="Catatan (Opsional)..." 
                                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-blue-500 placeholder-gray-400">
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
                        Jurnal Pembelajaran
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Materi Pembelajaran</label>
                            <input type="text" name="materi" required 
                                   value="{{ old('materi', $existingLogbook->materi ?? '') }}"
                                   placeholder="Contoh: Operasi Hitung Campuran" 
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Kejadian Class</label>
                            <textarea name="catatan" rows="4" placeholder="Ketik kejadian penting selama KBM..." 
                                      class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan', $existingLogbook->catatan ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Documentation Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 012 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Dokumentasi Peserta (KBM)
                    </h3>
                    
                    @if(isset($existingLogbook) && $existingLogbook->foto)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Tersimpan:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(json_decode($existingLogbook->foto) as $foto)
                            <div class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200 group shadow-sm hover:scale-105 transition-transform" id="saved-photo-{{ md5($foto) }}">
                                <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-cover cursor-pointer" onclick="openGlobalLightbox('{{ asset('storage/' . $foto) }}')">
                                <button type="button" onclick="deleteSavedPhoto('{{ $foto }}', 'saved-photo-{{ md5($foto) }}')" class="absolute top-1 right-1 bg-red-600/80 hover:bg-red-700 text-white rounded-full p-1 shadow transition-colors z-10 cursor-pointer" title="Hapus foto dari database">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="deleted-photos-container"></div>
                    </div>
                    @endif

                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG atau JPEG (Max. 2MB)</p>
                                <p class="mt-2 text-xs text-blue-500 font-medium bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded inline-block">
                                    <span class="hidden sm:inline">Tips: Tahan <strong>CTRL</strong> untuk pilih banyak</span>
                                    <span class="sm:hidden">Tips: Bisa pilih banyak foto sekaligus</span>
                                </p>
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
                    Simpan Presensi & Jurnal
                </button>
            </div>
        </div>
    </form>

<script>
    // Selected files array to handle files before uploading
    let selectedFiles = [];

    // Define function globally
    window.handleFiles = function(files) {
        const previewContainer = document.getElementById('image-preview-container');
        if (!previewContainer) return;

        if (files && files.length > 0) {
            Array.from(files).forEach(file => {
                if (file.type.match('image.*')) {
                    // Check duplicate
                    const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
                    if (!isDuplicate) {
                        selectedFiles.push(file);
                    }
                }
            });
        }

        renderPreviews();
        updateFileInput();
    }

    function renderPreviews() {
        const previewContainer = document.getElementById('image-preview-container');
        if (!previewContainer) return;

        previewContainer.innerHTML = ''; // Clear previous previews

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm group';
                
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover cursor-pointer" onclick="openGlobalLightbox('${e.target.result}')">
                    <button type="button" onclick="removeSelectedFile(${index})" class="absolute top-1.5 right-1.5 bg-red-600/80 hover:bg-red-700 text-white rounded-full p-1 shadow transition-colors z-10 cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-1.5 pointer-events-none">
                        <span class="text-white text-[10px] font-medium truncate w-full">${(file.size/1024).toFixed(1)} KB</span>
                    </div>
                `;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }

    window.removeSelectedFile = function(index) {
        selectedFiles.splice(index, 1);
        renderPreviews();
        updateFileInput();
    }

    function updateFileInput() {
        const fileInput = document.getElementById('dropzone-file');
        if (!fileInput) return;
        
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function changeDate(newDate) {
        const url = new URL(window.location.href);
        url.searchParams.set('date', newDate);
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Function to update attendance statistics
        function updateStats() {
            const counts = {
                'Hadir': 0,
                'Sakit': 0,
                'Izin': 0,
                'Alpha': 0,
                'Pulang': 0,
                'Telat': 0
            };

            // Count checked radios
            document.querySelectorAll('input[name^="attendance"][name$="[status]"]:checked').forEach(radio => {
                const status = radio.value;
                if (counts.hasOwnProperty(status)) {
                    counts[status]++;
                }
            });

            // Update DOM
            document.getElementById('stat-hadir').innerText = counts['Hadir'];
            document.getElementById('stat-sakit').innerText = counts['Sakit'];
            document.getElementById('stat-izin').innerText = counts['Izin'];
            document.getElementById('stat-alpha').innerText = counts['Alpha'];
            document.getElementById('stat-pulang').innerText = counts['Pulang'];
            document.getElementById('stat-telat').innerText = counts['Telat'];
        }

        // Expose setAll to window
        window.setAll = function(status) {
            document.querySelectorAll('input[data-type="' + status + '"]').forEach(radio => {
                radio.checked = true;
            });
            updateStats(); // Update immediately after bulk change
        }

        // Attach event listeners to all radio buttons
        document.querySelectorAll('input[name^="attendance"][name$="[status]"]').forEach(radio => {
            radio.addEventListener('change', updateStats);
        });

        // Initial update
        updateStats();
    });

    // Lightbox modal functions
    function openGlobalLightbox(src) {
        const lightbox = document.getElementById('globalLightbox');
        const img = document.getElementById('lightboxImage');
        if (lightbox && img) {
            img.src = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightbox.classList.add('opacity-100');
            }, 10);
        }
    }

    function closeGlobalLightbox() {
        const lightbox = document.getElementById('globalLightbox');
        if (lightbox) {
            lightbox.classList.remove('opacity-100');
            lightbox.classList.add('opacity-0');
            setTimeout(() => {
                lightbox.classList.remove('flex');
                lightbox.classList.add('hidden');
            }, 300);
        }
    }

    // Delete saved photo handler
    window.deleteSavedPhoto = function(path, elementId) {
        showConfirmModal('Apakah Anda yakin ingin menghapus foto ini dari server?', function() {
            const element = document.getElementById(elementId);
            if (element) {
                element.remove();
            }
            
            const container = document.getElementById('deleted-photos-container');
            if (container) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_photos[]';
                input.value = path;
                container.appendChild(input);
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGlobalLightbox();
        }
    });
</script>

<!-- Global Lightbox Modal -->
<div id="globalLightbox" class="fixed inset-0 z-[9999] hidden flex-col items-center justify-center bg-black/90 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeGlobalLightbox()">
    <div class="absolute top-4 right-4 flex items-center gap-3">
        <button onclick="closeGlobalLightbox()" class="text-white hover:text-gray-300 p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors focus:outline-none cursor-pointer" aria-label="Close Lightbox">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <div class="max-w-[90%] max-h-[85vh] relative" onclick="event.stopPropagation()">
        <img id="lightboxImage" src="" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain border border-white/10">
    </div>
</div>

</x-layout.layout>
