<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jadwal', 'href' => 'jadwal'],
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Jadwal Mata Pelajaran</h1>

        <div class="flex flex-col space-y-2 md:flex-row md:space-x-4 md:space-y-0">
            <x-btn text="Tambah" icon="plus" onclick="tambah()" />
            <x-btn text="Hapus" icon="trash" color="red" type="submit" />
        </div>


        {{-- <form action="\jadwal" method="POST">
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                @csrf
                <div>
                    <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                        Ajaran</label>
                    <select id="tahun" name="tahun_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Pilih Tahun Ajaran</option>
                        @if ($tahun->isEmpty())
                            <option value="">Tidak ada tahun ajaran</option>
                        @endif
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}">{{ $t->tahun }}-{{ $t->semester }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="kelas"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                    <select id="kelas" name="kelas_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Pilih Kelas</option>
                        @if ($kelas->isEmpty())
                            <option value="">Tidak ada kelas</option>
                        @endif
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kelas }}-{{ $k->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="pegawai"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">pegawai_id</label>
                    <select id="pegawai" name="pegawai_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Pilih Pegawai</option>
                        @if ($pegawai->isEmpty())
                            <option value="">Tidak ada pegawai</option>
                        @endif
                        @foreach ($pegawai as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mapel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mata
                        Pelajaran</label>
                    <select id="mapel_id" name="mapel_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="hari"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari</label>
                    <select id="hari" name="hari"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="">Pilih Hari</option>
                        @foreach ($hari as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex gap-4">
                        <!-- Jam ke -->
                        <div class="flex flex-col w-1/4">
                            <label for="jam" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                                ke:</label>
                            <input type="number" id="jam" placeholder="Jam ke" name="jam"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                        </div>

                        <!-- Jam Mulai -->
                        <div class="flex flex-col w-1/3">
                            <label for="mulai" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                                Mulai:</label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="time" id="mulai" name="mulai"
                                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    min="07:00" max="18:00" value="07:00" required />
                            </div>
                        </div>

                        <!-- Jam Akhir -->
                        <div class="flex flex-col w-1/3">
                            <label for="akhir" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                                Akhir:</label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="time" id="akhir" name="akhir"
                                    class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    min="07:00" max="18:00" value="08:00" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="ket"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                    <input type="text" id="ket" placeholder="Keterangan" name="ket"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
            </div>

            <button type="submit"
                class="text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Simpan</button>

        </form> --}}

        <hr class="my-1 border-gray-200 dark:border-gray-700" />

        {{-- Table data  --}}

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <form action="/jadwal" method="GET" id="searchForm"
                class="flex flex-wrap justify-between items-center gap-3 p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">

                {{-- Kiri: Filter --}}
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Dropdown Per Page --}}
                    <div>
                        <label for="per_Page" class="sr-only">Per Page</label>
                        <select id="per_Page" name="per_Page"
                            class="h-10 w-28 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 
                    focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                    dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit()">
                            <option value="10" {{ request('per_Page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_Page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_Page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_Page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    {{-- <input type="hidden" name="tahun_id" value="{{ request('tahun_id') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}"> --}}
                    {{-- Dropdown Tahun Akademik --}}
                    <div>
                        <label for="tahun" class="sr-only">Tahun Akademik</label>
                        <select id="tahun" name="filter_tahun" value="{{ request('filter_tahun') }}"
                            class="h-10 w-44 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 
                    focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                    dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit()">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}"
                                    {{ request('filter_tahun') == $t->id ? 'selected' : '' }}>
                                    {{ $t->tahun }}-{{ $t->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown Kelas --}}
                    <div>
                        <label for="kelas" class="sr-only">Kelas</label>
                        <select id="kelas" name="filter_kelas" value=""
                            class="h-10 w-44 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 
                    focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                    dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit()">
                            <option value=""{{ request('filter_kelas') == '' ? 'selected' : '' }}>Semua Kelas
                            </option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kanan: Search --}}
                    <div class="relative">
                        <label for="search" class="sr-only">Search</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            class="h-10 w-80 pl-10 pr-3 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 
                focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 
                dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Cari data..." value="{{ request('search') }}" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </form>





            {{-- Tabel Jadwal --}}
            <table id="data" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkAll" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkAll" class="sr-only">checkbox</label>
                            </div>
                        </th>

                        <th scope="col" class="px-3 py-3">Action</th>

                        {{-- Kolom No --}}
                        <th scope="col" class="px-3 py-3">
                            <div class="flex items-center">
                                No
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'id',
                                        'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc',
                                    ]) }}">
                                    <svg class="w-3 h-3 ms-1.5 transform transition-transform duration-200
                        {{ request('sort') === 'id' && request('direction') === 'asc' ? 'rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </th>

                        {{-- Kolom KELAS --}}
                        <th scope="col" class="px-10 py-3">
                            <div class="flex items-center">
                                KELAS
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'kelas',
                                        'direction' => request('sort') === 'kelas' && request('direction') === 'asc' ? 'desc' : 'asc',
                                    ]) }}">
                                    <svg class="w-3 h-3 ms-1.5 transform transition-transform duration-200
                        {{ request('sort') === 'kelas' && request('direction') === 'asc' ? 'rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </th>

                        {{-- Kolom HARI --}}
                        <th scope="col" class="px-5 py-3">
                            <div class="flex items-center">
                                HARI
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'hari',
                                        'direction' => request('sort') === 'hari' && request('direction') === 'asc' ? 'desc' : 'asc',
                                    ]) }}">
                                    <svg class="w-3 h-3 ms-1.5 transform transition-transform duration-200
                        {{ request('sort') === 'hari' && request('direction') === 'asc' ? 'rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </th>

                        {{-- Kolom MAPEL --}}
                        <th scope="col" class="px-15 py-3">
                            <div class="flex items-center">
                                MATA PELAJARAN
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'mapel',
                                        'direction' => request('sort') === 'mapel' && request('direction') === 'asc' ? 'desc' : 'asc',
                                    ]) }}">
                                    <svg class="w-3 h-3 ms-1.5 transform transition-transform duration-200
                        {{ request('sort') === 'mapel' && request('direction') === 'asc' ? 'rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </th>

                        {{-- Kolom GURU --}}
                        <th scope="col" class="px-15 py-3">
                            <div class="flex items-center">
                                GURU
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'sort' => 'pegawai',
                                        'direction' => request('sort') === 'pegawai' && request('direction') === 'asc' ? 'desc' : 'asc',
                                    ]) }}">
                                    <svg class="w-3 h-3 ms-1.5 transform transition-transform duration-200
                        {{ request('sort') === 'pegawai' && request('direction') === 'asc' ? 'rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </th>

                        <th scope="col" class="px-4 py-3">JAM</th>
                        <th scope="col" class="px-4 py-3">Mulai</th>
                        <th scope="col" class="px-4 py-3">Akhir</th>
                        <th scope="col" class="px-10 py-3">Keterangan</th>
                        <th scope="col" class="px-10 py-3">Tahun Akademik</th>
                    </tr>
                </thead>

                <tbody id="jadwalTable">
                    @foreach ($jadwals as $j)
                        {{-- Baris tampilan --}}
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                            id="row-{{ $j->id }}">
                            <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-table-search-{{ $j->id }}" type="checkbox"
                                        value="{{ $j->id }}" name="id[]"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-table-search-{{ $j->id }}"
                                        class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex space-x-2">
                                    <button type="button" onclick="editRow({{ $j->id }})"
                                        class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center text-blue-700 rounded-lg hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group">
                                        <svg class="w-5 h-5 text-blue-700 group-hover:text-white dark:text-white"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                    </button>
                                    <form action="/jadwal/{{ $j->id }}" method="POST" class="inline">
                                        @csrf @method('delete')
                                        <input type="hidden" name="filter_tahun"
                                            value="{{ request('filter_tahun') }}">
                                        <input type="hidden" name="filter_kelas" id="filter_kelas"
                                            value="{{ request('filter_kelas') }}">

                                        <button type="submit"
                                            onclick="return confirm('Apakah anda akan menghapus data ini?')"
                                            class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group">
                                            <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-2 py-4 text-center">{{ $loop->iteration }}</td>
                            <td class="px-2 py-4 text-center">
                                @if ($j->kelas->kelas == 'X A TKJ')
                                    <x-badge color="blue" text="{{ $j->kelas->kelas }}" />
                                @elseif($j->kelas->kelas == 'X B TKJ')
                                    <x-badge color="green" text="{{ $j->kelas->kelas }}" />
                                @elseif($j->kelas->kelas == 'XI A TKJ')
                                    <x-badge color="yellow" text="{{ $j->kelas->kelas }}" />
                                @elseif($j->kelas->kelas == 'XI B TKJ')
                                    <x-badge color="purple" text="{{ $j->kelas->kelas }}" />
                                @elseif($j->kelas->kelas == 'XII A TKJ')
                                    <x-badge color="red" text="{{ $j->kelas->kelas }}" />
                                @elseif($j->kelas->kelas == 'XII B TKJ')
                                    <x-badge color="indigo" text="{{ $j->kelas->kelas }}" />
                                @else
                                    <x-badge color="gray" text="{{ $j->kelas->kelas }}" />
                                @endif
                            </td>
                            <td class="px-2 py-4 text-center">{{ $j->hari }}</td>
                            <td class="px-2 py-4 text-center">{{ $j->mapel->mapel }}</td>
                            <td class="px-2 py-4 text-center">{{ $j->pegawai->name }}</td>
                            <td class="px-2 py-4 text-center">{{ $j->jam }}</td>
                            <td class="px-2 py-4 text-center">{{ $j->mulai }}</td>
                            <td class="px-2 py-4 text-center">{{ $j->akhir }}</td>
                            <td class="px-2 py-4 text-center">
                                @if ($j->ket == null || $j->ket == '')
                                    <span class="text-gray-500">Tidak ada keterangan</span>
                                @elseif($j->ket == 'Tidak Aktif')
                                    <x-badge color="red" text="{{ $j->ket }}" />
                                @elseif($j->ket == 'aktif')
                                    <x-badge color="green" text="{{ $j->ket }}" />
                                @endif
                            </td>
                            <td class="px-2 py-4 text-center">{{ $j->tahun->tahun }}-{{ $j->tahun->semester }}</td>
                        </tr>

                        {{-- Inline edit form, now valid HTML: form is inside one TD colspan --}}
                        <form action="jadwal/{{ $j->id }}" method="POST" id="edit">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="tahun_id" value="{{ $j->tahun_id }}">
                            <input type="hidden" name="filter_tahun" value="{{ request('filter_tahun') }}">
                            <input type="hidden" name="filter_kelas" value="{{ request('filter_kelas') }}">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <tr id="edit-form-{{ $j->id }}"
                                class="hidden bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <!-- Checkbox -->
                                <td class="p-2">
                                    <input type="checkbox" disabled class="w-4 h-4 rounded" />
                                </td>

                                <!-- Action -->
                                <td class="p-2">
                                    <div class="flex gap-1">
                                        <button type="submit"
                                            class="flex items-center justify-center hover:bg-blue-700 text-white rounded px-2 py-1">
                                            ✔
                                        </button>
                                        <button type="button" onclick="cancelEditRow({{ $j->id }})"
                                            class="flex items-center justify-center hover:bg-red-600 hover:text-white text-white rounded px-2 py-1">
                                            ✖
                                        </button>
                                    </div>
                                </td>

                                <!-- No -->
                                <td class="p-2 text-center text-sm">{{ $loop->iteration }}</td>

                                <!-- Kelas -->
                                <td class="p-2">
                                    <select name="kelas_id" class="w-full border rounded-lg p-1 text-sm">
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" @selected($j->kelas_id == $k->id)>
                                                {{ $k->kelas }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Hari -->
                                <td class="p-2">
                                    <select name="hari" class="w-full border rounded-lg p-1 text-sm">
                                        @foreach ($hari as $h)
                                            <option value="{{ $h }}" @selected($j->hari == $h)>
                                                {{ $h }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Mapel -->
                                <td class="p-2">
                                    <select name="mapel_id" class="w-full border rounded-lg p-1 text-sm">
                                        @foreach ($mapel as $m)
                                            <option value="{{ $m->id }}" @selected($j->mapel_id == $m->id)>
                                                {{ $m->mapel }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Guru -->
                                <td class="p-2">
                                    <select name="pegawai_id" class="w-full border rounded-lg p-1 text-sm">
                                        @foreach ($pegawai as $p)
                                            <option value="{{ $p->id }}" @selected($j->pegawai_id == $p->id)>
                                                {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Jam -->
                                <td class="p-2">
                                    <input type="number" name="jam" value="{{ $j->jam }}"
                                        class="w-full border rounded-lg p-1 text-sm" />
                                </td>

                                <!-- Mulai -->
                                <td class="p-2">
                                    <input type="time" name="mulai" value="{{ $j->mulai }}"
                                        class="w-full border rounded-lg p-1 text-sm" />
                                </td>

                                <!-- Akhir -->
                                <td class="p-2">
                                    <input type="time" name="akhir" value="{{ $j->akhir }}"
                                        class="w-full border rounded-lg p-1 text-sm" />
                                </td>



                                <!-- Keterangan -->
                                <td class="p-2">
                                    <input type="text" name="ket" value="{{ $j->ket }}"
                                        class="w-full border rounded-lg p-1 text-sm" />
                                </td>
                            </tr>
                        </form>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $jadwals->appends(request()->query())->links() }}
        </div>

    </div>
</x-layout.layout>

<script>
    // Fungsi untuk buka form edit sebuah baris
    function editRow(id) {
        // sembunyikan form edit lain (jika ada)
        document.querySelectorAll('tr[id^="edit-form-"]').forEach(function(row) {
            row.classList.add('hidden');
            // pastikan tampilkan kembali row display jika sebelumnya tersembunyi
            let otherId = row.id.replace('edit-form-', '');
            let otherDisplay = document.getElementById('row-' + otherId);
            if (otherDisplay) otherDisplay.classList.remove('hidden');
        });

        // sembunyikan row tampilan, tampilkan row edit
        const displayRow = document.getElementById('row-' + id);
        const editRowEl = document.getElementById('edit-form-' + id);
        if (displayRow) displayRow.classList.add('hidden');
        if (editRowEl) {
            editRowEl.classList.remove('hidden');
            // fokus ke elemen input pertama agar user bisa langsung edit
            const firstInput = editRowEl.querySelector('select, input, textarea, button');
            if (firstInput) firstInput.focus();
            // scroll into view kalau perlu
            editRowEl.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    // Fungsi untuk membatalkan edit (tutup form edit)
    function cancelEditRow(id) {
        const editRowEl = document.getElementById('edit-form-' + id);
        const displayRow = document.getElementById('row-' + id);
        if (editRowEl) editRowEl.classList.add('hidden');
        if (displayRow) displayRow.classList.remove('hidden');
    }

    // Hide all edit forms if user clicks outside any edit form or edit button
    document.addEventListener('click', function(event) {
        // apakah klik pada tombol edit (atau di dalamnya)?
        let isEditButton = !!event.target.closest('button[onclick^="editRow"]');
        // apakah klik di dalam row edit (atau di dalamnya)?
        let isEditForm = !!event.target.closest('tr[id^="edit-form-"]');

        if (!isEditButton && !isEditForm) {
            document.querySelectorAll('tr[id^="edit-form-"]').forEach(function(row) {
                if (!row.classList.contains('hidden')) {
                    row.classList.add('hidden');
                    // show corresponding display row
                    let id = row.id.replace('edit-form-', '');
                    let displayRow = document.getElementById('row-' + id);
                    if (displayRow) displayRow.classList.remove('hidden');
                }
            });
        }
    });




    function tambah() {

    }

    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="id[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });




    //search 
    const searchInput = document.getElementById('search');
    const form = document.getElementById('searchForm');
    let typingTimer;
    const doneTypingInterval = 1000;

    searchInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            form.submit();
        }, doneTypingInterval);
    });


    // Handle perPage dropdown change
    document.getElementById('perPage').addEventListener('change', function() {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('per_page', this.value); // Update per_page query parameter
        window.location.search = urlParams.toString(); // Reload with new parameters
    });
</script>
