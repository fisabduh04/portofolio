<x-layout.layout>
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <x-breadcrumb :breadcrumbs="[
            ['name' => 'Home', 'href' => route('dashboard.index')],
            ['name' => 'Absensi', 'href' => route('absensi.index')],
            ['name' => 'Input Presensi', 'href' => '#'],
        ]" />
    </div>

    <form action="{{ route('absensi.store') }}" method="POST" enctype="multipart/form-data" id="absensiForm">
        @csrf
        <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">

        <!-- Session Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jadwal->mapel->mapel }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 mt-1">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                {{ $jadwal->kelas->kelas }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $jadwal->pegawai->name }}
                            </span>
                            <span class="flex items-center gap-1 text-primary-600 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $jadwal->mulai }} - {{ $jadwal->akhir }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="setAll('Hadir')" class="inline-flex items-center px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800 transition-colors">
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
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Siswa</th>
                                    <th class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                                    <th class="px-6 py-4 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($students as $siswa)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-sm">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $siswa->nama }}</div>
                                                <div class="text-xs text-gray-500">{{ $siswa->nipd }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            @foreach(['Hadir' => 'H', 'Sakit' => 'S', 'Izin' => 'I', 'Alpha' => 'A'] as $label => $short)
                                            <label class="cursor-pointer group">
                                                <input type="radio" 
                                                       name="attendance[{{ $siswa->id }}][status]" 
                                                       value="{{ $label }}" 
                                                       class="hidden peer"
                                                       data-type="{{ $label }}"
                                                       {{ $loop->first ? 'checked' : '' }}>
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
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" 
                                               name="attendance[{{ $siswa->id }}][keterangan]" 
                                               placeholder="Catatan..." 
                                               class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
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
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Jurnal Pembelajaran
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Materi Pembelajaran</label>
                            <input type="text" name="materi" required placeholder="Contoh: Operasi Hitung Campuran" 
                                   class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Kejadian Class</label>
                            <textarea name="catatan" rows="4" placeholder="Ketik kejadian penting selama KBM..." 
                                      class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Documentation Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 012 2H5a2 2 0 01-2-2V9z"></path><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Dokumentasi Peserta (KBM)
                    </h3>
                    <div class="flex items-center justify-center w-full">
                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG atau JPEG (Max. 2MB)</p>
                            </div>
                            <input id="dropzone-file" type="file" name="foto[]" multiple class="hidden" onchange="handleFiles(this.files)" />
                        </label>
                    </div>
                    <!-- Preview Container -->
                    <div id="image-preview-container" class="grid grid-cols-3 gap-2 mt-4"></div>
                </div>

                <!-- Submit Action -->
                <button type="submit" class="w-full flex items-center justify-center px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2-2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Presensi & Jurnal
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function setAll(status) {
        document.querySelectorAll('input[data-type="' + status + '"]').forEach(radio => {
            radio.checked = true;
        });
    }

    function handleFiles(files) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Premium Color Tokens - If Tailwind v4 is used these can be mapped, otherwise vanilla CSS fallback */
    :root {
        --color-primary-50: #f0f9ff;
        --color-primary-600: #0284c7;
        --color-primary-700: #0369a1;
    }
    .text-primary-600 { color: var(--color-primary-600); }
    .bg-primary-600 { background-color: var(--color-primary-600); }
    .bg-primary-50 { background-color: var(--color-primary-50); }
    .border-primary-500:focus { border-color: #0ea5e9; }
    .ring-primary-500:focus { --tw-ring-color: #0ea5e9; }
</style>
@endpush
</x-layout.layout>
