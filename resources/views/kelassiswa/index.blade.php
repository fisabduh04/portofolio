<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => '/dashboard'],
        ['name' => 'Kelas', 'href' => '/kelas'],
        ['name' => 'Rombongan Belajar', 'href' => '/kelassiswa'],
    ]" />

    {{-- Main Content Container (Style Jadwal) --}}
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-200 dark:border-gray-700">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-full md:w-auto">
                <h2 class="text-xl font-bold font-sans text-gray-900 dark:text-white">
                    Data Rombongan Belajar
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 font-sans">(Total: {{ $pemetaans->total() }})</span>
                </h2>
            </div>
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                <x-btn onclick="muncul()" icon="plus" text="Tambah Data" color="blue" />
                <x-btn onclick="location.reload()" icon="arrow-path" text="Refresh" color="light" />
            </div>
        </div>

        {{-- Panel Input (Setup Data) --}}
        <div id="InputSiswaPanel" class="hidden bg-gray-50 p-6 border-b border-gray-200 dark:bg-gray-700 dark:border-gray-600 transition-all">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                </div>
                <h4 id="panelTitle" class="font-bold text-sm text-gray-800 dark:text-white uppercase tracking-wider">Form Input Rombongan Belajar</h4>
            </div>

            <form action="{{ isset($pemetaan) ? route('kelassiswa.update', $pemetaan->id) : route('kelassiswa.store') }}"
                id="InputSiswa" method="POST">
                @csrf
                @isset($pemetaan)
                    @method('PUT')
                @endisset

                <div class="grid gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Tahun Ajaran -->
                    <div>
                        <label for="tahun" class="block mb-2 text-xs font-semibold text-gray-900 dark:text-white uppercase">Tahun Ajaran</label>
                        <select id="tahun" name="tahun"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Tahun</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}">
                                    {{ $t->tahun }} - {{ $t->semester }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas" class="block mb-2 text-xs font-semibold text-gray-900 dark:text-white uppercase">Kelas</label>
                        <select id="kelas" name="kelas"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">
                                    {{ $k->kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Siswa -->
                    <div>
                        <label for="siswa" class="block mb-2 text-xs font-semibold text-gray-900 dark:text-white uppercase">Siswa</label>
                        <select id="siswa" name="siswa"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Siswa</option>
                            @foreach ($siswa as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->nama }} - {{ $s->nipd }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block mb-2 text-xs font-semibold text-gray-900 dark:text-white uppercase">Keterangan</label>
                        <select id="keterangan" name="ket"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="aktif">Aktif</option>
                            <option value="do">Berhenti</option>
                            <option value="naik">Naik Kelas</option>
                            <option value="tinggal">Tidak Naik Kelas</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <x-btn type="submit" id="submitButton" text="Simpan Data" icon="check" color="green" />
                    <x-btn type="button" text="Batal" color="light" onclick="tutup()" />
                </div>
            </form>
        </div>

        {{-- Secondary Toolbar (Style Jadwal) --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 bg-white dark:bg-gray-900">
            
            {{-- Left: Bulk Actions --}}
            <div class="w-full md:w-auto">
                <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                    <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                    Actions
                </button>
                <div id="actionsDropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                        <li><a href="{{ route('kelas-siswa-export') }}" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Export Results</a></li>
                        <li><a href="#" data-modal-target="small-modal" data-modal-toggle="small-modal" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Import CSV</a></li>
                    </ul>
                    <div class="py-1">
                        <button type="submit" form="MyForm" onclick="return confirm('Apakah Anda yakin ingin menghapus data yang dipilih?')" class="block w-full text-left py-2 px-4 text-sm text-red-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-200 dark:hover:text-white">
                            Delete Selected
                        </button>
                    </div>
                </div>
            </div>

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
                        <input type="text" name="search" value="{{ request('search') }}"
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
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkAll" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                        <th scope="col" class="px-4 py-3">NIPD</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3">Kelas</th>
                        <th scope="col" class="px-4 py-3">Tahun Ajaran</th>
                        <th scope="col" class="px-4 py-3">Status</th>
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
                                    {{-- Use paginator firstItem() so numbering continues across pages --}}
                                    {{ ($pemetaans->firstItem() ?? 0) + $loop->index }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-btn type="button" 
                                            onclick="edit({{ $pemetaan->id }}, {{ $pemetaan->siswa_id }}, {{ $pemetaan->kelas_id }}, {{ $pemetaan->tahun_id }}, '{{ $pemetaan->ket }}')"
                                            icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" />
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

            document.getElementById('table-search').addEventListener('input', function() {
                clearTimeout(window.delay);
                window.delay = setTimeout(() => {
                    if (this.value.trim() !== '') {
                        this.form.submit(); // submit form induk langsung
                    }
                }, 1000);
            });

            function muncul() {
                const panel = document.getElementById('InputSiswaPanel');
                const form = document.getElementById('InputSiswa');
                const submitBtn = document.getElementById('submitButton');
                const panelTitle = document.getElementById('panelTitle');

                form.reset();
                panel.classList.toggle('hidden');

                if (!panel.classList.contains('hidden')) {
                    form.removeAttribute('action');
                    form.setAttribute('action', "{{ route('kelassiswa.store') }}");
                    form.querySelector('input[name="_method"]')?.remove();
                    
                    panelTitle.textContent = 'Setup Data Baru';
                    submitBtn.textContent = 'Simpan Data';
                }
            }

            function edit(id, siswa_id, kelas_id, tahun_id, ket) {
                const panel = document.getElementById('InputSiswaPanel');
                const form = document.getElementById('InputSiswa');
                const submitBtn = document.getElementById('submitButton');
                const panelTitle = document.getElementById('panelTitle');

                panel.classList.remove('hidden');
                form.setAttribute('action', `/kelassiswa/${id}`);

                if (!form.querySelector('input[name="_method"]')) {
                    const methodInput = document.createElement('input');
                    methodInput.setAttribute('type', 'hidden');
                    methodInput.setAttribute('name', '_method');
                    methodInput.setAttribute('value', 'PUT');
                    form.appendChild(methodInput);
                }

                form.querySelector('select[name="siswa"]').value = siswa_id;
                form.querySelector('select[name="kelas"]').value = kelas_id;
                form.querySelector('select[name="tahun"]').value = tahun_id;
                form.querySelector('select[name="ket"]').value = ket;

                panelTitle.textContent = 'Edit Data Rombongan Belajar';
                submitBtn.textContent = 'Update Data';
            }

            function tutup() {
                const panel = document.getElementById('InputSiswaPanel');
                panel.classList.add('hidden');
            }
        </script>


        </script>





</x-layout.layout>
