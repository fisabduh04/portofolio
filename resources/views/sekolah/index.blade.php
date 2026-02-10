<x-layout.layout>
    {{-- Breadcrumb --}}
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Pengaturan', 'href' => '#'],
        ['name' => 'Data Sekolah', 'href' => route('sekolah.index')]
    ]" />

    <div class="mt-4">
        {{-- Header & Title (Simplified) --}}
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                {{-- Removed Redundant "Pengaturan Sekolah" H2 --}}
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola identitas, lokasi, dan branding visual sekolah.
                </p>
            </div>
            <div class="mt-4 sm:ml-4 sm:mt-0">
                <x-btn type="submit" form="schoolForm" color="blue" icon="check">
                    Simpan Perubahan
                </x-btn>
            </div>
        </div>

        @if(session('success'))
            <x-layout.toast type="success" :message="session('success')" />
        @endif

        {{-- Main Card --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm">
            
            {{-- Professional Tabs Navigation --}}
            <div class="border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="nav-item inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500 group" id="profile-tab" data-target="section-profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                            <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 11 14H9a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 10 19Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            Profil & Identitas
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="nav-item inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all text-gray-500 dark:text-gray-400 group" id="location-tab" data-target="section-location" type="button" role="tab" aria-controls="location" aria-selected="false">
                            <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
                            </svg>
                            Lokasi & Peta
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="nav-item inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all text-gray-500 dark:text-gray-400 group" id="legal-tab" data-target="section-legal" type="button" role="tab" aria-controls="legal" aria-selected="false">
                            <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 5h6m-6 4h6M10 3v4h4V3h-4Z"/>
                            </svg>
                            Legalitas
                        </button>
                    </li>
                    <li role="presentation">
                        <button class="nav-item inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all text-gray-500 dark:text-gray-400 group" id="branding-tab" data-target="section-branding" type="button" role="tab" aria-controls="branding" aria-selected="false">
                             <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.134 8.75 3.191-4.877a.89.89 0 0 1 1.488 0l3.078 4.704a2.804 2.804 0 0 0-4.053 4.295 2.804 2.804 0 0 0 4.053-4.294l3.125-4.776a.9.9 0 0 1 1.503 0l3.056 4.706C19.82 11.235 22 13.905 22 17a5 5 0 1 1-10 0 4.975 4.975 0 0 1-1.076-3.132A4.973 4.973 0 0 1 9.924 17 5 5 0 1 1 0 17c0-3.133 2.148-5.836 2.134-8.25Z"/>
                            </svg>
                            Media & Branding
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Form Content --}}
            <div class="p-6">
                <form id="schoolForm" action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- 1. SECTION: PROFILE --}}
                    <section id="section-profile" class="settings-section space-y-8 transition-opacity duration-300 ease-in-out opacity-100">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                
                                {{-- Nama Sekolah --}}
                                <div class="col-span-2 sm:col-span-1">
                                    <x-form.input name="nama_sekolah" label="Nama Resmi Sekolah" :value="$sekolah->nama_sekolah" required>
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                {{-- NPSN --}}
                                <div class="col-span-2 sm:col-span-1">
                                    <x-form.input name="npsn" label="NPSN" :value="$sekolah->npsn" maxlength="8" required>
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.5 1.5-1.5 1.5H7.5c-.75 0-1.5.5-1.5 1.5v1m3-1h3m-3 0v1m0 0h3m-3 0v2H6m9-10h.01M17 16h.01"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                {{-- NSS --}}
                                <div>
                                    <x-form.input name="nss" label="NSS" :value="$sekolah->nss">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                {{-- Status Sekolah --}}
                                <div>
                                    <label for="status_sekolah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Sekolah</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-500 dark:text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <select id="status_sekolah" name="status_sekolah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="Negeri" @selected($sekolah->status_sekolah == 'Negeri')>Negeri</option>
                                            <option value="Swasta" @selected($sekolah->status_sekolah == 'Swasta')>Swasta</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Bentuk Pendidikan --}}
                                <div>
                                    <x-form.input name="bentuk_pendidikan" label="Bentuk Pendidikan" :value="$sekolah->bentuk_pendidikan" placeholder="SMK/SMA/MA">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                {{-- Kepala Sekolah --}}
                                <div>
                                    <x-form.input name="kepala_sekolah" label="Nama Kepala Sekolah" :value="$sekolah->kepala_sekolah">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                            </div>

                             <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>
                            
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Kontak Official</h3>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <x-form.input type="email" name="email" label="Email Sekolah" :value="$sekolah->email">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input type="url" name="website" label="Website Resmi" :value="$sekolah->website" placeholder="https://">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="no_telp" label="Nomor Telepon" :value="$sekolah->no_telp">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="no_fax" label="Nomor Fax" :value="$sekolah->no_fax">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    {{-- 2. SECTION: LOCATION --}}
                    <section id="section-location" class="settings-section hidden space-y-8 transition-opacity duration-300 ease-in-out opacity-0">
                        <div>
                             <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Alamat Domisili</h3>
                            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                 <div class="col-span-2">
                                    <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Lengkap</label>
                                    <div class="relative">
                                         <div class="absolute top-3 left-0 flex items-center pl-3.5 pointer-events-none text-gray-500 dark:text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                        <textarea id="alamat" name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $sekolah->alamat }}</textarea>
                                    </div>
                                </div>
                                
                                <div>
                                    <x-form.input name="rt" label="RT" :value="$sekolah->rt">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="rw" label="RW" :value="$sekolah->rw">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="kode_pos" label="Kode Pos" :value="$sekolah->kode_pos">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                <div>
                                    <x-form.input name="kelurahan" label="Kelurahan/Desa" :value="$sekolah->kelurahan">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                <div>
                                    <x-form.input name="kecamatan" label="Kecamatan" :value="$sekolah->kecamatan">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="kabupaten" label="Kota/Kabupaten" :value="$sekolah->kabupaten">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                <div>
                                    <x-form.input name="provinsi" label="Provinsi" :value="$sekolah->provinsi">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>
                            
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Titik Koordinat</h3>
                             <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <x-form.input name="lintang" label="Lintang (Latitude)" :value="$sekolah->lintang" placeholder="-7.xxxxx">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="bujur" label="Bujur (Longitude)" :value="$sekolah->bujur" placeholder="112.xxxxx">
                                        <x-slot:icon>
                                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                            </div>
                        </div>
                    </section>
                    
                    {{-- 3. SECTION: LEGAL --}}
                    <section id="section-legal" class="settings-section hidden space-y-8 transition-opacity duration-300 ease-in-out opacity-0">
                         <div>
                             <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Dokumen Pendirian</h3>
                             <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <x-form.input name="sk_pendirian" label="Nomor SK Pendirian" :value="$sekolah->sk_pendirian">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                                
                                <div>
                                    <x-form.input type="date" name="tgl_sk_pendirian" label="Tanggal SK Pendirian" :value="$sekolah->tgl_sk_pendirian ? $sekolah->tgl_sk_pendirian->format('Y-m-d') : ''">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="sk_izin_operasional" label="SK Izin Operasional" :value="$sekolah->sk_izin_operasional">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>

                                <div>
                                    <x-form.input name="akreditasi" label="Akreditasi" :value="$sekolah->akreditasi" placeholder="Contoh: A (Unggul)">
                                        <x-slot:icon>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                        </x-slot:icon>
                                    </x-form.input>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 4. SECTION: BRANDING --}}
                    <section id="section-branding" class="settings-section hidden space-y-8 transition-opacity duration-300 ease-in-out opacity-0">
                         <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Media & Aset Visual</h3>
                           
                            <div>
                                <label class="block mb-2 text-sm font-medium leading-6 text-gray-900 dark:text-white">Logo Sekolah</label>
                                <div class="mt-2 flex items-center gap-x-6 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                                    <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-full bg-white ring-1 ring-gray-200 dark:ring-gray-700 flex items-center justify-center group shadow-sm">
                                        {{-- Dynamic Image Preview --}}
                                        <img id="preview-logo-img" src="{{ $sekolah->logo ? asset('storage/'.$sekolah->logo) : '' }}" class="{{ $sekolah->logo ? '' : 'hidden' }} h-full w-full object-contain z-10 relative">
                                        
                                        {{-- Placeholder --}}
                                        <div id="placeholder-logo" class="{{ $sekolah->logo ? 'hidden' : 'flex' }} items-center justify-center w-full h-full absolute inset-0 text-gray-400">
                                            <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        
                                        {{-- Overlay Edit Icon --}}
                                         <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center z-20 transition-all cursor-pointer" onclick="document.getElementById('logo_input').click()">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Upload Logo Baru</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-3">Format JPG, GIF atau PNG. Ukuran ideal 500x500px.</p>
                                        <x-btn type="button" onclick="document.getElementById('logo_input').click()" color="light" size="sm">Pilih File</x-btn>
                                        <input type="file" id="logo_input" name="logo" class="hidden" accept="image/*" onchange="handleImagePreview(this, 'preview-logo-img', 'placeholder-logo')">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block mb-2 text-sm font-medium leading-6 text-gray-900 dark:text-white">Kop Surat (Header Laporan)</label>
                                <div class="mt-2 flex justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 px-6 py-10 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer relative group bg-gray-50 dark:bg-gray-700/30" onclick="document.getElementById('kop_input').click()">
                                    <div class="text-center w-full">
                                        {{-- Preview Image --}}
                                        <img id="preview-kop-img" src="{{ $sekolah->kop_surat ? asset('storage/'.$sekolah->kop_surat) : '' }}" class="{{ $sekolah->kop_surat ? '' : 'hidden' }} mx-auto h-32 object-contain mb-2 rounded shadow-sm">

                                        {{-- Placeholder Content --}}
                                        <div id="placeholder-kop" class="{{ $sekolah->kop_surat ? 'hidden' : 'block' }}">
                                            <div class="mx-auto h-12 w-12 text-gray-300 mb-3">
                                                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="flex text-sm leading-6 text-gray-600 dark:text-gray-400 justify-center">
                                                <span class="relative rounded-md font-semibold text-blue-600 hover:text-blue-500 z-10">
                                                    <span>Upload file</span>
                                                </span>
                                                <p class="pl-1">atau drag and drop</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600 dark:text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                        
                                        {{-- "Click to change" hint when image exists --}}
                                        <p id="hint-kop" class="{{ $sekolah->kop_surat ? 'block' : 'hidden' }} text-xs text-center text-blue-500 mt-2 font-medium">Klik area ini untuk mengganti gambar</p>

                                        <input type="file" id="kop_input" name="kop_surat" class="hidden" accept="image/*" onchange="handleImagePreview(this, 'preview-kop-img', 'placeholder-kop', 'hint-kop')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </form>
            </div>
        </div>

         {{-- Mobile Sticky Save Button --}}
        <div class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg z-30 flex justify-end">
             <x-btn type="submit" form="schoolForm" color="blue" icon="check" class="w-full">
                Simpan Perubahan
            </x-btn>
        </div>
    </div>

    {{-- Script for Tab Functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navButtons = document.querySelectorAll('.nav-item');
            const sections = document.querySelectorAll('.settings-section');

            navButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    
                    // Update Nav State
                    navButtons.forEach(b => {
                        b.classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                        b.classList.add('border-transparent');
                        b.setAttribute('aria-selected', 'false');
                        // Reset icon colors in other tabs if needed
                        const icon = b.querySelector('svg');
                        if(icon) {
                            icon.classList.remove('text-blue-600', 'dark:text-blue-500');
                            icon.classList.add('text-gray-400', 'dark:text-gray-500');
                        }
                    });
                    
                    btn.classList.add('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                    btn.classList.remove('border-transparent');
                    btn.setAttribute('aria-selected', 'true');
                     // Update current icon color
                     const icon = btn.querySelector('svg');
                     if(icon) {
                         icon.classList.remove('text-gray-400', 'dark:text-gray-500');
                         icon.classList.add('text-blue-600', 'dark:text-blue-500');
                     }

                    // Show/Hide Sections with simple Class Toggling
                    sections.forEach(sec => {
                        if(sec.id === targetId) {
                            sec.classList.remove('hidden');
                            // Small delay to allow 'hidden' removal to register before adding opacity
                            setTimeout(() => {
                                sec.classList.remove('opacity-0');
                                sec.classList.add('opacity-100');
                            }, 50);
                        } else {
                            sec.classList.remove('opacity-100');
                            sec.classList.add('opacity-0');
                            // Wait for transition to finish before hiding
                            setTimeout(() => {
                                sec.classList.add('hidden');
                            }, 300); // Matches duration-300
                        }
                    });
                });
            });
        });

        function handleImagePreview(input, imgId, placeholderId, hintId = null) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(imgId);
                    const placeholder = document.getElementById(placeholderId);
                    
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                    
                    if (hintId) {
                        const hint = document.getElementById(hintId);
                        if (hint) hint.classList.remove('hidden');
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layout.layout>
