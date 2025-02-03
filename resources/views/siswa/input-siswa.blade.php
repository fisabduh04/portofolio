<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Siswa', 'href' => '/siswa'],
        ['name' => 'Data Detail Siswa', 'href' => '']
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">

        <button type="submit" form="myForm"
            class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">
            <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="M11 16h2m6.707-9.293-2.414-2.414A1 1 0 0 0 16.586 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.414a1 1 0 0 0-.293-.707ZM16 20v-6a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v6h8ZM9 4h6v3a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V4Z" />
            </svg>
            @isset($siswa->id) Update @else Simpan @endisset
        </button>

        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <form id="myForm" @isset($siswa->id) action="{{ route('siswa.update', $siswa->id) }}"
            @else action="/siswa" @endisset method="POST" enctype="multipart/form-data">
            @isset($siswa->id) @method('PUT') @endisset
            @csrf
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div class="flex flex-col items-center justify-center w-full" id="uploadfoto">
                    <!-- Jika Foto Sudah Ada -->
                    @if(isset($siswa) && $siswa->foto)
                    <div class="relative">
                        <img id="existing-preview" src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto Siswa"
                            class="object-cover w-64 h-64 rounded-lg">
                        <button type="button" onclick="replacePhoto()"
                            class="absolute p-2 text-white bg-red-500 rounded-full top-2 right-2">
                            Ganti Foto
                        </button>
                    </div>
                    @else
                    <!-- Jika Foto Tidak Ada -->
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click
                                    to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)
                            </p>
                        </div>
                        <input id="dropzone-file" name="foto" type="file" class="hidden" accept="image/*"
                            onchange="previewImage(event)" />
                    </label>
                    @endif
                </div>

                <!-- Area untuk Preview Foto Baru -->
                <div class="mt-4" id="gambar" style="display: none">
                    <img id="preview" onclick="replacePhoto()" class="object-cover w-64 h-64 rounded-lg"
                        style="display: none;" alt="Preview Image" />
                </div>



                <div class="grid gap-5">
                    <x-form.input name="nama" :value="$siswa->nama??''" label="Nama Lengkap" />

                    <x-form.input name="nipd" :value="$siswa->nipd??''" label="NIPD" />

                    <x-form.input name="nisn" :value="$siswa->nisn??''" label="NISN" />
                </div>
                <x-form.input name="jk" :value="$siswa->jk??''" label="Jenis Kelamin" />
                <x-form.input name="status" :value="$siswa->status??''" label="Status" />
                <x-form.input name="aktif" :value="$siswa->aktif??''" label="Aktif" />
                <x-form.input name="tempatlahir" :value="$siswa->tempatlahir??''" label="Tempat Lahir" />
                <x-form.input name="tanggallahir" :value="$siswa->tanggallahir??''" label="Tanggal Lahir" />
                <x-form.input name="nik" :value="$siswa->nik??''" label="No. NIK" />
                <x-form.input name="agama" :value="$siswa->agama??''" label="Agama" />
                <x-form.input name="alamat" :value="$siswa->alamat??''" label="Alamat" />
                <x-form.input name="rt" :value="$siswa->rt??''" label="RT" />
                <x-form.input name="rw" :value="$siswa->rw??''" label="RW" />
                <x-form.input name="dusun" :value="$siswa->dusun??''" label="Dusun" />
                <x-form.input name="kelurahan" :value="$siswa->kelurahan??''" label="Kelurahan" />
                <x-form.input name="kecamatan" :value="$siswa->kecamatan??''" label="KEcamatan" />
                <x-form.input name="kode_pos" :value="$siswa->kode_pos??''" label="Kode Pos" />
                <x-form.input name="jenis_tinggal" :value="$siswa->jenis_tinggal??''" label="Jenis Tinggal" />
                <x-form.input name="alat_transportasi" :value="$siswa->alat_transportasi??''"
                    label="ALat Transportasi" />
                <x-form.input name="telepon" :value="$siswa->telepon??''" label="No. Telepon" />
                <x-form.input name="hp" :value="$siswa->hp??''" label="No. HP" />
                <x-form.input name="email" :value="$siswa->email??''" label="email" />
                <x-form.input name="skhun" :value="$siswa->skhun??''" label="No. SKHUN" />
                <x-form.input name="penerima_kps" :value="$siswa->penerima_kps??''" label="Penerima KPS" />
                <x-form.input name="nokps" :value="$siswa->nokps??''" label="No. KPS" />
            </div>
            <hr class="">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-form.input name="ayah" :value="$siswa->ayah??''" label="Nama Ayah" />
                <x-form.input name="tahunlahirayah" :value="$siswa->tahunlahirayah??''" label="Tahun Lahir Ayah" />
                <x-form.input name="pendidikanayah" :value="$siswa->pendidikanayah??''" label="Pendidikan Ayah" />
                <x-form.input name="pekerjaanayah" :value="$siswa->pekerjaanayah??''" label="Pekerjaan Ayah" />
                <x-form.input name="penghasilanayah" :value="$siswa->penghasilanayah??''" label="Penghasila Ayah" />
                <x-form.input name="nikayah" :value="$siswa->nikayah??''" label="NIK. Ayah" />
            </div>
            <hr>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-form.input name="namaibu" :value="$siswa->namaibu??''" label="Nama Ibu" />
                <x-form.input name="tahunlahiribu" :value="$siswa->tahunlahiribu??''" label="Tahun Lahir Ibu" />
                <x-form.input name="pendidikanibu" :value="$siswa->pendidikanibu??''" label="Pendidikan Ibu" />
                <x-form.input name="pekerjaanibu" :value="$siswa->pekerjaanibu??''" label="Pekerjaan Ibu" />
                <x-form.input name="penghasilanibu" :value="$siswa->penghasilanibu??''" label="Penghasilan Ibu" />
                <x-form.input name="nikibu" :value="$siswa->nikibu??''" label="No. NIK Ibu" />
            </div>
            <hr>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-form.input name="namawali" :value="$siswa->namawali??''" label="Nama Wali" />
                <x-form.input name="tahunlahirwali" :value="$siswa->tahunlahirwali??''" label="Tahun Lahir Wali" />
                <x-form.input name="pendidikanwali" :value="$siswa->pendidikanwali??''" label="Pendidikan Wali" />
                <x-form.input name="pekerjaanwali" :value="$siswa->pekerjaanwali??''" label="Pekerjaan Wali" />
                <x-form.input name="penghasilanwali" :value="$siswa->penghasilanwali??''" label="Penghasilan Wali" />
                <x-form.input name="nikwali" :value="$siswa->nikwali??''" label="No NIK Wali" />
            </div>
            <hr>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-form.input name="rombelsaatini" :value="$siswa->rombelsaatini??''" label="Rombel Saat ini" />
                <x-form.input name="nopesertaunas" :value="$siswa->nopesertaunas??''" label="No Peserta UNAS" />
                <x-form.input name="noijazah" :value="$siswa->noijazah??''" label="No. Ijasah" />
                <x-form.input name="penerimakip" :value="$siswa->penerimakip??''" label="Penerima KIP" />
                <x-form.input name="nomorkip" :value="$siswa->nomorkip??''" label="No. KIP" />
                <x-form.input name="namadikip" :value="$siswa->namadikip??''" label="Nama di KIP" />
                <x-form.input name="nomorkks" :value="$siswa->nomorkks??''" label="No. KKS" />
                <x-form.input name="noaktalahir" :value="$siswa->noaktalahir??''" label="Akta Lahir" />
                <x-form.input name="bank" :value="$siswa->bank??''" label="Nama Bank" />
                <x-form.input name="nomor_rekening_bank" :value="$siswa->nomor_rekening_bank??''"
                    label="No Rek. Bank" />
                <x-form.input name="rekening_atas_nama" :value="$siswa->rekening_atas_nama??''"
                    label="Rekening Atas Nama" />
                <x-form.input name="layakpip" :value="$siswa->layakpip??''" label="Layak PIP" />
                <x-form.input name="alasanlayakpip" :value="$siswa->alasanlayakpip??''" label="Alasan Layak PIP" />
                <x-form.input name="kebutuhankhusus" :value="$siswa->kebutuhankhusus??''" label="KEbutuhan Khusus" />
                <x-form.input name="sekolahasal" :value="$siswa->sekolahasal??''" label="Sekolah Asal" />
                <x-form.input name="anakke" :value="$siswa->anakke??''" label="Anak ke" />
            </div>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <x-form.input name="lintang" :value="$siswa->lintang??''" label="Posisi Lintang" />
                <x-form.input name="bujur" :value="$siswa->bujur??''" label="Posisi Bujur" />
                <x-form.input name="nokk" :value="$siswa->nokk??''" label="No. KK" />
                <x-form.input name="beratbadan" :value="$siswa->beratbadan??''" label="Berat Badan" />
                <x-form.input name="tinggibadan" :value="$siswa->tinggibadan??''" label="Tinggi Badan" />
                <x-form.input name="lingkarkepala" :value="$siswa->lingkarkepala??''" label="Lingkar Kepala" />
                <x-form.input name="jmlsaudara" :value="$siswa->jmlsaudara??''" label="Jumlah Saudara" />
                <x-form.input name="jarakrumah" :value="$siswa->jarakrumah??''" label="Jarak Ke Rumah" />
            </div>
            <div class="flex justify-start">
                <button type="submit"
                    class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">
                    <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M11 16h2m6.707-9.293-2.414-2.414A1 1 0 0 0 16.586 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.414a1 1 0 0 0-.293-.707ZM16 20v-6a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v6h8ZM9 4h6v3a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V4Z" />
                    </svg>
                    @isset($siswa->id) Update @else Simpan @endisset
                </button>
            </div>
        </form>
    </div>



    {{-- <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.style.display = 'block';

            const gambar=document.getElementById('gambar');
            gambar.style.display='block';

            const upload=document.getElementById('uploadfoto');
            upload.style.display='none';


            // Periksa apakah ada file yang dipilih
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                // Fungsi untuk menampilkan gambar setelah dibaca
                reader.onload = function (e) {
                    preview.src = e.target.result; // Set sumber gambar dari hasil pembacaan
                }

                // Membaca file sebagai URL Data
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>--}}


    <script>
        // Preview Foto Baru
    function previewImage(event) {
        const fileInput = event.target;
        const preview = document.getElementById('preview');
        const gambarContainer = document.getElementById('gambar');
        const uploadfoto=document.getElementById('uploadfoto');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
                gambarContainer.style.display = "block";
                uploadfoto.style.display='none';
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }

    // Ganti Foto
    function replacePhoto() {
        document.getElementById('uploadfoto').innerHTML = `
            <label for="dropzone-file"
                class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                    </svg>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click
                            to upload</span> or drag and drop</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                </div>
                <input id="dropzone-file" name="foto" type="file" class="hidden" accept="image/*"
                    onchange="previewImage(event)" />
            </label>
        `;
    }
    </script>

</x-layout.layout>