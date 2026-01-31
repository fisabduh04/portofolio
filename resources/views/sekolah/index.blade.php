<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('home')],
        ['name' => 'Identitas Sekolah', 'href' => '#'],
    ]" />

    <section class="mt-6 pb-20" x-data="{ activeSection: 'identitas' }">
        <div class="max-w-7xl mx-auto">
            
            {{-- Page Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Profil & Identitas Sekolah</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-medium">Data lembaga, legalitas, dan aset sekolah terintegrasi.</p>
                </div>
                <div>
                    <span class="inline-flex items-center px-4 py-1.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-xs font-bold border border-blue-200 uppercase tracking-tighter">
                        Semester Genap 2024/2025
                    </span>
                </div>
            </div>

            @if ($errors->any())
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-900" role="alert">
                    <span class="font-bold uppercase tracking-widest block mb-2">Simpan data gagal:</span>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Main Layout --}}
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- Sidebar Navigation --}}
                <div class="lg:w-64 flex-shrink-0">
                    <div class="sticky top-24 space-y-2">
                        @php
                            $sections = [
                                'identitas' => ['label' => 'Identitas Utama', 'icon' => 'office-building', 'color' => 'blue'],
                                'lokasi' => ['label' => 'Lokasi & Alamat', 'icon' => 'location-marker', 'color' => 'green'],
                                'kontak' => ['label' => 'Kontak & Legalitas', 'icon' => 'shield-check', 'color' => 'indigo'],
                                'periodik' => ['label' => 'Sarpras & Periodik', 'icon' => 'lightning-bolt', 'color' => 'amber'],
                                'media' => ['label' => 'Media & Logo', 'icon' => 'photograph', 'color' => 'pink'],
                            ];
                        @endphp

                        @foreach($sections as $id => $info)
                            <button @click="document.getElementById('section-{{ $id }}').scrollIntoView({behavior: 'smooth'}); activeSection = '{{ $id }}'" 
                                :class="activeSection === '{{ $id }}' ? 'bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-800 dark:bg-{{ $info['color'] }}-900/30 dark:text-{{ $info['color'] }}-300' : 'bg-white text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400'"
                                class="w-full text-left px-4 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-3 border border-transparent {{ $id === 'identitas' && 'active' }}">
                                <span class="w-6 h-6 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                {{ $info['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="flex-1 space-y-8">
                    <form action="{{ route('sekolah.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        
                        {{-- IDENTITAS --}}
                        <div id="section-identitas" class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden scroll-mt-24">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Identitas Utama</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Nama Sekolah</label>
                                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $sekolah->nama_sekolah) }}" 
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:text-white uppercase font-bold" placeholder="NAMA SEKOLAH...">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">NPSN</label>
                                    <input type="number" name="npsn" value="{{ old('npsn', $sekolah->npsn) }}" 
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700 font-mono" placeholder="XXXXXXXX">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">NSS (Opsional)</label>
                                    <input type="text" name="nss" value="{{ old('nss', $sekolah->nss) }}" 
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700" placeholder="-">
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Status Sekolah</label>
                                    <select name="status_sekolah" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700">
                                        <option value="Negeri" @selected(old('status_sekolah', $sekolah->status_sekolah) == 'Negeri')>Negeri</option>
                                        <option value="Swasta" @selected(old('status_sekolah', $sekolah->status_sekolah) == 'Swasta')>Swasta</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Bentuk Pendidikan</label>
                                    <select name="bentuk_pendidikan" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700">
                                        @foreach(['SD','SMP','SMA','SMK','SLB','PKBM'] as $bentuk)
                                            <option value="{{ $bentuk }}" @selected(old('bentuk_pendidikan', $sekolah->bentuk_pendidikan) == $bentuk)>{{ $bentuk }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- LOKASI --}}
                        <div id="section-lokasi" class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden scroll-mt-24">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-700 flex items-center">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Lokasi & Alamat</h3>
                            </div>
                            <div class="p-6 space-y-6">
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="2" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 block w-full p-2.5 dark:bg-gray-700">{{ old('alamat', $sekolah->alamat) }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block mb-1 text-[10px] font-bold text-gray-500 uppercase">RT</label>
                                        <input type="text" name="rt" value="{{ old('rt', $sekolah->rt) }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-[10px] font-bold text-gray-500 uppercase">RW</label>
                                        <input type="text" name="rw" value="{{ old('rw', $sekolah->rw) }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-[10px] font-bold text-gray-500 uppercase">Kode Pos</label>
                                        <input type="text" name="kode_pos" value="{{ old('kode_pos', $sekolah->kode_pos) }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-[10px] font-bold text-gray-500 uppercase">Dusun</label>
                                        <input type="text" name="dusun" value="{{ old('dusun', $sekolah->dusun) }}" class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-2 dark:bg-gray-700">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Kelurahan</label>
                                        <input type="text" name="kelurahan" value="{{ old('kelurahan', $sekolah->kelurahan) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div>
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Kecamatan</label>
                                        <input type="text" name="kecamatan" value="{{ old('kecamatan', $sekolah->kecamatan) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div>
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Kabupaten/Kota</label>
                                        <input type="text" name="kabupaten" value="{{ old('kabupaten', $sekolah->kabupaten) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div>
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Provinsi</label>
                                        <input type="text" name="provinsi" value="{{ old('provinsi', $sekolah->provinsi) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2 mb-3 text-emerald-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">Geo-Tagging (Koordinat)</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <input type="text" name="lintang" value="{{ old('lintang', $sekolah->lintang) }}" placeholder="Latitude" class="bg-white border border-gray-200 rounded-lg text-xs w-full p-2 dark:bg-gray-800">
                                        <input type="text" name="bujur" value="{{ old('bujur', $sekolah->bujur) }}" placeholder="Longitude" class="bg-white border border-gray-200 rounded-lg text-xs w-full p-2 dark:bg-gray-800">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTAK & LEGALITAS --}}
                        <div id="section-kontak" class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden scroll-mt-24">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-700 flex items-center">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Kontak & Legalitas</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">No. Telepon / Fax</label>
                                        <div class="flex gap-2"><input type="text" name="no_telp" value="{{ old('no_telp', $sekolah->no_telp) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"><input type="text" name="no_fax" value="{{ old('no_fax', $sekolah->no_fax) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div></div>
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Email & Website</label>
                                        <input type="email" name="email" value="{{ old('email', $sekolah->email) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700 mb-2"><input type="text" name="website" value="{{ old('website', $sekolah->website) }}" placeholder="Website" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"></div>
                                </div>
                                <div class="space-y-4">
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">SK Pendirian</label>
                                        <div class="flex gap-2"><input type="text" name="sk_pendirian" value="{{ old('sk_pendirian', $sekolah->sk_pendirian) }}" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700"><input type="date" name="tgl_sk_pendirian" value="{{ old('tgl_sk_pendirian', optional($sekolah->tgl_sk_pendirian)->format('Y-m-d')) }}" class="bg-white border border-gray-300 rounded-lg p-2 dark:bg-gray-700 text-xs"></div></div>
                                    <div><label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Akreditasi</label>
                                        <select name="akreditasi" class="bg-white border border-gray-300 rounded-lg w-full p-2.5 dark:bg-gray-700 uppercase font-bold">
                                            @foreach(['A','B','C','Tidak Terakreditasi'] as $akr)
                                                <option value="{{ $akr }}" @selected(old('akreditasi', $sekolah->akreditasi) == $akr)>{{ $akr }}</option>
                                            @endforeach
                                        </select></div>
                                </div>
                            </div>
                        </div>

                        {{-- MEDIA --}}
                        <div id="section-media" class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden scroll-mt-24">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 dark:bg-gray-700/50 dark:border-gray-700">
                                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest">Media & Logo</h3>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg p-4 flex flex-col items-center justify-center relative min-h-[200px] hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <img id="logo-preview" src="{{ $sekolah->logo_url }}" class="{{ $sekolah->logo ? '' : 'hidden' }} max-h-32 object-contain mb-4">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Logo Sekolah (PNG/JPG)</p>
                                    <input type="file" name="logo" onchange="previewImage(event, 'logo-preview')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                                <div class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg p-4 flex flex-col items-center justify-center relative min-h-[200px] hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <img id="kop-preview" src="{{ $sekolah->kop_surat_url }}" class="{{ $sekolah->kop_surat ? '' : 'hidden' }} max-h-32 object-contain mb-4">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kop Surat (Header)</p>
                                    <input type="file" name="kop_surat" onchange="previewImage(event, 'kop-preview')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end pt-8">
                            <x-btn type="submit" color="blue" variant="solid" size="lg" icon="save">SIMPAN PERUBAHAN</x-btn>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function previewImage(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layout.layout>
