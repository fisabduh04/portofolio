<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Kelas', 'href' => ''],
        ['name' => 'Kelas-Siswa', 'href' => '/kelassiswa'],
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">

        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Kelas Siswa</h1>


        {{-- Kumpulan Tombol --}}
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <a type="button" id="tambah" onclick="muncul()"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                </svg>
                Tambah
            </a>
            <button type="button" data-modal-target="small-modal" data-modal-toggle="small-modal"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path
                        d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z" />
                    <path
                        d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
                </svg>
                Import Excel
            </button>

            <a type="button" href="{{ route('kelas-siswa-export') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-3 h-3 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 12.25V1m0 11.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M4 19v-2.25m6-13.5V1m0 2.25a2.25 2.25 0 0 0 0 4.5m0-4.5a2.25 2.25 0 0 1 0 4.5M10 19V7.75m6 4.5V1m0 11.25a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5ZM16 19v-2" />
                </svg>
                Export Excel
            </a>

            <button type="submit" data-modal-target="popup-modal" data-modal-toggle="popup-modal"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                </svg>
                Hapus
            </button>

            <div id="popup-modal" tabindex="-1"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative w-full max-w-md max-h-full p-4">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <button type="button"
                            class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="popup-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 text-center md:p-5">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah anda akan
                                menghapus data ini ?</h3>
                            <button data-modal-hide="popup-modal" type="submit" form="MyForm"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Ya Benar !
                            </button>
                            <button data-modal-hide="popup-modal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                                cancel</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <hr class="h-px my-3 bg-gray-200 border-0 dark:bg-gray-700">
        <form action="{{ isset($pemetaan) ? route('kelassiswa.update', $pemetaan->id) : route('kelassiswa.store') }}"
            id="InputSiswa" method="POST">
            @csrf
            @isset($pemetaan)
                @method('PUT')
            @endisset
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                        Ajaran</label>
                    <select id="tahun" name="tahun"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled
                            {{ old('tahun', isset($pemetaan) ? $pemetaan->tahun_id : '') == '' ? 'selected' : '' }}>
                            Tahun
                            Ajaran</option>
                        @foreach ($tahun as $key => $t)
                            <option value="{{ $t->id }}"
                                {{ old('tahun', isset($pemetaan) ? $pemetaan->tahun_id : '') == $t->id ? 'selected' : '' }}>
                                {{ $t->tahun }}-{{ $t->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kelas"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                    <select id="kelas" name="kelas"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled
                            {{ old('kelas', isset($pemetaan) ? $pemetaan->kelas_id : '') == '' ? 'selected' : '' }}>
                            Kelas
                        </option>
                        @foreach ($kelas as $key => $k)
                            <option value="{{ $k->id }}"
                                {{ old('kelas', isset($pemetaan) ? $pemetaan->kelas_id : '') == $k->id ? 'selected' : '' }}>
                                {{ $k->kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="siswa"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Siswa</label>
                    <select id="kelas" name="siswa"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="" disabled
                            {{ old('siswa', isset($pemetaan) ? $pemetaan->siswa_id : '') == '' ? 'selected' : '' }}>
                            Siswa
                        </option>
                        @foreach ($siswa as $key => $s)
                            <option value="{{ $s->id }}"
                                {{ old('siswa', isset($pemetaan) ? $pemetaan->siswa_id : '') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}-{{ $s->nipd }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="keterangan"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                    <select id="keterangan" name="ket"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option>Pilih</option>
                        <option value="aktif">Aktif</option>
                        <option value="do">Berhenti</option>
                        <option value="naik">Naik Kelas</option>
                        <option value="tinggal">Tidak Naik Kelas</option>
                    </select>
                </div>
                <div>

                    <!-- Tombol Submit -->

                    <button type="submit"
                        class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 shadow-lg shadow-purple-500/50 dark:shadow-lg dark:shadow-purple-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">{{ isset($pemetaan) ? 'Update' : 'Simpan' }}</button>
                </div>
            </div>
        </form>


        <hr class="h-px my-4 bg-gray-200 border-0 dark:bg-gray-700">



        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div
                class="flex flex-wrap items-center justify-between pb-4 space-y-4 flex-column sm:flex-row sm:space-y-0 me-6 ms-6">
                <div class="flex items-center space-x-2">
                    <label for="items-per-page" class="sr-only">Items per page</label>
                    <select id="perPage" name="per_page"
                        class="block py-2 px-4 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 min-w-[90px]">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="10000000">All</option>
                    </select>
                    <small class="inline-block text-gray-500 dark:text-gray-400">Items</small>
                </div>


                {{-- Filter  --}}
                <form action="{{ route('kelassiswa.index') }}" method="GET" class="flex items-center space-x-4">

                    <div class="flex items-center space-x-2">
                        <label for="filter-tahun"
                            class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">Tahun
                            Ajaran:</label>
                        <select id="filter-tahun" name="filter_tahun"
                            class="block p-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onchange="this.form.submit()">
                            <option value="all">Semua</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('filter_tahun') == $t->id ? 'selected' : '' }}>
                                    {{ $t->tahun }} - {{ $t->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-2 ms-4">
                        <label for="filter-kelas"
                            class="text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">Kelas:</label>
                        <select id="filter-kelas" name="filter_kelas"
                            class="block p-2 px-6 text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onchange="this.form.submit()">
                            <option value="all">Semua</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="relative ms-2">
                        <div
                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="table-search" type="text" name="search"
                            value="{{ request('search') }}"
                            class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for items">
                    </div>

                </form>


                <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400 mt-2">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="checkAll"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkAll" class="sr-only">checkbox</label>
                                </div>
                            </th>

                            <th scope="col" class="px-2 py-3">
                                <div class="flex items-center">No
                                    <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg></a>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                Action
                            </th>

                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">NIPD
                                    <a
                                        href="?sort=nipd&direction={{ request('sort') === 'nipd' && request('direction') === 'asc' ? 'desc' : 'asc' }}">
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'nipd' && request('direction') == 'asc' ? 'rotate-180' : '' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086
                1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0
                L6.837 7.952a1.9 1.9 0 0 0-.11 1.986
                2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087
                1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05
                a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">
                                    Nama
                                    <a
                                        href="?sort=nama&direction={{ request('sort') == 'nama' && request('direction') == 'asc' ? 'desc' : 'asc' }}">
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'nama' && request('direction') == 'asc' ? 'rotate-180' : '' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086
                1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0
                L6.837 7.952a1.9 1.9 0 0 0-.11 1.986
                2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087
                1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05
                a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg>
                                    </a>
                                </div>
                            </th>


                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">Kelas
                                    <a
                                        href="?sort=kelas&direction={{ request('sort') === 'kelas' && request('direction') === 'asc' ? 'desc' : 'asc' }}">
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'kelas' && request('direction') == 'asc' ? 'rotate-180' : '' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg></a>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">Tahun Ajaran
                                    <a
                                        href="?sort=ket&direction={{ request('sort') === 'tahun' && request('direction') === 'asc' ? 'desc' : 'asc' }}">
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'tahun' && request('direction') == 'asc' ? 'rotate-180' : '' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">>
                                            <path
                                                d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg></a>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <div class="flex items-center">Keterangan
                                    <a
                                        href="?sort=ket&direction={{ request('sort') === 'tahun' && request('direction') === 'asc' ? 'desc' : 'asc' }}">
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'tahun' && request('direction') == 'asc' ? 'rotate-180' : '' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                        </svg></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pemetaans->isNotEmpty())
                            <form id="MyForm"
                                action="{{ route('kelassiswa.destroy', $pemetaans->first()->id ?? '') }}"
                                method="POST">
                                @csrf @method('delete')
                        @endif
                        @foreach ($pemetaans as $pemetaan)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox" type="checkbox" name="id[]"
                                            value="{{ $pemetaan->id }}"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-{{ $pemetaan->id }}"
                                            class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <td class="px-2 py-4">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-4">
                                    <a type="button" href="{{ route('kelassiswa.edit', $pemetaan->id) }}"
                                        onclick="edit()"
                                        class="p-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>

                                        Edit
                                    </a>
                                </td>

                                <td class="px-4 py-4">
                                    {{ $pemetaan->siswa->nipd }}
                                </td>
                                <th scope="row"
                                    class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $pemetaan->siswa->nama }}
                                </th>
                                <td class="px-4 py-4">
                                    {{ $pemetaan->kelas->kelas }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ $pemetaan->tahun->tahun }}-{{ $pemetaan->tahun->semester }}
                                </td>
                                <td class="px-4 py-4">
                                    @if ($pemetaan->ket == 'aktif')
                                        <span
                                            class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400">Aktif</span>
                                    @elseif ($pemetaan->ket == 'do')
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400">DO</span>
                                    @elseif ($pemetaan->ket == 'naik')
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-blue-400 border border-blue-400">Naik
                                            Kelas</span>
                                    @elseif ($pemetaan->ket == 'tinggal')
                                        <span
                                            class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-400 border border-yellow-400">Tinggal
                                            Kelas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </form>
                    </tbody>
                </table>

            </div>
            <div class="mt-4">
                {{ $pemetaans->withQueryString()->links() }}
            </div>
        </div>


        <!-- Small Modal untuk Import Excel -->
        <div id="small-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Import Excel Siswa dalam Kelas
                        </h3>
                        <button type="button"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="small-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 space-y-4 md:p-5">
                        <form action="{{ route('kelas-siswa-import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                                Proses ini menginput siswa ke dalam setiap kelas sehingga data siswa dan kelas harus
                                valid
                            </p>

                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="small_size">Import
                                File Excel Anda</label>
                            <input accept=".xlsx, .csv, .xls" name="file"
                                class="block w-full mb-5 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="small_size" type="file">

                            <button type="submit"
                                class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 shadow-lg shadow-purple-500/50 dark:shadow-lg dark:shadow-purple-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center"><svg
                                    class="w-5 h-5 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 5v9m-5 0H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2M8 9l4-5 4 5m1 8h.01" />
                                </svg>
                                Unggah</button>
                            </from>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
                        <button data-modal-hide="small-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Script JS --}}
        <script>
            // Handle perPage dropdown change
            document.getElementById('perPage').addEventListener('change', function() {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('per_page', this.value); // Update per_page query parameter
                window.location.search = urlParams.toString(); // Reload with new parameters
            });

            // Handle select all checkbox
            document.getElementById('checkAll').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[type="checkbox"][name="id[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });


            function muncul() {
                const form = document.getElementById('InputSiswa');
                form.classList.toggle('hidden'); // Toggle visibility of the form
                form.reset(); // Reset form fields
                form.querySelectorAll('select').forEach(select => {
                    select.selectedIndex = 0; // Reset select elements to default
                });
                form.querySelectorAll('input[type="text"]').forEach(input => {
                    input.value = ''; // Clear text inputs
                });
                form.query
            }

            function edit(params) {
                const form = document.getElementById('InputSiswa');
                form.classList.remove('hidden'); // Show the form

            }

            document.getElementById('table-search').addEventListener('input', function() {
                clearTimeout(window.delay);
                window.delay = setTimeout(() => {
                    if (this.value.trim() !== '') {
                        this.form.submit(); // submit form induk langsung
                    }
                }, 400);
            });
        </script>





</x-layout.layout>
