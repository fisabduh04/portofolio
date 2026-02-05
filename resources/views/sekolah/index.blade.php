<x-layout.layout>
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Data Induk', 'href' => '#'],
                ['name' => 'Data Sekolah', 'href' => route('sekolah.index')],
            ]" />
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-heading">Data Sekolah</h1>
            <p class="text-body-secondary">Kelola identitas utama sekolah, alamat, kontak, dan media.</p>
        </div>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        {{-- Form Start --}}
        <form action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Tabs Header --}}
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="sekolahTabs" data-tabs-toggle="#sekolahTabsContent" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="identitas-tab" data-tabs-target="#identitas" type="button" role="tab" aria-controls="identitas" aria-selected="false">Identitas</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="alamat-tab" data-tabs-target="#alamat" type="button" role="tab" aria-controls="alamat" aria-selected="false">Lokasi & Alamat</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="media-tab" data-tabs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false">Logo & Kop Surat</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="legalitas-tab" data-tabs-target="#legalitas" type="button" role="tab" aria-controls="legalitas" aria-selected="false">Legalitas & Sanitasi</button>
                    </li>
                </ul>
            </div>

            {{-- Tabs Content --}}
            <div id="sekolahTabsContent">
                
                {{-- TAB: IDENTITAS --}}
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="identitas" role="tabpanel" aria-labelledby="identitas-tab">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                             <x-form.input label="Nama Sekolah" name="nama_sekolah" :value="$sekolah->nama_sekolah" required />
                             <x-form.input label="NPSN" name="npsn" :value="$sekolah->npsn" required maxlength="8" />
                             <x-form.input label="NSS" name="nss" :value="$sekolah->nss" />
                             <div>
                                <label for="status_sekolah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Sekolah</label>
                                <select id="status_sekolah" name="status_sekolah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="Negeri" @selected($sekolah->status_sekolah == 'Negeri')>Negeri</option>
                                    <option value="Swasta" @selected($sekolah->status_sekolah == 'Swasta')>Swasta</option>
                                </select>
                             </div>
                             <x-form.input label="Bentuk Pendidikan" name="bentuk_pendidikan" :value="$sekolah->bentuk_pendidikan" placeholder="Contoh: SMK" />
                        </div>
                        <div class="space-y-4">
                            <x-form.input label="Email Sekolah" name="email" type="email" :value="$sekolah->email" />
                            <x-form.input label="Website" name="website" :value="$sekolah->website" />
                            <x-form.input label="No. Telepon" name="no_telp" :value="$sekolah->no_telp" />
                            <x-form.input label="No. Fax" name="no_fax" :value="$sekolah->no_fax" />
                            <x-form.input label="Nama Kepala Sekolah" name="kepala_sekolah" :value="$sekolah->kepala_sekolah" />
                            <x-form.input label="NIP Kepala Sekolah" name="nip_kepala_sekolah" :value="$sekolah->nip_kepala_sekolah" />
                        </div>
                    </div>
                </div>

                {{-- TAB: ALAMAT --}}
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                     <div class="grid gap-6 mb-6 md:grid-cols-2">
                         <div class="col-span-2">
                            <label for="alamat_text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Lengkap (Jalan)</label>
                            <textarea id="alamat_text" name="alamat" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $sekolah->alamat }}</textarea>
                         </div>
                         <div class="grid grid-cols-2 gap-4">
                            <x-form.input label="RT" name="rt" :value="$sekolah->rt" />
                            <x-form.input label="RW" name="rw" :value="$sekolah->rw" />
                         </div>
                         <x-form.input label="Dusun" name="dusun" :value="$sekolah->dusun" />
                         <x-form.input label="Kelurahan / Desa" name="kelurahan" :value="$sekolah->kelurahan" />
                         <x-form.input label="Kecamatan" name="kecamatan" :value="$sekolah->kecamatan" />
                         <x-form.input label="Kabupaten / Kota" name="kabupaten" :value="$sekolah->kabupaten" />
                         <x-form.input label="Provinsi" name="provinsi" :value="$sekolah->provinsi" />
                         <x-form.input label="Kode Pos" name="kode_pos" :value="$sekolah->kode_pos" />
                         <x-form.input label="Lintang" name="lintang" :value="$sekolah->lintang" />
                         <x-form.input label="Bujur" name="bujur" :value="$sekolah->bujur" />
                     </div>
                </div>

                {{-- TAB: MEDIA (LOGO & KOP SURAT) --}}
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="media" role="tabpanel" aria-labelledby="media-tab">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Logo Upload --}}
                        <div class="space-y-2">
                             <label class="block text-sm font-medium text-gray-900 dark:text-white">Logo Sekolah</label>
                             <div class="flex items-center justify-center w-full relative">
                                <label for="logo_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 overflow-hidden relative group border-gray-300">
                                    
                                    {{-- Image Preview Container --}}
                                    <div class="absolute inset-0 flex items-center justify-center z-0 p-4">
                                        @if($sekolah->logo)
                                            <img id="preview_logo" src="{{ asset('storage/'.$sekolah->logo) }}" class="max-h-full object-contain" alt="Logo Preview">
                                        @else
                                            <img id="preview_logo" src="" class="hidden max-h-full object-contain" alt="Logo Preview">
                                            <div id="placeholder_logo" class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik upload</span></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG (MAX. 2MB)</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input id="logo_file" name="logo" type="file" class="hidden" accept="image/*" onchange="previewImage(this, 'preview_logo', 'placeholder_logo')" />
                                </label>
                            </div>
                        </div>

                        {{-- Kop Surat Upload --}}
                        <div class="space-y-2">
                             <label class="block text-sm font-medium text-gray-900 dark:text-white">Kop Surat (Untuk Cetak)</label>
                             <div class="flex items-center justify-center w-full relative">
                                <label for="kop_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 overflow-hidden relative group border-gray-300">
                                    
                                     {{-- Image Preview Container --}}
                                    <div class="absolute inset-0 flex items-center justify-center z-0 p-4">
                                        @if($sekolah->kop_surat)
                                            <img id="preview_kop" src="{{ asset('storage/'.$sekolah->kop_surat) }}" class="max-h-full w-full object-contain" alt="Kop Surat Preview">
                                        @else
                                            <img id="preview_kop" src="" class="hidden max-h-full w-full object-contain" alt="Kop Surat Preview">
                                            <div id="placeholder_kop" class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik upload</span></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG (Lebar)</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input id="kop_file" name="kop_surat" type="file" class="hidden" accept="image/*" onchange="previewImage(this, 'preview_kop', 'placeholder_kop')" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: LEGALITAS & SANITASI --}}
                <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="legalitas" role="tabpanel" aria-labelledby="legalitas-tab">
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="space-y-4">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2">Legalitas</h4>
                            <x-form.input label="SK Pendirian" name="sk_pendirian" :value="$sekolah->sk_pendirian" />
                             <x-form.input label="Tanggal SK Pendirian" name="tgl_sk_pendirian" type="date" :value="$sekolah->tgl_sk_pendirian ? $sekolah->tgl_sk_pendirian->format('Y-m-d') : ''" />
                            <x-form.input label="SK Izin Operasional" name="sk_izin_operasional" :value="$sekolah->sk_izin_operasional" />
                             <x-form.input label="Tanggal SK Izin Ops" name="tgl_sk_izin_operasional" type="date" :value="$sekolah->tgl_sk_izin_operasional ? $sekolah->tgl_sk_izin_operasional->format('Y-m-d') : ''" />
                            <x-form.input label="Akreditasi" name="akreditasi" :value="$sekolah->akreditasi" maxlength="5" />
                         </div>
                         <div class="space-y-4">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-2">Sanitasi & Prasarana</h4>
                            <x-form.input label="Luas Tanah (m2)" name="luas_tanah" type="number" :value="$sekolah->luas_tanah" />
                            <x-form.input label="Luas Bangunan (m2)" name="luas_bangunan" type="number" :value="$sekolah->luas_bangunan" />
                            <x-form.input label="Sumber Air" name="sumber_air" :value="$sekolah->sumber_air" />
                            <x-form.input label="Daya Listrik (Watt)" name="daya_listrik" :value="$sekolah->daya_listrik" />
                            <x-form.input label="Akses Internet" name="akses_internet" :value="$sekolah->akses_internet" />
                         </div>
                     </div>
                </div>

            </div>

            {{-- Submit Button --}}
            <div class="mt-6 flex justify-end">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Script for Image Preview --}}
    <script>
        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layout.layout>
