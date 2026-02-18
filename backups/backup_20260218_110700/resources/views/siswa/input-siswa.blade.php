<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Siswa', 'href' => route('siswa.index')],
        ['name' => isset($siswa->id) ? 'Edit Siswa' : 'Tambah Siswa', 'href' => '']
    ]" />

    <form action="{{ isset($siswa->id) ? route('siswa.update', $siswa->id) : route('siswa.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="mt-6"
          id="siswaForm">
        @csrf
        @if(isset($siswa->id)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left Column: Photo & Basic Info (Sticky) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="p-6 bg-white border border-gray-200 rounded-base shadow-sm dark:bg-gray-800 dark:border-gray-700 sticky top-24">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white text-center">Foto Siswa</h3>
                    
                    <div class="flex flex-col items-center justify-center">
                        <div class="relative w-32 h-32 sm:w-40 sm:h-40 mb-4 overflow-hidden rounded-full border-4 border-gray-100 dark:border-gray-700 shadow-sm group">
                            <img id="preview-image" 
                                 src="{{ isset($siswa->foto) ? asset('storage/' . $siswa->foto) : 'https://ui-avatars.com/api/?name=' . ($siswa->nama ?? 'Siswa') . '&background=random' }}" 
                                 alt="Preview" 
                                 class="w-full h-full object-cover">
                            
                            <label for="foto-upload" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </label>
                        </div>
                        <input type="file" name="foto" id="foto-upload" class="hidden" accept="image/*" onchange="previewImage(event)">
                        
                        <div class="w-full mt-4 space-y-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="aktif" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Aktif" {{ (old('aktif', $siswa->aktif ?? '') == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ (old('aktif', $siswa->aktif ?? '') == 'Non-Aktif') ? 'selected' : '' }}>Non-Aktif</option>
                                    <option value="Lulus" {{ (old('aktif', $siswa->aktif ?? '') == 'Lulus') ? 'selected' : '' }}>Lulus</option>
                                    <option value="Keluar" {{ (old('aktif', $siswa->aktif ?? '') == 'Keluar') ? 'selected' : '' }}>Keluar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons for Desktop (Hidden on mobile, pushed to bottom bar) -->
                    <div class="hidden lg:block mt-8 space-y-2">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 px-5 py-3 text-sm font-medium text-white bg-blue-700 rounded-base hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 shadow-lg">
                            <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            {{ isset($siswa->id) ? 'Update Data' : 'Simpan Data' }}
                        </button>
                        <a href="{{ route('siswa.index') }}" class="w-full flex justify-center items-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white rounded-base border border-gray-200 hover:bg-gray-100">
                            Batal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabbed Forms -->
            <div class="lg:col-span-3">
                
                <!-- Custom Tabs Navigation -->
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <ul class="flex flex-nowrap -mb-px text-sm font-medium text-center" id="myTab" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Profil Siswa</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="alamat-tab" data-tabs-target="#alamat" type="button" role="tab" aria-controls="alamat" aria-selected="false">Alamat & Kontak</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="ortu-tab" data-tabs-target="#ortu" type="button" role="tab" aria-controls="ortu" aria-selected="false">Orang Tua & Wali</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="bantuan-tab" data-tabs-target="#bantuan" type="button" role="tab" aria-controls="bantuan" aria-selected="false">Bantuan & PIP</button>
                        </li>
                        <li role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="fisik-tab" data-tabs-target="#fisik" type="button" role="tab" aria-controls="fisik" aria-selected="false">Fisik & Lainnya</button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div id="myTabContent" class="space-y-6">
                    
                    <!-- TAB 1: PROFIL SISWA -->
                    <div class="p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Identitas Utama</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                                <input type="text" name="nama" value="{{ old('nama', $siswa->nama ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIPD</label>
                                <input type="text" name="nipd" value="{{ old('nipd', $siswa->nipd ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NISN</label>
                                <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIK</label>
                                <input type="number" name="nik" value="{{ old('nik', $siswa->nik ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. KK</label>
                                <input type="number" name="nokk" value="{{ old('nokk', $siswa->nokk ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                                <input type="text" name="tempatlahir" value="{{ old('tempatlahir', $siswa->tempatlahir ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                                <input type="date" name="tanggallahir" value="{{ old('tanggallahir', $siswa->tanggallahir ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                                <div class="flex items-center gap-4 mt-2">
                                    <div class="flex items-center">
                                        <input id="jk-l" type="radio" value="L" name="jk" {{ (old('jk', $siswa->jk ?? '') == 'L') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label for="jk-l" class="ml-2 text-sm font-medium text-gray-900">Laki-laki</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="jk-p" type="radio" value="P" name="jk" {{ (old('jk', $siswa->jk ?? '') == 'P') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label for="jk-p" class="ml-2 text-sm font-medium text-gray-900">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agama</label>
                                <select name="agama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ (old('agama', $siswa->agama ?? '') == $agama) ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SKHUN</label>
                                <input type="text" name="skhun" value="{{ old('skhun', $siswa->skhun ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Ijazah</label>
                                <input type="text" name="noijazah" value="{{ old('noijazah', $siswa->noijazah ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rombel Saat Ini</label>
                                <input type="text" name="rombelsaatini" value="{{ old('rombelsaatini', $siswa->rombelsaatini ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Peserta UNAS</label>
                                <input type="text" name="nopesertaunas" value="{{ old('nopesertaunas', $siswa->nopesertaunas ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Akta Lahir</label>
                                <input type="text" name="noaktalahir" value="{{ old('noaktalahir', $siswa->noaktalahir ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Anak Ke-</label>
                                <input type="number" name="anakke" value="{{ old('anakke', $siswa->anakke ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Saudara</label>
                                <input type="number" name="jmlsaudara" value="{{ old('jmlsaudara', $siswa->jmlsaudara ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sekolah Asal</label>
                                <input type="text" name="sekolahasal" value="{{ old('sekolahasal', $siswa->sekolahasal ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ALAMAT -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                         <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Alamat & Kontak</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Jalan</label>
                                <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dusun</label>
                                <input type="text" name="dusun" value="{{ old('dusun', $siswa->dusun ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelurahan / Desa</label>
                                <input type="text" name="kelurahan" value="{{ old('kelurahan', $siswa->kelurahan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kecamatan</label>
                                <input type="text" name="kecamatan" value="{{ old('kecamatan', $siswa->kecamatan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Pos</label>
                                <input type="text" name="kode_pos" value="{{ old('kode_pos', $siswa->kode_pos ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RT</label>
                                <input type="text" name="rt" value="{{ old('rt', $siswa->rt ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RW</label>
                                <input type="text" name="rw" value="{{ old('rw', $siswa->rw ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Tinggal</label>
                                <input type="text" name="jenis_tinggal" value="{{ old('jenis_tinggal', $siswa->jenis_tinggal ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Bersama Orang Tua">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alat Transportasi</label>
                                <input type="text" name="alat_transportasi" value="{{ old('alat_transportasi', $siswa->alat_transportasi ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Telepon / HP</label>
                                <input type="text" name="hp" value="{{ old('hp', $siswa->hp ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <input type="email" name="email" value="{{ old('email', $siswa->email ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                         </div>
                    </div>

                    <!-- TAB 3: DATA ORANG TUA / WALI -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="ortu" role="tabpanel" aria-labelledby="ortu-tab">
                        <div class="space-y-8">
                            <!-- AYAH -->
                            <div class="p-4 border border-blue-100 rounded-base bg-blue-50 dark:bg-gray-700 dark:border-gray-600">
                                <h5 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-4">Data Ayah</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="text" name="ayah" placeholder="Nama Ayah" value="{{ old('ayah', $siswa->ayah ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="nikayah" placeholder="NIK Ayah" value="{{ old('nikayah', $siswa->nikayah ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="tahunlahirayah" placeholder="Tahun Lahir" value="{{ old('tahunlahirayah', $siswa->tahunlahirayah ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pendidikanayah" placeholder="Pendidikan" value="{{ old('pendidikanayah', $siswa->pendidikanayah ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pekerjaanayah" placeholder="Pekerjaan" value="{{ old('pekerjaanayah', $siswa->pekerjaanayah ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="penghasilanayah" placeholder="Penghasilan" value="{{ old('penghasilanayah', $siswa->penghasilanayah ?? '') }}" class="form-input rounded-base">
                                </div>
                            </div>
                            
                            <!-- IBU -->
                            <div class="p-4 border border-pink-100 rounded-base bg-pink-50 dark:bg-gray-700 dark:border-gray-600">
                                <h5 class="text-lg font-bold text-pink-800 dark:text-pink-300 mb-4">Data Ibu</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="text" name="namaibu" placeholder="Nama Ibu" value="{{ old('namaibu', $siswa->namaibu ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="nikibu" placeholder="NIK Ibu" value="{{ old('nikibu', $siswa->nikibu ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="tahunlahiribu" placeholder="Tahun Lahir" value="{{ old('tahunlahiribu', $siswa->tahunlahiribu ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pendidikanibu" placeholder="Pendidikan" value="{{ old('pendidikanibu', $siswa->pendidikanibu ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pekerjaanibu" placeholder="Pekerjaan" value="{{ old('pekerjaanibu', $siswa->pekerjaanibu ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="penghasilanibu" placeholder="Penghasilan" value="{{ old('penghasilanibu', $siswa->penghasilanibu ?? '') }}" class="form-input rounded-base">
                                </div>
                            </div>

                            <!-- WALI -->
                            <div class="p-4 border border-gray-200 rounded-base bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                <h5 class="text-lg font-bold text-gray-800 dark:text-gray-300 mb-4">Data Wali (Opsional)</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="text" name="namawali" placeholder="Nama Wali" value="{{ old('namawali', $siswa->namawali ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="nikwali" placeholder="NIK Wali" value="{{ old('nikwali', $siswa->nikwali ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="tahunlahirwali" placeholder="Tahun Lahir" value="{{ old('tahunlahirwali', $siswa->tahunlahirwali ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pendidikanwali" placeholder="Pendidikan" value="{{ old('pendidikanwali', $siswa->pendidikanwali ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="pekerjaanwali" placeholder="Pekerjaan" value="{{ old('pekerjaanwali', $siswa->pekerjaanwali ?? '') }}" class="form-input rounded-base">
                                    <input type="text" name="penghasilanwali" placeholder="Penghasilan" value="{{ old('penghasilanwali', $siswa->penghasilanwali ?? '') }}" class="form-input rounded-base">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: BANTUAN & BANK -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="bantuan" role="tabpanel" aria-labelledby="bantuan-tab">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Penerima KPS?</label>
                                <input type="text" name="penerima_kps" value="{{ old('penerima_kps', $siswa->penerima_kps ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. KPS</label>
                                <input type="text" name="nokps" value="{{ old('nokps', $siswa->nokps ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Penerima KIP?</label>
                                <input type="text" name="penerimakip" value="{{ old('penerimakip', $siswa->penerimakip ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. KIP</label>
                                <input type="text" name="nomorkip" value="{{ old('nomorkip', $siswa->nomorkip ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama di KIP</label>
                                <input type="text" name="namadikip" value="{{ old('namadikip', $siswa->namadikip ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. KKS</label>
                                <input type="text" name="nomorkks" value="{{ old('nomorkks', $siswa->nomorkks ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Layak PIP?</label>
                                <input type="text" name="layakpip" value="{{ old('layakpip', $siswa->layakpip ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan Layak PIP</label>
                                <input type="text" name="alasanlayakpip" value="{{ old('alasanlayakpip', $siswa->alasanlayakpip ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bank</label>
                                <input type="text" name="bank" value="{{ old('bank', $siswa->bank ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Rekening</label>
                                <input type="text" name="nomor_rekening_bank" value="{{ old('nomor_rekening_bank', $siswa->nomor_rekening_bank ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rekening Atas Nama</label>
                                <input type="text" name="rekening_atas_nama" value="{{ old('rekening_atas_nama', $siswa->rekening_atas_nama ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: FISIK & LAINNYA -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="fisik" role="tabpanel" aria-labelledby="fisik-tab">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tinggi Badan (cm)</label>
                                <input type="number" name="tinggibadan" value="{{ old('tinggibadan', $siswa->tinggibadan ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Berat Badan (kg)</label>
                                <input type="number" name="beratbadan" value="{{ old('beratbadan', $siswa->beratbadan ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lingkar Kepala (cm)</label>
                                <input type="number" name="lingkarkepala" value="{{ old('lingkarkepala', $siswa->lingkarkepala ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jarak ke Sekolah (km)</label>
                                <input type="text" name="jarakrumah" value="{{ old('jarakrumah', $siswa->jarakrumah ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kebutuhan Khusus</label>
                                <input type="text" name="kebutuhankhusus" value="{{ old('kebutuhankhusus', $siswa->kebutuhankhusus ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posisi Lintang</label>
                                <input type="text" name="lintang" value="{{ old('lintang', $siswa->lintang ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posisi Bujur</label>
                                <input type="text" name="bujur" value="{{ old('bujur', $siswa->bujur ?? '') }}" class="form-input rounded-base w-full">
                            </div>
                         </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Sticky Button Bar -->
            <div class="lg:hidden fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg z-30 flex gap-2">
                 <button type="submit" class="flex-1 flex justify-center items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-base text-sm px-5 py-2.5">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    {{ isset($siswa->id) ? 'Update' : 'Simpan' }}
                 </button>
                 <a href="{{ route('siswa.index') }}" class="flex-1 text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-base text-sm px-5 py-2.5 text-center">Batal</a>
            </div>

        </div>
    </form>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview-image');
                output.src = reader.result;
            };
            if(event.target.files[0]){
                 reader.readAsDataURL(event.target.files[0]);
            }
        }

        // Simple Tab Logic (if simpler than utilizing Flowbite JS)
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('[role="tab"]');
            const panels = document.querySelectorAll('[role="tabpanel"]');
            
            // Set initial active tab
            setActiveTab('profile-tab');

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    setActiveTab(tab.id);
                });
            });

            function setActiveTab(tabId) {
                const targetPanelId = document.getElementById(tabId).getAttribute('data-tabs-target').substring(1);
                
                // Hide all panels
                panels.forEach(panel => panel.classList.add('hidden'));
                
                // Deactivate all tabs
                tabs.forEach(t => {
                    t.classList.remove('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                    t.classList.add('border-transparent');
                    t.setAttribute('aria-selected', 'false');
                });

                // Show target panel
                document.getElementById(targetPanelId).classList.remove('hidden');
                
                // Activate clicked tab
                const activeTab = document.getElementById(tabId);
                activeTab.classList.remove('border-transparent');
                activeTab.classList.add('text-blue-600', 'border-blue-600', 'dark:text-blue-500', 'dark:border-blue-500');
                activeTab.setAttribute('aria-selected', 'true');
            }
        });
    </script>

    <style>
        .form-input {
            width: 100%;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            color: #111827;
            font-size: 0.875rem;
            line-height: 1.25rem;
            display: block;
            padding: 0.625rem;
        }
        .dark .form-input {
             background-color: #374151;
             border-color: #4b5563;
             color: #ffffff;
             placeholder-color: #9ca3af;
        }
        .form-input:focus {
            ring: 2px solid #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</x-layout.layout>
