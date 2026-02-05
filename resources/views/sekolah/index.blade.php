<x-layout.layout>
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <x-breadcrumb :breadcrumbs="[
                    ['name' => 'Home', 'href' => route('dashboard.index')],
                    ['name' => 'Data Induk', 'href' => '#'],
                    ['name' => 'Data Sekolah', 'href' => route('sekolah.index')],
                ]" />
                <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-heading">Profil Sekolah</h1>
                <p class="text-body-secondary mt-1">Kelola identitas resmi, legalitas, dan branding sekolah dalam satu tempat.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" form="schoolForm" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="flex p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50/50 border border-green-100 backdrop-blur-sm dark:bg-gray-800/50 dark:text-green-400 dark:border-green-900/30" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Success</span>
                <div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div>
            </div>
        @endif

        {{-- Main Settings Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Internal Navigation (Left Sidebar) --}}
            <aside class="lg:col-span-3">
                <nav class="space-y-1 sticky top-8" id="settings-nav">
                    <button type="button" data-target="section-profile" class="nav-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 bg-blue-50 text-blue-700 border border-blue-100 group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Profil & Identitas
                    </button>
                    <button type="button" data-target="section-location" class="nav-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lokasi & Alamat
                    </button>
                    <button type="button" data-target="section-legal" class="nav-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Legalitas
                    </button>
                    <button type="button" data-target="section-branding" class="nav-item w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Media & Branding
                    </button>
                </nav>
            </aside>

            {{-- Content Area --}}
            <main class="lg:col-span-9">
                <form id="schoolForm" action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    {{-- 1. SECTION: PROFILE --}}
                    <section id="section-profile" class="settings-section space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Identitas Utama</h3>
                                <p class="text-sm text-gray-500 mt-1">Informasi dasar identitas sekolah sesuai Dapodik.</p>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <x-form.input label="Nama Resmi Sekolah" name="nama_sekolah" :value="$sekolah->nama_sekolah" required placeholder="Contoh: SMK AL-MIFTAH" />
                                <x-form.input label="NPSN (8 Digit)" name="npsn" :value="$sekolah->npsn" required maxlength="8" placeholder="12345678" />
                                <x-form.input label="NSS" name="nss" :value="$sekolah->nss" placeholder="Nomor Statistik Sekolah" />
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Status Sekolah</label>
                                    <select name="status_sekolah" class="bg-gray-50/50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600">
                                        <option value="Negeri" @selected($sekolah->status_sekolah == 'Negeri')>Negeri</option>
                                        <option value="Swasta" @selected($sekolah->status_sekolah == 'Swasta')>Swasta</option>
                                    </select>
                                </div>
                                <x-form.input label="Bentuk Pendidikan" name="bentuk_pendidikan" :value="$sekolah->bentuk_pendidikan" placeholder="Misal: SMK, SMA, SMP" />
                                <x-form.input label="Nama Kepala Sekolah" name="kepala_sekolah" :value="$sekolah->kepala_sekolah" />
                            </div>
                        </div>

                        {{-- Kontak Card --}}
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Kontak Official</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <x-form.input label="Email Sekolah" name="email" type="email" :value="$sekolah->email" placeholder="school@email.com" />
                                <x-form.input label="Website" name="website" :value="$sekolah->website" placeholder="https://www.school.sch.id" />
                                <x-form.input label="No. Telepon" name="no_telp" :value="$sekolah->no_telp" />
                                <x-form.input label="No. Fax" name="no_fax" :value="$sekolah->no_fax" />
                            </div>
                        </div>
                    </section>

                    {{-- 2. SECTION: LOCATION --}}
                    <section id="section-location" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Domisili & Geografis</h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="3" class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl text-sm focus:ring-blue-500 focus:border-blue-500 p-4 dark:bg-gray-700 dark:border-gray-600">{{ $sekolah->alamat }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <x-form.input label="RT" name="rt" :value="$sekolah->rt" />
                                    <x-form.input label="RW" name="rw" :value="$sekolah->rw" />
                                    <div class="col-span-2">
                                        <x-form.input label="Kelurahan" name="kelurahan" :value="$sekolah->kelurahan" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-form.input label="Kecamatan" name="kecamatan" :value="$sekolah->kecamatan" />
                                    <x-form.input label="Kota/Kabupaten" name="kabupaten" :value="$sekolah->kabupaten" />
                                    <x-form.input label="Provinsi" name="provinsi" :value="$sekolah->provinsi" />
                                    <x-form.input label="Kode Pos" name="kode_pos" :value="$sekolah->kode_pos" />
                                </div>
                                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100 flex gap-4">
                                     <svg class="w-10 h-10 text-blue-500 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                     <div>
                                         <h4 class="font-bold text-blue-900">Koordinat Peta</h4>
                                         <p class="text-xs text-blue-700 mb-4">Gunakan Google Maps untuk mendapatkan titik presisi.</p>
                                         <div class="grid grid-cols-2 gap-4">
                                            <x-form.input label="Lintang (Lat)" name="lintang" :value="$sekolah->lintang" placeholder="-7.xxx" />
                                            <x-form.input label="Bujur (Lng)" name="bujur" :value="$sekolah->bujur" placeholder="112.xxx" />
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 3. SECTION: LEGAL --}}
                    <section id="section-legal" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                             <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Legalitas Operasional</h3>
                            </div>
                             <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <x-form.input label="SK Pendirian" name="sk_pendirian" :value="$sekolah->sk_pendirian" />
                                <x-form.input label="Tgl SK Pendirian" name="tgl_sk_pendirian" type="date" :value="$sekolah->tgl_sk_pendirian ? $sekolah->tgl_sk_pendirian->format('Y-m-d') : ''" />
                                <x-form.input label="SK Izin Operasional" name="sk_izin_operasional" :value="$sekolah->sk_izin_operasional" />
                                <x-form.input label="Akreditasi" name="akreditasi" :value="$sekolah->akreditasi" placeholder="Unggul / A" />
                             </div>
                        </div>
                    </section>

                    {{-- 4. SECTION: BRANDING --}}
                    <section id="section-branding" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Media & Logo</h3>
                            </div>
                            <div class="p-8 space-y-8">
                                {{-- Logo Section --}}
                                <div class="flex flex-col md:flex-row gap-8 items-start">
                                    <div class="w-40 h-40 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                        @if($sekolah->logo)
                                            <img id="prev_logo_sidebar" src="{{ asset('storage/'.$sekolah->logo) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <label for="logo_inp" class="cursor-pointer text-white text-xs font-bold uppercase tracking-wider">Ubah</label>
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <h4 class="font-bold text-gray-900 dark:text-white">Logo Sekolah</h4>
                                        <p class="text-xs text-gray-500">Gunakan file PNG transparan minimal 512x512px untuk hasil terbaik di sertifikat dan dashboard.</p>
                                        <input type="file" id="logo_inp" name="logo" class="hidden" accept="image/*" onchange="previewImg(this, 'prev_logo_sidebar')">
                                        <label for="logo_inp" class="inline-block px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold hover:bg-gray-50 cursor-pointer transition-all">Pilih File...</label>
                                    </div>
                                </div>

                                {{-- Kop Surat Section --}}
                                <div class="space-y-4 pt-8 border-t border-gray-50">
                                    <h4 class="font-bold text-gray-900 dark:text-white">Header Kop Surat</h4>
                                    <p class="text-xs text-gray-500">Header ini akan tampil otomatis di semua laporan PDF dan surat resmi.</p>
                                    <div class="w-full h-48 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                        @if($sekolah->kop_surat)
                                            <img id="prev_kop_sidebar" src="{{ asset('storage/'.$sekolah->kop_surat) }}" class="w-full h-full object-contain">
                                        @else
                                            <div id="kop_placeholder" class="text-center group-hover:scale-105 transition-transform duration-300">
                                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3-3m3 3V4"></path></svg>
                                                <span class="text-xs text-gray-400 font-medium">Klik untuk upload Kop Surat</span>
                                            </div>
                                        @endif
                                        <input type="file" id="kop_inp" name="kop_surat" class="hidden" accept="image/*" onchange="previewImg(this, 'prev_kop_sidebar')">
                                        <label for="kop_inp" class="absolute inset-0 cursor-pointer"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
            </main>
        </div>
    </div>

    {{-- Script for Navigation & Preview --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navButtons = document.querySelectorAll('.nav-item');
            const sections = document.querySelectorAll('.settings-section');

            navButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Reset Buttons
                    navButtons.forEach(nb => {
                        nb.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-100');
                        nb.classList.add('text-gray-600', 'hover:bg-gray-50');
                    });
                    // Active Button
                    btn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-100');
                    btn.classList.remove('text-gray-600', 'hover:bg-gray-50');

                    // Switch Sections
                    const target = btn.dataset.target;
                    sections.forEach(sec => {
                        sec.classList.add('hidden');
                        if (sec.id === target) sec.classList.remove('hidden');
                    });
                });
            });
        });

        function previewImg(input, imgId) {
            const preview = document.getElementById(imgId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        
                        // Hide placeholder if it exists
                        const placeholder = document.getElementById('kop_placeholder');
                        if (placeholder && imgId === 'prev_kop_sidebar') {
                            placeholder.classList.add('hidden');
                        }
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layout.layout>
