<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Pegawai', 'href' => route('pegawai.index')],
        ['name' => isset($pegawai->id) ? 'Edit Pegawai' : 'Tambah Pegawai', 'href' => '']
    ]" />

    <form action="{{ isset($pegawai->id) ? route('pegawai.update', $pegawai->id) : route('pegawai.store') }}" 
          method="POST" 
          enctype="multipart/form-data" 
          class="mt-6"
          id="pegawaiForm">
        @csrf
        @if(isset($pegawai->id)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left Column: Photo & Basic Info (Sticky) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="p-6 bg-white border border-gray-200 rounded-base shadow-sm dark:bg-gray-800 dark:border-gray-700 sticky top-24">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white text-center">Foto Pegawai</h3>
                    
                    <div class="flex flex-col items-center justify-center">
                        <div class="relative w-32 h-32 sm:w-40 sm:h-40 mb-4 overflow-hidden rounded-full border-4 border-gray-100 dark:border-gray-700 shadow-sm group">
                            <img id="preview-image" 
                                 src="{{ isset($pegawai->foto) ? asset('storage/' . $pegawai->foto) : 'https://ui-avatars.com/api/?name=' . ($pegawai->name ?? 'Pegawai') . '&background=random' }}" 
                                 alt="Preview" 
                                 class="w-full h-full object-cover">
                            
                            <label for="foto-upload" class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </label>
                        </div>
                        <input type="file" name="foto" id="foto-upload" class="hidden" accept="image/*" onchange="previewImage(event)">
                        
                        <div class="w-full mt-4 space-y-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Status Pegawai</label>
                                <select name="aktif" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Aktif" {{ (old('aktif', $pegawai->aktif ?? '') == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ (old('aktif', $pegawai->aktif ?? '') == 'Non-Aktif') ? 'selected' : '' }}>Non-Aktif</option>
                                    <option value="Cuti" {{ (old('aktif', $pegawai->aktif ?? '') == 'Cuti') ? 'selected' : '' }}>Cuti</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons for Desktop -->
                    <div class="hidden lg:block mt-8 space-y-2">
                        <button type="submit" class="w-full flex justify-center items-center gap-2 px-5 py-3 text-sm font-medium text-white bg-blue-700 rounded-base hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 shadow-lg">
                            <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            {{ isset($pegawai->id) ? 'Update Data' : 'Simpan Data' }}
                        </button>
                        <a href="{{ route('pegawai.index') }}" class="w-full flex justify-center items-center px-5 py-2.5 text-sm font-medium text-gray-900 bg-white rounded-base border border-gray-200 hover:bg-gray-100">
                            Batal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabbed Forms -->
            <div class="lg:col-span-3">
                
                <!-- Custom Tabs Navigation -->
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <ul class="flex flex-nowrap -mb-px text-sm font-medium text-center" id="pegawaiTab" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="profil-tab" data-tabs-target="#profil" type="button" role="tab" aria-controls="profil" aria-selected="false">Profil Pegawai</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="kepegawaian-tab" data-tabs-target="#kepegawaian" type="button" role="tab" aria-controls="kepegawaian" aria-selected="false">Data Kepegawaian</button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="alamat-tab" data-tabs-target="#alamat" type="button" role="tab" aria-controls="alamat" aria-selected="false">Alamat & Kontak</button>
                        </li>
                        <li role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="keluarga-tab" data-tabs-target="#keluarga" type="button" role="tab" aria-controls="keluarga" aria-selected="false">Data Keluarga</button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div id="myTabContent" class="space-y-6">
                    
                    <!-- TAB 1: PROFIL -->
                    <div class="p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="profil" role="tabpanel" aria-labelledby="profil-tab">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Identitas Utama</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $pegawai->name ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required placeholder="Nama lengkap dengan gelar">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NUPTK</label>
                                <input type="number" name="nuptk" value="{{ old('nuptk', $pegawai->nuptk ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NIK</label>
                                <input type="number" name="nonik" value="{{ old('nonik', $pegawai->nonik ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. KK</label>
                                <input type="number" name="nokk" value="{{ old('nokk', $pegawai->nokk ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                                <input type="text" name="kotalahir" value="{{ old('kotalahir', $pegawai->kotalahir ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                                <input type="date" name="tanggallahir" value="{{ old('tanggallahir', $pegawai->tanggallahir ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kelamin</label>
                                <div class="flex items-center gap-6 mt-2">
                                    <div class="flex items-center">
                                        <input id="jk-l" type="radio" value="L" name="jk" {{ (old('jk', $pegawai->jk ?? '') == 'L') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label for="jk-l" class="ms-2 text-sm font-medium text-gray-900">Laki-laki</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="jk-p" type="radio" value="P" name="jk" {{ (old('jk', $pegawai->jk ?? '') == 'P') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        <label for="jk-p" class="ms-2 text-sm font-medium text-gray-900">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Agama</label>
                                <select name="agama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ (old('agama', $pegawai->agama ?? '') == $agama) ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Removed Attendance Configuration as per request; moved to Attendance Setting page -->

                    <!-- TAB 2: KEPEGAWAIAN -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="kepegawaian" role="tabpanel" aria-labelledby="kepegawaian-tab">
                         <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Data Kepegawaian</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis PTK</label>
                                <input type="text" name="jenis_ptk" value="{{ old('jenis_ptk', $pegawai->jenis_ptk ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Guru Mapel">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="">Pilih...</option>
                                    @foreach(['PNS', 'PPPK', 'GTY', 'GTT', 'Honor Daerah', 'Tenaga Honor Sekolah'] as $status)
                                        <option value="{{ $status }}" {{ (old('status_kepegawaian', $pegawai->status_kepegawaian ?? '') == $status) ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">NPWP</label>
                                <input type="text" name="npwp" value="{{ old('npwp', $pegawai->npwp ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pangkat / Golongan</label>
                                <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $pegawai->pangkat_golongan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sumber Gaji</label>
                                <input type="text" name="sumber_gaji" value="{{ old('sumber_gaji', $pegawai->sumber_gaji ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lembaga Pengangkatan</label>
                                <input type="text" name="lembaga_pengangkatan" value="{{ old('lembaga_pengangkatan', $pegawai->lembaga_pengangkatan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            
                            <!-- SK Section -->
                             <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-4 mt-2">
                                <h5 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Nomor & Tanggal SK</h5>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SK Pengangkatan</label>
                                <input type="text" name="sk_pengangkatan" value="{{ old('sk_pengangkatan', $pegawai->sk_pengangkatan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tgl SK Pengangkatan</label>
                                <input type="date" name="tgl_sk" value="{{ old('tgl_sk', $pegawai->tgl_sk ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SK CPNS</label>
                                <input type="text" name="sk_cpns" value="{{ old('sk_cpns', $pegawai->sk_cpns ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tgl CPNS</label>
                                <input type="date" name="tgl_cpns" value="{{ old('tgl_cpns', $pegawai->tgl_cpns ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: ALAMAT & KONTAK -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="alamat" role="tabpanel" aria-labelledby="alamat-tab">
                         <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Alamat Domisili</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat Jalan</label>
                                <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dusun</label>
                                <input type="text" name="dusun" value="{{ old('dusun', $pegawai->dusun ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelurahan / Desa</label>
                                <input type="text" name="kelurahan" value="{{ old('kelurahan', $pegawai->kelurahan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kecamatan</label>
                                <input type="text" name="kecamatan" value="{{ old('kecamatan', $pegawai->kecamatan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Pos</label>
                                <input type="text" name="kodepos" value="{{ old('kodepos', $pegawai->kodepos ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RT</label>
                                <input type="text" name="rt" value="{{ old('rt', $pegawai->rt ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">RW</label>
                                <input type="text" name="rw" value="{{ old('rw', $pegawai->rw ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Telepon / HP</label>
                                <input type="text" name="hp" value="{{ old('hp', $pegawai->hp ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                             <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <input type="email" name="email" value="{{ old('email', $pegawai->email ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                         </div>
                    </div>

                    <!-- TAB 4: KELUARGA -->
                    <div class="hidden p-6 bg-white rounded-base border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700" id="keluarga" role="tabpanel" aria-labelledby="keluarga-tab">
                         <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Data Pasangan</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Suami / Istri</label>
                                <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan', $pegawai->nama_pasangan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pekerjaan</label>
                                <input type="text" name="pekerjaan_pasangan" value="{{ old('pekerjaan_pasangan', $pegawai->pekerjaan_pasangan ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <!-- Mobile Sticky Button Bar -->
            <div class="lg:hidden fixed bottom-14 left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 shadow-lg z-30 flex gap-2">
                 <button type="submit" class="flex-1 flex justify-center items-center gap-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-base text-sm px-5 py-2.5">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    {{ isset($pegawai->id) ? 'Update' : 'Simpan' }}
                 </button>
                 <a href="{{ route('pegawai.index') }}" class="flex-1 text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-base text-sm px-5 py-2.5 text-center">Batal</a>
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

        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('[role="tab"]');
            const panels = document.querySelectorAll('[role="tabpanel"]');
            
            // Set initial active tab
            setActiveTab('profil-tab');

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
</x-layout.layout>
