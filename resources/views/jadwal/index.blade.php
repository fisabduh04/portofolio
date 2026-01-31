<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Akademik', 'href' => ''],
        ['name' => 'Jadwal Mata Pelajaran', 'href' => 'jadwal'],
    ]" />



    {{-- Alert Bentrok Jadwal --}}
    @if (isset($bentrokJadwalList) && $bentrokJadwalList->isNotEmpty())
        <div class="mb-6 p-4 border border-red-200 bg-red-50 rounded-base dark:bg-red-900/20 dark:border-red-800" role="alert">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-800 dark:text-red-400">Jadwal Bentrok Terdeteksi!</h3>
                    <p class="text-sm text-red-700 dark:text-red-300">Terdapat {{ $totalBentrok }} data bentrok yang perlu diperbaiki.</p>
                </div>
            </div>
            <ul class="ml-14 list-disc list-outside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach ($bentrokJadwalList->unique('id') as $j)
                    <li>
                        Guru <strong>{{ $j->pegawai->name ?? 'N/A' }}</strong> bentrok pada hari <strong>{{ $j->hari }}</strong> 
                        ({{ $j->mulai }} - {{ $j->akhir }}) di Kelas <strong>{{ $j->kelas->kelas ?? 'N/A' }}</strong>
                    </li>
                @endforeach
                @if($totalBentrok > 10)
                    <li class="font-bold italic">... Dan {{ $totalBentrok - 10 }} data lainnya.</li>
                @endif
            </ul>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-200 dark:border-gray-700">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-full md:w-auto">
                <h2 class="text-xl font-bold font-sans text-gray-900 dark:text-white">
                    Data Jadwal Pelajaran
                    <span class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 font-sans">(Total: {{ $jadwals->total() }})</span>
                </h2>
            </div>
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                <x-btn onclick="tambah()" icon="plus" text="Tambah Jadwal" color="blue" />
                <x-btn onclick="location.reload()" icon="arrow-path" text="Refresh" color="light" />
            </div>
        </div>

        {{-- Panel Setup Data Baru (Slide Down) --}}
        <div id="tahunbulk" class="hidden bg-gray-50 p-4 border-b border-gray-200 dark:bg-gray-700 dark:border-gray-600 transition-all">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">1</div>
                <h4 class="font-bold text-sm text-gray-800 dark:text-white">Setup Data Baru</h4>
            </div>
            
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/3">
                    <label for="tahun_bulk" class="block mb-2 text-xs font-semibold text-gray-900 dark:text-white">Pilih Tahun Ajaran Default</label>
                    <select id="tahun_bulk" name="tahun_id" form="bulkFormJadwal"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach ($tahun as $t)
                            <option value="{{ $t->id }}" {{ request('filter_tahun') == $t->id ? 'selected' : '' }}>
                                {{ $t->tahun }} - {{ $t->semester }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                     <x-btn type="submit" form="bulkFormJadwal" id="btnSimpanBarisBaru" text="Simpan Semua Baris" icon="check" color="green" />
                </div>
            </div>
        </div>

        {{-- Secondary Toolbar --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 bg-white dark:bg-gray-900">
            
            {{-- Left: Actions --}}
            <div class="w-full md:w-auto">
                {{-- Actions Dropdown Button --}}
                <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                    <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                    Actions
                </button>
                <div id="actionsDropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actionsDropdownButton">
                        <li><a href="#" onclick="exportJadwal(); return false;" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Export Excel</a></li>
                        <li><a href="#" onclick="document.getElementById('importModal').classList.remove('hidden'); return false;" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Import CSV</a></li>
                    </ul>
                    <div class="py-1">
                         <button type="button" id="btnBulkDelete" data-modal-target="modal-bulk-delete" data-modal-toggle="modal-bulk-delete" disabled class="block w-full text-left py-2 px-4 text-sm text-red-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-200 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                            Delete Selected
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Filters --}}
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                 <form action="/jadwal" method="GET" id="searchForm" class="flex flex-col md:flex-row gap-3 w-full">
                    
                    {{-- Filter Tahun --}}
                    <div class="w-full md:w-40">
                         <select id="tahun" name="filter_tahun" onchange="this.form.submit()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t->id }}" {{ request('filter_tahun') == $t->id ? 'selected' : '' }}>{{ $t->tahun }} - {{ $t->semester }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kelas --}}
                    <div class="w-full md:w-32">
                        <select id="kelas" name="filter_kelas" onchange="this.form.submit()"
                             class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>{{ $k->kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search mapel/guru..." required="">
                    </div>
                </form>
            </div>
        </div>

        {{-- Hidden Logic Forms --}}
        <form id="bulkDeleteForm" action="{{ route('jadwal.bulkDelete') }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        <form id="bulkFormJadwal" action="{{ route('jadwal.updateAll') }}" method="POST" class="hidden">@csrf</form>
        <form id="exportForm" action="{{ route('jadwal.export') }}" method="GET" class="hidden">
            <input type="hidden" name="type" id="exportType" value="all">
            <input type="hidden" name="ids" id="exportIds" value="">
            <input type="hidden" name="tahun_id" value="{{ request('filter_tahun') }}">
        </form>





        {{-- Data Caching --}}
        @php
            $optKelas = ''; foreach($kelas as $k) $optKelas .= "<option value='{$k->id}'>{$k->kelas}</option>";
            $optHari = ''; foreach($hari as $h) $optHari .= "<option value='{$h}'>{$h}</option>";
            $optMapel = ''; foreach($mapel as $m) $optMapel .= "<option value='{$m->id}'>{$m->mapel}</option>";
            $optPegawai = ''; foreach($pegawai as $p) $optPegawai .= "<option value='{$p->id}'>{$p->name}</option>";
        @endphp

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table id="data" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-700">
                    <tr>
                         <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkAll" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkAll" class="sr-only">checkbox</label>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">NO</th>
                        <th scope="col" class="px-4 py-3 text-center">AKSI</th>
                        <th scope="col" class="px-4 py-3">KELAS</th>
                        <th scope="col" class="px-4 py-3">HARI</th>
                        <th scope="col" class="px-4 py-3">MAPEL</th>
                        <th scope="col" class="px-4 py-3">GURU</th>
                        <th scope="col" class="px-4 py-3 text-center">JAM</th>
                        <th scope="col" class="px-4 py-3 text-center">WAKTU</th>
                        <th scope="col" class="px-4 py-3 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody id="jadwalTable">
                    
                    {{-- Template Row Baru --}}
                    <template id="tplNewJadwalRow">
                        <tr class="bg-blue-50 hover:bg-blue-100 new-row border-b dark:bg-blue-900/20 dark:border-gray-700">
                            <td class="p-4 w-4"></td>
                            <td class="px-4 py-3 text-blue-600 font-bold text-center">BARU</td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeNewRow(this)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                            <td class="px-2 py-3">
                                 <input type="hidden" name="id[]" value="" form="bulkFormJadwal">
                                 <select name="kelas_id[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5"><option value="">Pilih...</option>{!! $optKelas !!}</select>
                            </td>
                            <td class="px-2 py-3">
                                <select name="hari[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5"><option value="">Pilih...</option>{!! $optHari !!}</select>
                            </td>
                            <td class="px-2 py-3">
                                <select name="mapel_id[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5"><option value="">Pilih...</option>{!! $optMapel !!}</select>
                            </td>
                             <td class="px-2 py-3">
                                <select name="pegawai_id[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5"><option value="">Pilih...</option>{!! $optPegawai !!}</select>
                            </td>
                             <td class="px-2 py-3 w-16">
                                <input type="number" name="jam[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5" placeholder="Ke">
                            </td>
                             <td class="px-2 py-3 w-32">
                                <div class="flex gap-1">
                                    <input type="time" name="mulai[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5">
                                    <input type="time" name="akhir[]" form="bulkFormJadwal" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5">
                                </div>
                            </td>
                             <td class="px-2 py-3">
                                <input type="text" name="ket" form="bulkFormJadwal" placeholder="Status..." class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5">
                            </td>
                        </tr>
                    </template>

                    @forelse ($jadwals as $j)
                        @php
                            $isConflict = isset($jadwalBentrokIds) && in_array($j->id, $jadwalBentrokIds);
                            $rowClass = $isConflict ? 'bg-red-50 dark:bg-red-900/30 border-b dark:border-gray-700' : 'border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';
                        @endphp
                        
                        <tr class="{{ $rowClass }}" id="row-{{ $j->id }}">
                             <td class="p-4 w-4">
                                <div class="flex items-center">
                                    <input id="checkbox-{{ $j->id }}" type="checkbox" value="{{ $j->id }}" name="ids[]" form="bulkDeleteForm" class="item-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-{{ $j->id }}" class="sr-only">checkbox</label>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit Button --}}
                                    <x-btn onclick="editRow({{ $j->id }})" icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" />
                                    {{-- Delete Button --}}
                                    <x-btn data-modal-target="popup-modal-{{ $j->id }}" data-modal-toggle="popup-modal-{{ $j->id }}" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" />
                                </div>
                            </td>
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $j->kelas->kelas ?? '-' }}
                            </th>
                            <td class="px-4 py-3">{{ $j->hari }}</td>
                            <td class="px-4 py-3">{{ $j->mapel->mapel ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $j->pegawai->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-base dark:bg-blue-200 dark:text-blue-800">{{ $j->jam }}</span>
                            </td>
                             <td class="px-4 py-3 text-center text-xs">
                                {{ \Carbon\Carbon::parse($j->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->akhir)->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(strtolower($j->ket) == 'aktif')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-base dark:bg-green-900 dark:text-green-300">Aktif</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded-base dark:bg-gray-700 dark:text-gray-300">{{ $j->ket ?: '-' }}</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Inline Edit Row (Hidden by default) --}}
                         <tr id="edit-form-{{ $j->id }}" class="hidden bg-blue-50 border-b dark:bg-blue-900/10 dark:border-gray-700">
                            <td class="p-4 w-4">
                                <form action="jadwal/{{ $j->id }}" method="POST" id="form-edit-{{ $j->id }}">
    @csrf @method('PUT')
    <input type="hidden" name="tahun_id" value="{{ $j->tahun_id }}">
</form>
                            </td>
                            <td colspan="2" class="px-4 py-3 text-center">
                                <div class="flex gap-2 justify-center">
                                     <x-btn type="submit" form="form-edit-{{ $j->id }}" icon="check" color="green" size="sm" class="!p-2" />
                                     <x-btn onclick="cancelEditRow({{ $j->id }})" icon="x" color="light" size="sm" class="!p-2" />
                                </div>
                            </td>
                             <td class="px-2 py-3">
                                 <select name="kelas_id" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                    @foreach ($kelas as $k) <option value="{{ $k->id }}" @selected($j->kelas_id == $k->id)>{{ $k->kelas }}</option> @endforeach
                                </select>
                             </td>
                             <td class="px-2 py-3">
                                <select name="hari" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                    @foreach ($hari as $h) <option value="{{ $h }}" @selected($j->hari == $h)>{{ $h }}</option> @endforeach
                                </select>
                             </td>
                             <td class="px-2 py-3">
                                 <select name="mapel_id" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                     @foreach ($mapel as $m) <option value="{{ $m->id }}" @selected($j->mapel_id == $m->id)>{{ $m->mapel }}</option> @endforeach
                                </select>
                             </td>
                              <td class="px-2 py-3">
                                 <select name="pegawai_id" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                     @foreach ($pegawai as $p) <option value="{{ $p->id }}" @selected($j->pegawai_id == $p->id)>{{ $p->name }}</option> @endforeach
                                </select>
                             </td>
                             <td class="px-2 py-3">
                                <input type="number" name="jam" value="{{ $j->jam }}" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                            </td>
                             <td class="px-2 py-3">
                                <div class="flex gap-1">
                                     <input type="time" name="mulai" value="{{ \Carbon\Carbon::parse($j->mulai)->format('H:i') }}" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                     <input type="time" name="akhir" value="{{ \Carbon\Carbon::parse($j->akhir)->format('H:i') }}" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                                </div>
                            </td>
                             <td class="px-2 py-3">
                                <input type="text" name="ket" value="{{ $j->ket }}" form="form-edit-{{ $j->id }}" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-base block w-full p-1">
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data jadwal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $jadwals->appends(request()->query())->links() }}
        </div>
    </div>

    {{-- Delete Modals (Per Row) --}}
    @foreach($jadwals as $j)
        <x-modal.hapus id="{{ $j->id }}" action="{{ route('jadwal.destroy', $j->id) }}" />
    @endforeach

    {{-- Bulk Delete Confirmation Modal --}}
    <div id="modal-bulk-delete" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-bulk-delete">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus data yang dipilih?</h3>
                    <div class="flex justify-center gap-3">
                         <button type="button" onclick="document.getElementById('bulkDeleteForm').submit()" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Ya, hapus
                        </button>
                        <button type="button" data-modal-hide="modal-bulk-delete" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Tidak, batalkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" tabindex="-1" class="fixed inset-0 z-50 hidden overflow-y-auto overflow-x-hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-base shadow-xl dark:bg-gray-700">
             <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Import Data Jadwal</h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-base text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
             <div class="p-6">
                <form action="{{ route('jadwal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                     <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="file_input">Upload file Excel/CSV</label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-base cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file" name="file" accept=".csv, .xls, .xlsx" required>
                    </div>
                    <x-btn type="submit" text="Start Import" color="blue" class="w-full" />
                </form>
            </div>
        </div>
    </div>

<script>
    // Logic Preserved - Functionality verification
    
    // 1. Export Logic
    function exportJadwal() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const form = document.getElementById('exportForm');
        
        if (checkedBoxes.length > 0) {
            document.getElementById('exportType').value = 'selected';
            document.getElementById('exportIds').value = Array.from(checkedBoxes).map(cb => cb.value).join(',');
            if(confirm('Export ' + checkedBoxes.length + ' data terpilih?')) form.submit();
        } else {
             document.getElementById('exportType').value = 'all';
             document.getElementById('exportIds').value = '';
            if(confirm('Export semua data sesuai filter?')) form.submit();
        }
    }

    // 2. Edit Row Logic
    function editRow(id) {
         // Close other edits
        document.querySelectorAll('tr[id^="edit-form-"]').forEach(row => {
            row.classList.add('hidden');
            let otherId = row.id.replace('edit-form-', '');
            let display = document.getElementById('row-' + otherId);
            if(display) display.classList.remove('hidden');
        });

        // Open this edit
        document.getElementById('row-' + id).classList.add('hidden');
        document.getElementById('edit-form-' + id).classList.remove('hidden');
    }

    function cancelEditRow(id) {
        document.getElementById('row-' + id).classList.remove('hidden');
        document.getElementById('edit-form-' + id).classList.add('hidden');
    }

    // 3. Checkbox Logic
    const checkAll = document.getElementById('checkAll');
    const bulkBtn = document.getElementById('btnBulkDelete');

    function updateBulkState() {
        const count = document.querySelectorAll('.item-checkbox:checked').length;
        if(bulkBtn) bulkBtn.disabled = count === 0;
    }

    checkAll.addEventListener('change', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkState();
    });

    document.addEventListener('change', (e) => {
        if(e.target.matches('.item-checkbox')) {
            updateBulkState();
            checkAll.checked = document.querySelectorAll('.item-checkbox:checked').length === document.querySelectorAll('.item-checkbox').length;
        }
    });

    // 4. Tambah Row Logic
    function tambah() {
        document.getElementById('tahunbulk').classList.remove('hidden');
        const tpl = document.getElementById('tplNewJadwalRow');
        const tbody = document.getElementById('jadwalTable');
        const clone = tpl.content.cloneNode(true);
        tbody.insertBefore(clone, tbody.firstChild);
    }

    function removeNewRow(btn) {
        btn.closest('tr').remove();
        if(!document.querySelector('tr.new-row')) document.getElementById('tahunbulk').classList.add('hidden');
    }

    // Auto Search
    let timer;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => document.getElementById('searchForm').submit(), 800);
    });

</script>
</x-layout.layout>
