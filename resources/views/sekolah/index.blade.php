<x-layout.layout>
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Hero Header Section --}}
        <div class="relative w-full h-48 rounded-3xl overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg">
             {{-- Pattern Overlay --}}
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            
            <div class="absolute bottom-0 left-0 w-full p-6 md:p-8 flex items-end justify-between bg-gradient-to-t from-black/60 to-transparent">
                <div class="flex items-center gap-6">
                    <div class="relative w-24 h-24 rounded-2xl bg-white p-1 shadow-xl -mb-12 border-4 border-white dark:border-gray-800">
                        @if($sekolah->logo)
                            <img src="{{ asset('storage/'.$sekolah->logo) }}" class="w-full h-full object-contain rounded-xl">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-50 rounded-xl">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div class="mb-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">{{ $sekolah->nama_sekolah ?? 'Nama Sekolah Belum Diisi' }}</h1>
                        <p class="text-blue-100 text-sm md:text-base font-medium flex items-center gap-2">
                            <span>{{ $sekolah->npsn ?? 'NPSN Kosong' }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-300"></span>
                            <span>{{ $sekolah->kabupaten ?? 'Lokasi Belum Diisi' }}</span>
                        </p>
                    </div>
                </div>
                
                <div class="hidden md:block mb-1">
                     <button type="submit" form="schoolForm" class="inline-flex items-center px-6 py-3 text-sm font-bold text-blue-700 bg-white rounded-xl hover:bg-blue-50 focus:ring-4 focus:ring-blue-300 shadow-xl transition-all transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Save Button (Sticky) --}}
        <div class="md:hidden sticky top-4 z-30 flex justify-end">
            <button type="submit" form="schoolForm" class="inline-flex items-center px-5 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-full shadow-lg shadow-blue-500/40 hover:bg-blue-700 backdrop-blur-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan
            </button>
        </div>

        @if(session('success'))
            <div class="flex p-4 mb-4 text-sm text-green-800 rounded-2xl bg-green-50/50 border border-green-100 backdrop-blur-sm dark:bg-gray-800/50 dark:text-green-400 dark:border-green-900/30 shadow-sm" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div><span class="font-bold">Berhasil Disimpan!</span> Perubahan data sekolah telah diperbarui.</div>
            </div>
        @endif

        {{-- Settings Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pt-6">
            
            {{-- Internal Navigation --}}
            <aside class="lg:col-span-3">
                <nav class="space-y-1 sticky top-8" id="settings-nav">
                    <button type="button" data-target="section-profile" class="nav-item w-full flex items-center px-4 py-3.5 text-sm font-bold rounded-2xl transition-all duration-200 bg-white text-blue-600 shadow-sm border border-gray-100 ring-1 ring-black/5 group">
                        <span class="w-8 h-8 rounded-lg bg-blue-100/50 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </span>
                        Profil & Identitas
                    </button>
                    <button type="button" data-target="section-location" class="nav-item w-full flex items-center px-4 py-3.5 text-sm font-medium rounded-2xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 group">
                        <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:shadow-sm transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        Lokasi & Peta
                    </button>
                    <button type="button" data-target="section-legal" class="nav-item w-full flex items-center px-4 py-3.5 text-sm font-medium rounded-2xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 group">
                         <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                         </span>
                        Legalitas
                    </button>
                    <button type="button" data-target="section-branding" class="nav-item w-full flex items-center px-4 py-3.5 text-sm font-medium rounded-2xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 group">
                         <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center mr-3 group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                         </span>
                        Media & Branding
                    </button>
                </nav>
            </aside>

            {{-- Main Form --}}
            <main class="lg:col-span-9">
                <form id="schoolForm" action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    {{-- 1. SECTION: PROFILE --}}
                    <section id="section-profile" class="settings-section space-y-6">
                        
                        {{-- Card Identitas --}}
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
                            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/30">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Identitas Utama</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">Data wajib sesuai Dapodik.</p>
                                </div>
                                <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Primary
                                </span>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <div class="col-span-2">
                                    <x-form.input label="Nama Resmi Sekolah" name="nama_sekolah" :value="$sekolah->nama_sekolah" required 
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                                    />
                                </div>
                                <x-form.input label="NPSN" name="npsn" :value="$sekolah->npsn" required maxlength="8" 
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>'
                                />
                                <x-form.input label="NSS" name="nss" :value="$sekolah->nss" />
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Status Sekolah</label>
                                    <select name="status_sekolah" class="bg-gray-50/50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 transition-all">
                                        <option value="Negeri" @selected($sekolah->status_sekolah == 'Negeri')>Negeri</option>
                                        <option value="Swasta" @selected($sekolah->status_sekolah == 'Swasta')>Swasta</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                     <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Bentuk Pendidikan</label>
                                     <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <input type="text" name="bentuk_pendidikan" value="{{ $sekolah->bentuk_pendidikan }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Contoh: SMK">
                                     </div>
                                </div>
                                <x-form.input label="Kepala Sekolah" name="kepala_sekolah" :value="$sekolah->kepala_sekolah" 
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                                />
                            </div>
                        </div>

                        {{-- Card Kontak --}}
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
                             <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kontak Official</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <x-form.input label="Email Sekolah" name="email" type="email" :value="$sekolah->email" placeholder="mail@sekolah.sch.id"
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'
                                />
                                <x-form.input label="Website Resmi" name="website" :value="$sekolah->website" placeholder="https://sekolah.sch.id"
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>'
                                />
                                <x-form.input label="No. Telepon" name="no_telp" :value="$sekolah->no_telp" placeholder="021-xxxxxx"
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>'
                                />
                                <x-form.input label="No. Fax" name="no_fax" :value="$sekolah->no_fax" 
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>'
                                />
                            </div>
                        </div>
                    </section>
                    
                    {{-- 2. SECTION: LOCATION --}}
                    <section id="section-location" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
                            <div class="grid grid-cols-1 lg:grid-cols-3">
                                {{-- Map Visual Placeholder --}}
                                <div class="bg-blue-50/50 p-6 flex flex-col justify-center items-center border-b lg:border-b-0 lg:border-r border-gray-100 relative overflow-hidden group">
                                     {{-- Background Pattern simulating map --}}
                                     <div class="absolute inset-0 opacity-20 bg-[url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg')] bg-cover bg-center"></div>
                                     <div class="relative z-10 text-center">
                                         <div class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center mx-auto mb-4 text-blue-600 animate-bounce">
                                             <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                         </div>
                                         <h4 class="font-bold text-gray-900">Titik Koordinat</h4>
                                         <p class="text-xs text-gray-500 mb-4 px-4">Pastikan pin lokasi akurat untuk keperluan zonasi.</p>
                                         
                                         <div class="space-y-3 w-full max-w-[200px] mx-auto">
                                             <input type="text" name="lintang" value="{{ $sekolah->lintang }}" class="text-center w-full text-xs font-mono bg-white border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Latitude (-7.xxx)">
                                             <input type="text" name="bujur" value="{{ $sekolah->bujur }}" class="text-center w-full text-xs font-mono bg-white border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Longitude (112.xxx)">
                                         </div>
                                     </div>
                                </div>
                                
                                {{-- Form Address --}}
                                <div class="lg:col-span-2 p-8 space-y-6">
                                     <h3 class="text-lg font-bold text-gray-900 border-b pb-4 mb-4">Alamat Domisili</h3>
                                     <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Jalan / Alamat Lengkap</label>
                                        <textarea name="alamat" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500 p-4 dark:bg-gray-700 dark:border-gray-600 transition-shadow focus:shadow-md">{{ $sekolah->alamat }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-form.input label="RT" name="rt" :value="$sekolah->rt" />
                                        <x-form.input label="RW" name="rw" :value="$sekolah->rw" />
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <x-form.input label="Kelurahan/Desa" name="kelurahan" :value="$sekolah->kelurahan" />
                                        <x-form.input label="Kecamatan" name="kecamatan" :value="$sekolah->kecamatan" />
                                        <x-form.input label="Kota/Kabupaten" name="kabupaten" :value="$sekolah->kabupaten" />
                                        <x-form.input label="Provinsi" name="provinsi" :value="$sekolah->provinsi" />
                                    </div>
                                    <x-form.input label="Kode Pos" name="kode_pos" :value="$sekolah->kode_pos" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>' />
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 3. SECTION: LEGAL --}}
                     <section id="section-legal" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
                             <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Dokumen Legalitas</h3>
                            </div>
                             <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <x-form.input label="SK Pendirian" name="sk_pendirian" :value="$sekolah->sk_pendirian" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' />
                                <x-form.input label="Tgl SK Pendirian" name="tgl_sk_pendirian" type="date" :value="$sekolah->tgl_sk_pendirian ? $sekolah->tgl_sk_pendirian->format('Y-m-d') : ''" />
                                <x-form.input label="SK Izin Operasional" name="sk_izin_operasional" :value="$sekolah->sk_izin_operasional" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' />
                                <x-form.input label="Akreditasi" name="akreditasi" :value="$sekolah->akreditasi" placeholder="Unggul / A" icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>' />
                             </div>
                        </div>
                    </section>

                    {{-- 4. SECTION: BRANDING --}}
                    <section id="section-branding" class="settings-section hidden space-y-6">
                        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
                            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aset Visual & Branding</h3>
                            </div>
                            <div class="p-8 space-y-10">
                                {{-- Logo Section --}}
                                <div class="flex flex-col md:flex-row gap-8 items-start">
                                    <div class="w-40 h-40 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group hover:border-blue-400 transition-colors">
                                        @if($sekolah->logo)
                                            <img id="prev_logo_sidebar" src="{{ asset('storage/'.$sekolah->logo) }}" class="w-full h-full object-contain p-2">
                                        @else
                                            <svg class="w-12 h-12 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <label for="logo_inp" class="cursor-pointer text-white text-xs font-bold uppercase tracking-wider bg-black/50 px-3 py-1 rounded-full backdrop-blur-sm">Ganti Logo</label>
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <h4 class="font-bold text-gray-900 dark:text-white text-lg">Logo Sekolah</h4>
                                        <p class="text-sm text-gray-500 leading-relaxed">
                                            Upload logo dengan format PNG (transparan) agar menyatu dengan background. 
                                            <br>Disarankan ukuran minimal <span class="font-mono text-xs bg-gray-100 px-1 py-0.5 rounded text-gray-700">512x512px</span>.
                                        </p>
                                        <div class="flex items-center gap-3 pt-2">
                                            <label for="logo_inp" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 cursor-pointer shadow-sm transition-all hover:shadow">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                Pilih File
                                            </label>
                                            <span id="logo_filename" class="text-xs text-gray-400">Tidak ada file dipilih</span>
                                        </div>
                                        <input type="file" id="logo_inp" name="logo" class="hidden" accept="image/*" onchange="previewImg(this, 'prev_logo_sidebar'); document.getElementById('logo_filename').innerText = this.files[0].name">
                                    </div>
                                </div>

                                {{-- Kop Surat Section --}}
                                <div class="space-y-4 pt-8 border-t border-gray-100">
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg dark:text-white">Header Kop Surat</h4>
                                            <p class="text-sm text-gray-500">Gambar ini akan menjadi header otomatis untuk semua laporan PDF.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full h-48 rounded-3xl bg-gray-50 border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden relative group hover:border-blue-400 transition-colors">
                                        @if($sekolah->kop_surat)
                                            <img id="prev_kop_sidebar" src="{{ asset('storage/'.$sekolah->kop_surat) }}" class="w-full h-full object-contain">
                                        @else
                                            <div id="kop_placeholder" class="text-center group-hover:scale-105 transition-transform duration-300">
                                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span class="text-sm text-gray-500 font-medium">Klik area ini untuk upload Kop Surat (Lebar)</span>
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
                        nb.classList.remove('bg-white', 'text-blue-600', 'shadow-sm', 'ring-1', 'ring-black/5');
                        nb.classList.add('text-gray-500', 'hover:text-gray-900', 'hover:bg-gray-50');
                        // Reset icon container bg
                        const iconContainer = nb.querySelector('span');
                        if(iconContainer) {
                             iconContainer.classList.remove('bg-blue-100/50', 'text-blue-600');
                             iconContainer.classList.add('bg-gray-100', 'text-gray-500');
                        }
                    });
                    
                    // Active Button Style
                    btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm', 'ring-1', 'ring-black/5');
                    btn.classList.remove('text-gray-500', 'hover:text-gray-900', 'hover:bg-gray-50');
                    
                    // Activ Icon Style
                    const activeIcon = btn.querySelector('span');
                    if(activeIcon) {
                        activeIcon.classList.remove('bg-gray-100', 'text-gray-500');
                        activeIcon.classList.add('bg-blue-100/50', 'text-blue-600');
                    }

                    // Switch Sections
                    const target = btn.dataset.target;
                    sections.forEach(sec => {
                        sec.classList.add('hidden');
                        if (sec.id === target) {
                            sec.classList.remove('hidden');
                            // Small animations for appearing section
                            sec.classList.add('animate-fade-in-up'); 
                        }
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
                    } else {
                        // Fallback if image tag doesn't exist (initially empty)
                         location.reload(); 
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    
    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-layout.layout>
