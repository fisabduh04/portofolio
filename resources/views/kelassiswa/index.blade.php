<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Rombongan Belajar', 'href' => route('kelassiswa.index')],
    ]" />

    {{-- Main Content Container (Style Jadwal) --}}
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-200 dark:border-gray-700">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-full md:w-auto">
                {{-- Data Count --}}
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 font-sans">Total Data: {{ $pemetaans->total() }}</span>
            </div>
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                {{-- Primary Action: Tambah --}}
                <x-btn onclick="tambah()" color="blue" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Tambah Data</span>
                </x-btn>

                {{-- Secondary Action: Refresh --}}
                <x-btn onclick="location.reload()" color="light" size="sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span>Refresh</span>
                </x-btn>
            </div>
        </div>

        {{-- Panel Input (Setup Data) --}}
        <div id="InputSiswaPanel" class="hidden bg-gray-50 dark:bg-gray-800 p-6 border-b border-gray-200 dark:border-gray-700 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm pointer-events-none shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <h4 id="panelTitle" class="font-bold text-lg text-gray-800 dark:text-white tracking-tight">Input Data Rombongan Belajar</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Atur siswa dan kelas secara masal atau satu per satu.</p>
                    </div>
                </div>
                <button type="button" onclick="tutup()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ isset($pemetaan) ? route('kelassiswa.update', $pemetaan->id) : route('kelassiswa.store') }}"
                id="InputSiswa" method="POST">
                @csrf
                @isset($pemetaan)
                    @method('PUT')
                @endisset

                <!-- Global Settings (Tahun Ajaran) -->
                <div class="mb-6 bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <label for="tahun" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Tahun Ajaran
                                <span class="text-xs font-normal text-gray-500 ml-1">(Berlaku untuk semua data di bawah)</span>
                            </label>
                            <select id="tahun" name="tahun_id" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white transition-all">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahun as $t)
                                    <option value="{{ $t->id }}">
                                        {{ $t->tahun }} - {{ $t->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Repeater Header (Desktop) -->
                <div class="hidden md:grid grid-cols-12 gap-4 mb-2 px-4 font-semibold text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <div class="col-span-12 md:col-span-5">Siswa</div>
                    <div class="col-span-12 md:col-span-4">Kelas</div>
                    <div class="col-span-12 md:col-span-2">Status</div>
                    <div class="col-span-12 md:col-span-1 text-center">Aksi</div>
                </div>

                <!-- Repeater Container -->
                <div id="repeater-container" class="space-y-3 mb-6">
                    <!-- Row 1 (Default) -->
                    <div class="repeater-row grid grid-cols-1 md:grid-cols-12 gap-4 items-start bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700 group">
                        <!-- Siswa -->
                        <div class="md:col-span-5">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Siswa</label>
                            <select name="siswa_id[]" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih Siswa</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->nipd }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Kelas -->
                        <div class="md:col-span-4">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Kelas</label>
                            <select name="kelas_id[]" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Keterangan -->
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Status</label>
                            <select name="ket[]" required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="aktif">Aktif</option>
                                <option value="berhenti">Berhenti</option>
                                <option value="naik">Naik Kelas</option>
                                <option value="tinggal">Tidak Naik Kelas</option>
                            </select>
                        </div>

                        <!-- Delete Button -->
                        <div class="md:col-span-1 flex justify-center items-center h-full pt-1">
                            <button type="button" onclick="removeRow(this)" class="text-gray-400 hover:text-red-500 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all opacity-100 md:opacity-0 md:group-hover:opacity-100" title="Hapus Baris">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Row Button -->
                <button type="button" onclick="addRow()" class="w-full py-3 mb-6 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 font-medium hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:border-gray-600 dark:hover:border-blue-400 dark:hover:text-blue-400 dark:hover:bg-gray-800 transition-all duration-200 flex items-center justify-center group">
                    <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 flex items-center justify-center mr-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Siswa Lain
                </button>

                <!-- Template for Javascript cloning (Hidden) -->
                <template id="row-template">
                    <div class="repeater-row grid grid-cols-1 md:grid-cols-12 gap-4 items-start bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700 group animate-fade-in-down">
                        <div class="md:col-span-5">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Siswa</label>
                            <select name="siswa_id[]" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih Siswa</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->nipd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Kelas</label>
                            <select name="kelas_id[]" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Status</label>
                            <select name="ket[]" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="aktif">Aktif</option>
                                <option value="do">Berhenti</option>
                                <option value="naik">Naik Kelas</option>
                                <option value="tinggal">Tidak Naik Kelas</option>
                            </select>
                        </div>
                        <div class="md:col-span-1 flex justify-center items-center h-full pt-1">
                            <button type="button" onclick="removeRow(this)" class="text-gray-400 hover:text-red-500 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all opacity-100 md:opacity-0 md:group-hover:opacity-100" title="Hapus Baris">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="tutup()" class="px-5 py-2.5 text-sm font-medium text-gray-700 focus:outline-none bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="submitButton" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>

        {{-- Secondary Toolbar --}}
        <div class="flex flex-col md:flex-row items-center justify-end space-y-3 md:space-y-0 md:space-x-4 p-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            {{-- Right: Search & Filters --}}
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                <form action="{{ route('kelassiswa.index') }}" method="GET" class="flex flex-wrap items-center justify-end gap-3">
                    {{-- Search --}}
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="Cari Siswa...">
                    </div>

                    {{-- Filter Tahun --}}
                    <select name="filter_tahun" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full md:w-32 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="all">Tahun</option>
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}" {{ request('filter_tahun') == $t->id ? 'selected' : '' }}>
                                {{ $t->tahun }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filter Kelas --}}
                    <select name="filter_kelas" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full md:w-32 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="all">Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                {{ $k->kelas }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Actions Dropdown --}}
                    <div class="relative">
                        <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all duration-200" type="button">
                            <span>Actions</span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="actionsDropdown" class="hidden z-10 w-48 bg-white rounded-base divide-y divide-gray-100 shadow-xl dark:bg-gray-700 dark:divide-gray-600 border border-gray-200 dark:border-gray-600 !right-0">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actionsDropdownButton">
                                <li>
                                    <a href="#" onclick="exportData(event)" class="flex items-center gap-3 py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors duration-200">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>Export Results</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" data-modal-target="small-modal" data-modal-toggle="small-modal" class="flex items-center gap-3 py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors duration-200">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span>Import CSV</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="py-2">
                                <button type="button" data-modal-target="popup-modal-bulk-delete" data-modal-toggle="popup-modal-bulk-delete" class="flex items-center w-full gap-3 py-2 px-4 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 dark:text-red-400 dark:hover:text-red-300 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Delete Selected</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Per Page --}}
                    <select name="per_page" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full md:w-36 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach([10, 25, 50, 100] as $p)
                            <option value="{{ $p }}" {{ request('per_page') == $p ? 'selected' : '' }}>{{ $p }} Items</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>



        {{-- Table Container --}}
        <form id="MyForm" action="{{ route('kelassiswa.bulkDelete') }}" method="POST" class="hidden">
            @csrf @method('delete')
        </form>
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
            <table class="w-full text-sm text-left text-body rtl:text-right">
                <thead class="text-xs text-heading uppercase bg-neutral-secondary-soft border-b border-default">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkAll" type="checkbox" onclick="toggleCheckAll(this)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <a href="{{ route('kelassiswa.index', array_merge(request()->all(), ['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center text-heading hover:text-blue-600">
                                No
                                @if(request('sort') == 'id')
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                        <th scope="col" class="px-4 py-3">
                            <a href="{{ route('kelassiswa.index', array_merge(request()->all(), ['sort' => 'nipd', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center text-heading hover:text-blue-600">
                                NIPD
                                @if(request('sort') == 'nipd')
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <a href="{{ route('kelassiswa.index', array_merge(request()->all(), ['sort' => 'nama', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center text-heading hover:text-blue-600">
                                Nama Siswa
                                @if(request('sort') == 'nama')
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <a href="{{ route('kelassiswa.index', array_merge(request()->all(), ['sort' => 'kelas', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center text-heading hover:text-blue-600">
                                Kelas
                                @if(request('sort') == 'kelas')
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <a href="{{ route('kelassiswa.index', array_merge(request()->all(), ['sort' => 'tahun', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center text-heading hover:text-blue-600">
                                Tahun Ajaran
                                @if(request('sort') == 'tahun')
                                    <svg class="w-3 h-3 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if ($pemetaans->isNotEmpty())
                         {{-- Form moved outside --}}
                    @endif
                        @foreach ($pemetaans as $pemetaan)
                            <tr
                                class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-{{ $pemetaan->id }}" form="MyForm" type="checkbox" name="id[]"
                                            value="{{ $pemetaan->id }}"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-{{ $pemetaan->id }}"
                                            class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <td class="px-2 py-4">
                                    {{-- Use paginator firstItem() so numbering continues across pages --}}
                                    {{ ($pemetaans->firstItem() ?? 0) + $loop->index }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-btn type="button" 
                                            onclick="edit({{ $pemetaan->id }}, '{{ $pemetaan->siswa_id }}', '{{ $pemetaan->kelas_id }}', '{{ $pemetaan->tahun_id }}', '{{ $pemetaan->ket }}')"
                                            icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" />
                                        
                                        <x-btn type="button" 
                                            data-modal-target="popup-modal-{{ $pemetaan->id }}" 
                                            data-modal-toggle="popup-modal-{{ $pemetaan->id }}" 
                                            icon="trash" color="red" variant="ghost" size="sm" class="!p-2" title="Hapus" />
                                        
                                        <x-modal.hapus id="{{ $pemetaan->id }}" action="{{ route('kelassiswa.destroy', $pemetaan->id) }}" message="Apakah Anda yakin ingin menghapus siswa {{ $pemetaan->siswa->nama }}?" />
                                    </div>
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
                                    <select onchange="updateStatus({{ $pemetaan->id }}, this)"
                                        class="text-xs rounded-lg block w-full p-2 border transition-colors duration-200 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="aktif" class="text-green-600 bg-green-50" {{ $pemetaan->ket == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="do" class="text-red-600 bg-red-50" {{ $pemetaan->ket == 'do' ? 'selected' : '' }}>Berhenti</option>
                                        <option value="naik" class="text-blue-600 bg-blue-50" {{ $pemetaan->ket == 'naik' ? 'selected' : '' }}>Naik Kelas</option>
                                        <option value="tinggal" class="text-orange-600 bg-orange-50" {{ $pemetaan->ket == 'tinggal' ? 'selected' : '' }}>Tinggal Kelas</option>
                                    </select>
                                    <span id="status-msg-{{ $pemetaan->id }}" class="hidden text-[10px] mt-1 italic"></span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            
            {{-- Pagination Footer (Style Jadwal) --}}
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                {{ $pemetaans->withQueryString()->links() }}
            </div>
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
                            </form>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5 dark:border-gray-600">
                        <button data-modal-hide="small-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        {{-- Bulk Delete Modal (Moved here for better DOM handling) --}}
        <x-modal.hapus id="bulk-delete" formTarget="MyForm" message="Apakah Anda yakin ingin menghapus data yang dipilih?" />

        {{-- Script JS --}}
        <script>
            // Handle perPage dropdown change
            const perPageSelect = document.querySelector('select[name="per_page"]');
            if(perPageSelect) {
                perPageSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }

            // DOM Elements
            const panel = document.getElementById('InputSiswaPanel');
            const form = document.getElementById('InputSiswa');
            const submitButton = document.getElementById('submitButton');
            const container = document.getElementById('repeater-container');
            const template = document.getElementById('row-template');
            const panelTitle = document.getElementById('panelTitle');

            // --- Repeater Logic ---

            function addRow() {
                if(!template || !container) return;
                const clone = template.content.cloneNode(true);
                container.appendChild(clone);
            }

            function removeRow(button) {
                if (container.querySelectorAll('.repeater-row').length > 1) {
                    button.closest('.repeater-row').remove();
                } else {
                    alert("Minimal satu baris data harus ada.");
                }
            }

            // --- Modal/Panel Logic ---

            function tambah() {
                // 1. Reset Form
                if(form) {
                    form.reset();
                    form.action = "{{ route('kelassiswa.store') }}";
                    
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                }

                if(panelTitle) panelTitle.innerText = "Tambah Data (Bulk Input)";
                if(submitButton) {
                    submitButton.innerText = "Simpan Semua";
                    submitButton.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                    submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
                }

                // 2. Reset Repeater
                if(container) {
                    container.innerHTML = ''; 
                    addRow(); 
                }

                // 3. Show Add Row Button
                const addRowBtn = document.querySelector('button[onclick="addRow()"]');
                if(addRowBtn) addRowBtn.style.display = 'flex';

                // 4. Show Panel
                if(panel) panel.classList.remove('hidden');
                
                // Show 'tambah' in previous code was 'muncul'. 
                // Previous code: onclick="muncul()"
                // I changed onclick to 'tambah()' in the Header Section (Part 1).
            }
            
            // Backward compatibility if user clicks old button maybe? 
            // I updated the button to onclick="tambah()" in Part 1.
            function muncul() { tambah(); }

            function edit(id, siswa_id, kelas_id, tahun_id, ket) {
                // 1. Setup Form
                if(form) {
                    form.action = `/kelassiswa/${id}`;
                    
                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        form.appendChild(methodInput);
                    } else {
                        methodInput.value = 'PUT';
                    }
                }

                if(panelTitle) panelTitle.innerText = "Edit Data Siswa";
                if(submitButton) {
                    submitButton.innerText = "Update Data";
                    submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                    submitButton.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
                }

                // 2. Prepare Repeater (Single Row)
                if(container) {
                    container.innerHTML = '';
                    addRow(); 
                    
                    const row = container.querySelector('.repeater-row');
                    
                    if(row) {
                        // 3. Populate & Rename for Single Update
                        const inputTahun = document.getElementById('tahun');
                        if(inputTahun) {
                            inputTahun.name = 'tahun'; 
                            inputTahun.value = tahun_id;
                        }

                        const inputSiswa = row.querySelector('select[name="siswa_id[]"]');
                        if(inputSiswa) {
                            inputSiswa.name = 'siswa'; 
                            inputSiswa.value = siswa_id;
                        }

                        const inputKelas = row.querySelector('select[name="kelas_id[]"]');
                        if(inputKelas) {
                            inputKelas.name = 'kelas'; 
                            inputKelas.value = kelas_id;
                        }

                        const inputKet = row.querySelector('select[name="ket[]"]');
                        if(inputKet) {
                            inputKet.name = 'ket'; 
                            inputKet.value = ket;
                        }
                        
                        // Hide Remove Button in Row
                        const removeBtn = row.querySelector('button[onclick="removeRow(this)"]');
                        if(removeBtn) removeBtn.style.display = 'none';
                    }
                }

                // 4. Hide Add Row Button
                const addRowBtn = document.querySelector('button[onclick="addRow()"]');
                if(addRowBtn) addRowBtn.style.display = 'none';
                
                // 5. Show Panel
                if(panel) panel.classList.remove('hidden');
            }

            function tutup() {
                if(panel) panel.classList.add('hidden');
                
                // Reset to default name for bulk input
                const inputTahun = document.getElementById('tahun');
                if(inputTahun) inputTahun.name = 'tahun_id';
            }

            // --- Inline Status Update (Fetch API) ---
            function updateStatus(id, selectElement) {
                const statusBaru = selectElement.value;
                const msgSpan = document.getElementById(`status-msg-${id}`);

                // Update Color Immediately
                updateSelectColor(selectElement);

                // Show "Saving..." indicator
                if(msgSpan) {
                    msgSpan.innerText = 'Menyimpan...';
                    msgSpan.classList.remove('hidden', 'text-green-600', 'text-red-600');
                    msgSpan.classList.add('text-gray-500');
                }
                selectElement.disabled = true; // Prevent multiple changes while saving

                fetch(`/kelassiswa/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        ket: statusBaru
                    })
                })
                .then(response => {
                    if (response.ok) {
                        // Success Feedback
                        if(msgSpan) {
                            msgSpan.innerText = 'Tersimpan!';
                            msgSpan.classList.remove('text-gray-500');
                            msgSpan.classList.add('text-green-600');
                            
                            // Hide message after 2 seconds
                            setTimeout(() => {
                                msgSpan.classList.add('hidden');
                            }, 2000);
                        }
                    } else {
                        // Error Feedback
                        throw new Error('Gagal menyimpan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Gagal menyimpan perubahan status. Periksa koneksi internet.");
                    if(msgSpan) {
                        msgSpan.innerText = 'Gagal!';
                        msgSpan.classList.remove('text-gray-500');
                        msgSpan.classList.add('text-red-600');
                    }
                })
                .finally(() => {
                    selectElement.disabled = false; // Re-enable select
                });
            }

            // Function to set color based on value
            function updateSelectColor(select) {
                // Reset base classes
                select.className = "text-xs rounded-lg block w-full p-2 border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:bg-gray-700 dark:border-gray-600";
                
                switch(select.value) {
                    case 'aktif':
                        select.classList.add('bg-green-50', 'text-green-700', 'border-green-300', 'focus:ring-green-500');
                        break;
                    case 'do': // Berhenti
                        select.classList.add('bg-red-50', 'text-red-700', 'border-red-300', 'focus:ring-red-500');
                        break;
                    case 'naik':
                        select.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-300', 'focus:ring-blue-500');
                        break;
                    case 'tinggal':
                        select.classList.add('bg-orange-50', 'text-orange-700', 'border-orange-300', 'focus:ring-orange-500');
                        break;
                    default:
                        select.classList.add('bg-gray-50', 'text-gray-900', 'border-gray-300', 'focus:ring-blue-500');
                }
            }

            // Initialize colors on load
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('select[onchange^="updateStatus"]').forEach(select => {
                    updateSelectColor(select);
                });
            });        
            // Global Checkbox Logic
            function toggleCheckAll(source) {
                const checkboxes = document.querySelectorAll('input[type="checkbox"][name="id[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = source.checked);
            }
            
            // Row Checkbox Listener
            document.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'id[]') {
                    const checkAll = document.getElementById('checkAll');
                    if (!checkAll) return;
                    
                    const rowCheckboxes = document.querySelectorAll('input[type="checkbox"][name="id[]"]');
                    const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);

                    checkAll.checked = allChecked;
                    checkAll.indeterminate = someChecked && !allChecked;
                }
            });

            // Search Auto-Submit
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(window.delay);
                    window.delay = setTimeout(() => {
                        this.form.submit();
                    }, 1000); // 1 second delay
                });
            }

            // Export Data
            function exportData(e) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('input[name="id[]"]:checked');
                let ids = [];
                checkboxes.forEach((checkbox) => {
                    ids.push(checkbox.value);
                });

                let url = "{{ route('kelas-siswa-export') }}";
                if (ids.length > 0) {
                    url += '?ids=' + ids.join(',');
                }

                window.location.href = url;
            }
        </script>

</x-layout.layout>
