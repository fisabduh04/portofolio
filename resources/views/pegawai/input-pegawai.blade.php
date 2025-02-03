<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Pegawai', 'href' => '/pegawai'],
        ['name' => 'Data Detail Pegawai', 'href' => '']
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">

        <button type="submit" form="myForm"
            class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">
            <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="M11 16h2m6.707-9.293-2.414-2.414A1 1 0 0 0 16.586 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.414a1 1 0 0 0-.293-.707ZM16 20v-6a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v6h8ZM9 4h6v3a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V4Z" />
            </svg>
            @isset($pegawai->id) Update @else Simpan @endisset
        </button>

        <hr class="h-px my-2 bg-gray-200 border-0 dark:bg-gray-700">
        <form id="myForm" @isset($pegawai->id) action="{{ route('pegawai.update', $pegawai->id) }}"
            @else action="/pegawai" @endisset method="POST" enctype="multipart/form-data">
            @isset($pegawai->id) @method('PUT') @endisset
            @csrf



            <div class="grid gap-6 mb-6 md:grid-cols-2">
                @isset($pegawai->foto)
                <img src="" alt="">
                @else
                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click
                                    to
                                    upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX.
                                800x400px)
                            </p>
                        </div>
                        <input id="dropzone-file" type="file" class="hidden" />
                    </label>
                </div>

                @endif


                <div class="grid gap-5">
                    <x-form.input name="name" :value="$pegawai->name??''" label="Nama Lengkap" />

                    <x-form.input name="nuptk" :value="$pegawai->nuptk??''" label="NUPTK" />

                    <x-form.input name="email" :value="$pegawai->email??''" label="Email" />
                </div>

                <x-form.input name="status" :value="$pegawai->status??''" label="Status" />
                <x-form.input name="aktif" :value="$pegawai->aktif??''" label="Aktif" />
                <x-form.input name="hp" :value="$pegawai->hp??''" label="No Hp/Telp" />
                <x-form.input name="kotalahir" :value="$pegawai->kotalahir??''" label="Kota Lahir" />
                <x-form.input name="tanggallahir" :value="$pegawai->tanggallahir??''" label="Tanggal Lahir" />

                <x-form.input name="alamat" :value="$pegawai->alamat??''" label="Alamat" />
                <x-form.input name="jenisptk" :value="$pegawai->jenisptk??''" label="Jenis PTK" />
                <x-form.input name="agama" :value="$pegawai->agama??''" label="Agama" />
                <x-form.input name="jk" :value="$pegawai->jk??''" label="Jenis Kelamin" />
                <x-form.input name="rt" :value="$pegawai->rt??''??''" label="RT" />
                <x-form.input name="rw" :value="$pegawai->rw??''??''" label="RW" />

                <x-form.input name="skpengangkatan" :value="$pegawai->skpengangkatan??''" label="SK Pengangkatan" />

                <x-form.input name="lembagapengangkatan" :value="$pegawai->lembagapengangkatan??''"
                    label="Lembaga Pengangkatan" />
                <x-form.input name="PangkatGolongan" :value="$pegawai->PangkatGolongan??''" label="Pangkat Golongan" />

                <x-form.input name="sumbergaji" :value="$pegawai->sumbergaji??''" label="Sumber Gaji" />
                <x-form.input name="ibukandung" :value="$pegawai->ibukandung??''" label="Ibu Kandung" />


                <x-form.input name="statusPerkawinan" :value="$pegawai->statusPerkawinan??''"
                    label="Status Perkawinan" />

                <x-form.input name="suamiistri" :value="$pegawai->suamiistri??''" label="Pasangan" />
                <x-form.input name="pekerjaansuamiIstri" :value="$pegawai->pekerjaansuamiIstri ??''"
                    label="Pekerjaan Pasangan (Suami atau Istri)" />
                <x-form.input name="npwp" :value="$pegawai->npwp??''" label="NPWP" />
                <x-form.input name="nonik" :value="$pegawai->nonik??''" label="NO NIK" />
                <x-form.input name="nokk" :value="$pegawai->nokk??''" label="NO KK" />
                <x-form.input name="foto" :value="$pegawai->foto??''" label="foto" />
            </div>
            <div class="flex justify-start">
                <button type="submit"
                    class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">
                    <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                            d="M11 16h2m6.707-9.293-2.414-2.414A1 1 0 0 0 16.586 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7.414a1 1 0 0 0-.293-.707ZM16 20v-6a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v6h8ZM9 4h6v3a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V4Z" />
                    </svg>
                    @isset($pegawai->id) Update @else Simpan @endisset
                </button>
            </div>
        </form>
    </div>
</x-layout.layout>