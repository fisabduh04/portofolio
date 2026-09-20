<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Wali Kelas', 'href' => route('walikelas.index')],
    ]" />

    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-200 dark:border-gray-700">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="w-full md:w-auto">
                {{-- Data Count --}}
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 font-sans">Total Data: {{ $penugasans->total() }}</span>
            </div>
            <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 shrink-0">
                {{-- Primary Action: Tambah --}}
                <x-btn id="toggle-assignment" aria-controls="assignment-panel" aria-expanded="{{ $editing || ($errors->any() && ! $errors->hasAny(['file', 'id', 'ids'])) ? 'true' : 'false' }}" color="blue" size="sm">
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

        @if (session('success'))
            <div role="status" class="m-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="m-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/30 dark:text-red-300">
                <p class="font-semibold">Data belum disimpan:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($selectedTahun)
            <section id="assignment-panel" @class(['bg-gray-50 dark:bg-gray-800 p-6 border-b border-gray-200 dark:border-gray-700 transition-all', 'hidden' => ! $editing && (! $errors->any() || $errors->hasAny(['file', 'id', 'ids']))])>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm pointer-events-none shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <div>
                            <h4 id="assignment-title" class="font-bold text-lg text-gray-800 dark:text-white tracking-tight">{{ $editing ? 'Edit Penugasan' : 'Tambah Data (Bulk Input)' }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Atur wali kelas secara masal atau satu per satu.</p>
                        </div>
                    </div>
                    <button type="button" data-close-assignment class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form method="POST" action="{{ $editing ? route('walikelas.update', $editing) : route('walikelas.store') }}" data-store-url="{{ route('walikelas.store') }}" id="assignment-form">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div class="mb-6 bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            <div>
                                <label for="assignment-year" class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Tahun Ajaran <span class="text-xs font-normal text-gray-500 ml-1">(Berlaku untuk semua data di bawah)</span></label>
                                <select id="assignment-year" name="tahun_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    @foreach ($tahun as $item)
                                        <option value="{{ $item->id }}" @selected(old('tahun_id', $selectedTahun->id) == $item->id)>{{ $item->tahun }} &mdash; {{ $item->semester }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                        <div class="hidden md:grid grid-cols-12 gap-4 mb-2 px-4 font-semibold text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <div class="col-span-4">Wali Kelas</div><div class="col-span-2">Kelas</div><div class="col-span-2">Status</div><div class="col-span-3">Keterangan</div><div class="col-span-1 text-center">Aksi</div>
                        </div>
                        <div id="assignment-rows" class="space-y-3 mb-6">
                            @if ($editing)
                                @include('walikelas.row', ['index' => 0, 'row' => [
                                    'pegawai_id' => old('pegawai_id', $editing->pegawai_id),
                                    'kelas_id' => old('kelas_id', $editing->kelas_id),
                                    'is_active' => old('is_active', $editing->is_active),
                                    'keterangan' => old('keterangan', $editing->keterangan),
                                ], 'isEditing' => true])
                            @else
                            @foreach ($inputRows as $index => $row)
                                @include('walikelas.row', ['index' => $index, 'row' => $row, 'isEditing' => false])
                            @endforeach
                            @endif
                        </div>
                <button type="button" id="add-assignment-row" @if ($editing) style="display: none" @endif class="w-full py-3 mb-6 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 font-medium hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:border-gray-600 dark:hover:border-blue-400 dark:hover:text-blue-400 dark:hover:bg-gray-800 transition-all duration-200 flex items-center justify-center group">
                    <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 flex items-center justify-center mr-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Wali Kelas Lain
                </button>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-btn data-close-assignment color="light">Batal</x-btn>
                            <button type="submit" id="assignment-submit" class="text-white {{ $editing ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-600 hover:bg-green-700' }} focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none shadow-lg transition-all">{{ $editing ? 'Update Data' : 'Simpan Semua' }}</button>
                        </div>
                    </form>
                    <template id="assignment-row-template">
                        @include('walikelas.row', ['index' => '__INDEX__', 'row' => [], 'isEditing' => false])
                    </template>
            </section>
        @else
            <div class="p-4 text-sm text-gray-600 dark:text-gray-300">Belum ada periode. <a href="{{ route('tahun.index') }}" class="font-medium text-blue-600 underline dark:text-blue-400">Tambahkan tahun dan semester</a> terlebih dahulu.</div>
        @endif

        <form method="GET" action="{{ route('walikelas.index') }}" class="grid grid-cols-1 gap-4 p-4 md:grid-cols-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700" id="assignment-filters">
            @if (request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
            @endif
            <div class="min-w-0 md:col-span-3">
                <label for="search-wali" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" /></svg>
                </div>
                <input id="search-wali" name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama, NUPTK, kelas, atau keterangan..." maxlength="100" class="h-11 pl-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            <div class="min-w-0 md:col-span-3">
                <label for="filter-tahun" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun / Semester</label>
                <select id="filter-tahun" name="tahun_id" class="h-11 min-w-0 pr-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" @disabled(! $selectedTahun)>
                    @forelse ($tahun as $item)
                        <option value="{{ $item->id }}" @selected($selectedTahun?->id == $item->id)>{{ $item->tahun }} — {{ $item->semester }}</option>
                    @empty
                        <option value="">Belum ada periode</option>
                    @endforelse
                </select>
            </div>
            <div class="min-w-0 md:col-span-2">
                <label for="filter-kelas" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas</label>
                <select id="filter-kelas" name="kelas_id" class="h-11 min-w-0 pr-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}" @selected(request('kelas_id') == $item->id)>{{ $item->kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 md:col-span-2">
                <label for="filter-jurusan" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jurusan</label>
                <select id="filter-jurusan" name="jurusan_id" class="h-11 min-w-0 pr-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Jurusan</option>
                    @foreach ($jurusan as $item)
                        <option value="{{ $item->id }}" @selected(request('jurusan_id') == $item->id)>{{ $item->jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-0 md:col-span-2">
                <label for="per-page" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tampilkan</label>
                <select id="per-page" name="per_page" class="h-11 min-w-0 pr-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected(request('per_page', 10) == $size)>{{ $size }} Data</option>
                    @endforeach
                </select>
            </div>
            <noscript><x-btn type="submit" size="sm">Cari</x-btn></noscript>
        </form>

        @if ($selectedTahun)
            <div class="flex justify-end px-4 pb-4">
                <x-btn type="button" color="light" size="sm" id="assignment-actions-button" data-dropdown-toggle="assignment-actions">Actions <svg class="w-3 h-3 ms-2" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-width="2" d="m1 1 4 4 4-4" /></svg></x-btn>
                <div id="assignment-actions" class="hidden z-10 w-48 bg-white rounded-base divide-y divide-gray-100 shadow-xl dark:bg-gray-700 dark:divide-gray-600 border border-gray-200 dark:border-gray-600">
                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="assignment-actions-button">
                        <li><button type="button" data-modal-target="wali-import-modal" data-modal-toggle="wali-import-modal" class="block w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600">Import Excel</button></li>
                        <li><a id="assignment-export" href="{{ route('walikelas.export', array_merge(request()->only(['kelas_id', 'jurusan_id', 'status', 'search', 'sort', 'direction']), ['tahun_id' => $selectedTahun->id])) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">Export Excel</a></li>
                    </ul>
                    @if ($penugasans->isNotEmpty())
                        <div class="py-1 border-t border-gray-100 dark:border-gray-600">
                            <button type="button" id="delete-selected-assignments" disabled data-modal-target="popup-modal-wali-bulk" data-modal-toggle="popup-modal-wali-bulk" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed">Hapus yang dipilih</button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-xs text-heading uppercase bg-neutral-secondary-medium border-b border-default">
                    <tr>
                        <th scope="col" class="w-4 p-4"><input type="checkbox" id="check-all-assignments" aria-label="Pilih semua penugasan" class="w-4 h-4 text-blue-600 rounded border-gray-300"></th>
                        <th scope="col" class="px-4 py-3" aria-sort="{{ request('sort') === 'id' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'id', 'direction' => request('sort') === 'id' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">No <span aria-hidden="true">{{ request('sort') === 'id' && request('direction', 'asc') === 'desc' ? '↓' : '↑' }}</span></a>
                        </th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                        <th scope="col" class="px-4 py-3"><a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'nuptk', 'direction' => request('sort') === 'nuptk' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">NUPTK</a></th>
                        <th scope="col" class="px-4 py-3" aria-sort="{{ request('sort') === 'kelas' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'kelas', 'direction' => request('sort') === 'kelas' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">Kelas <span aria-hidden="true">{{ request('sort') === 'kelas' && request('direction', 'asc') === 'desc' ? '↓' : '↑' }}</span></a>
                        </th>
                        <th scope="col" class="px-4 py-3" aria-sort="{{ request('sort') === 'nama' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'nama', 'direction' => request('sort') === 'nama' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">Wali Kelas <span aria-hidden="true">{{ request('sort') === 'nama' && request('direction', 'asc') === 'desc' ? '↓' : '↑' }}</span></a>
                        </th>
                        <th scope="col" class="px-4 py-3" aria-sort="{{ request('sort') === 'status' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'status', 'direction' => request('sort') === 'status' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">Status <span aria-hidden="true">{{ request('sort') === 'status' && request('direction', 'asc') === 'desc' ? '↓' : '↑' }}</span></a>
                        </th>
                        <th scope="col" class="px-4 py-3" aria-sort="{{ request('sort') === 'keterangan' ? (request('direction', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none' }}">
                            <a class="flex items-center gap-1 hover:text-blue-600" href="{{ route('walikelas.index', array_merge(request()->except(['page', 'edit']), ['tahun_id' => $selectedTahun?->id, 'sort' => 'keterangan', 'direction' => request('sort') === 'keterangan' && request('direction', 'asc') === 'asc' ? 'desc' : 'asc'])) }}">Keterangan <span aria-hidden="true">{{ request('sort') === 'keterangan' && request('direction', 'asc') === 'desc' ? '↓' : '↑' }}</span></a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penugasans as $penugasan)
                        <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                            <td class="w-4 p-4"><input type="checkbox" name="id[]" form="assignment-bulk-delete" value="{{ $penugasan->id }}" aria-label="Pilih wali kelas {{ $penugasan->pegawai->name }}" class="w-4 h-4 text-blue-600 rounded border-gray-300"></td>
                            <td class="px-4 py-4">{{ $penugasans->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <x-btn type="button" data-edit-assignment
                                        data-assignment-id="{{ $penugasan->id }}" data-update-url="{{ route('walikelas.update', $penugasan) }}"
                                        data-identity="{{ $penugasan->kelas->kelas }} — {{ $penugasan->pegawai->name }}"
                                        data-pegawai-id="{{ $penugasan->pegawai_id }}" data-kelas-id="{{ $penugasan->kelas_id }}" data-tahun-id="{{ $penugasan->tahun_id }}"
                                        data-status="{{ (int) $penugasan->is_active }}" data-notes="{{ $penugasan->keterangan }}"
                                        icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" aria-label="Edit wali kelas {{ $penugasan->pegawai->name }}" />
                                    <x-btn type="button" data-modal-target="popup-modal-wali-{{ $penugasan->id }}" data-modal-toggle="popup-modal-wali-{{ $penugasan->id }}" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" title="Hapus" aria-label="Hapus wali kelas {{ $penugasan->pegawai->name }}" />
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $penugasan->pegawai->nuptk ?: '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $penugasan->kelas->kelas }}</td>
                            <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ $penugasan->pegawai->name }}</td>
                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('walikelas.update', $penugasan) }}" data-status-form data-assignment-id="{{ $penugasan->id }}" data-kelas-id="{{ $penugasan->kelas_id }}">
                                    @csrf
                                    @method('PUT')
                                    <select name="is_active" aria-label="Status wali kelas {{ $penugasan->pegawai->name }}" class="text-xs rounded-lg block w-full p-2 border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:bg-gray-700 dark:border-gray-600 {{ $penugasan->is_active ? 'bg-green-50 text-green-700 border-green-300 focus:ring-green-500' : 'bg-red-50 text-red-700 border-red-300 focus:ring-red-500' }}">
                                        <option value="1" @selected($penugasan->is_active)>Aktif</option>
                                        <option value="0" @selected(! $penugasan->is_active)>Nonaktif</option>
                                    </select>
                                    <noscript><x-btn type="submit" size="sm">Simpan Status</x-btn></noscript>
                                    <span role="status" class="hidden text-xs text-gray-500" data-status-error></span>
                                </form>
                            </td>
                            <td class="max-w-sm whitespace-pre-line break-words px-4 py-4">{{ $penugasan->keterangan ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada penugasan yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $penugasans->links() }}</div>
    </div>

    @foreach ($penugasans as $penugasan)
        <x-modal.hapus :id="'wali-'.$penugasan->id" :action="route('walikelas.destroy', $penugasan)" />
    @endforeach
    @if ($penugasans->isNotEmpty())
        <form id="assignment-bulk-delete" method="POST" action="{{ route('walikelas.bulkDelete') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="tahun_id" value="{{ $selectedTahun->id }}">
        </form>
        <x-modal.hapus id="wali-bulk" formTarget="assignment-bulk-delete" message="Apakah Anda yakin ingin menghapus data yang dipilih?" />
    @endif

    @if ($selectedTahun)
        <div id="wali-import-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                        <h3 class="text-xl font-medium text-gray-900 dark:text-white">
                            Import Excel Wali Kelas
                        </h3>
                        <button type="button"
                            class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="wali-import-modal">
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
                        <form action="{{ route('walikelas.import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="tahun_id" value="{{ $selectedTahun->id }}">
                            <p class="text-sm text-gray-600 dark:text-gray-300">Periode: {{ $selectedTahun->tahun }} &mdash; {{ $selectedTahun->semester }}.</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Gunakan hasil Export Excel sebagai format. Kolom: tahun, semester, kelas, pegawai_id, nuptk, nama, status, keterangan. Isi pegawai_id atau NUPTK, serta status aktif/nonaktif. Maksimal 5 MB.</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Penugasan yang sudah ada akan diperbarui. Semua baris harus sesuai periode terpilih.</p>
                            @error('file')<p role="alert" class="text-sm text-red-600">{{ $message }}</p>@enderror
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="wali-import-file">Import
                                File Excel Anda</label>
                            <input accept=".xlsx, .csv, .xls" name="file" required
                                class="block w-full mb-5 text-xs text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                id="wali-import-file" type="file">

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
                        <button data-modal-hide="wali-import-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


    @endif

    <script>
        (() => {
            @if ($errors->has('file'))
                window.addEventListener('load', () => document.querySelector('[data-modal-toggle="wali-import-modal"]')?.click());
            @endif
            const panel = document.getElementById('assignment-panel');
            const toggle = document.getElementById('toggle-assignment');
            const form = document.getElementById('assignment-form');
            const title = document.getElementById('assignment-title');
            const rows = document.getElementById('assignment-rows');
            const addButton = document.getElementById('add-assignment-row');
            const submitButton = document.getElementById('assignment-submit');
            const year = document.getElementById('assignment-year');
            let nextIndex = rows ? Math.max(-1, ...Array.from(rows.querySelectorAll('[name]')).map(input => Number(input.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1 : 0;
            const addRow = () => {
                if (!rows || rows.children.length >= 100) return;
                const template = document.getElementById('assignment-row-template');
                rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));
                rows.lastElementChild.dispatchEvent(new CustomEvent('searchable-select:init', { bubbles: true }));
            };
            const destroyRowSelects = root => {
                root.querySelectorAll('[data-select-native]').forEach(select => select.searchableSelect?.destroy());
            };
            const showPanel = () => {
                panel.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };
            const setMode = editing => {
                addButton.style.display = editing ? 'none' : 'flex';
                submitButton.textContent = editing ? 'Update Data' : 'Simpan Semua';
                submitButton.classList.toggle('bg-yellow-500', editing);
                submitButton.classList.toggle('hover:bg-yellow-600', editing);
                submitButton.classList.toggle('bg-green-600', !editing);
                submitButton.classList.toggle('hover:bg-green-700', !editing);
                title.textContent = editing ? 'Edit Penugasan' : 'Tambah Data (Bulk Input)';
            };
            toggle?.addEventListener('click', () => {
                if (!panel) return;
                const url = new URL(window.location.href);
                url.searchParams.delete('edit');
                window.history.replaceState(null, '', url);
                form.reset();
                form.action = form.dataset.storeUrl;
                form.querySelector('[name="_method"]')?.remove();
                year.value = document.getElementById('filter-tahun').value;
                destroyRowSelects(rows);
                rows.replaceChildren();
                addRow();
                setMode(false);
                showPanel();
            });
            document.querySelectorAll('[data-close-assignment]').forEach(button => {
                button.addEventListener('click', () => {
                    rows.querySelectorAll('[data-select-native]').forEach(select => select.searchableSelect?.close());
                    panel.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    const url = new URL(window.location.href);
                    url.searchParams.delete('edit');
                    window.history.replaceState(null, '', url);
                });
            });
            document.querySelectorAll('[data-edit-assignment]').forEach(button => {
                button.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('edit', button.dataset.assignmentId);
                    url.searchParams.set('tahun_id', button.dataset.tahunId);
                    window.history.replaceState(null, '', url);
                    form.action = button.dataset.updateUrl;
                    let method = form.querySelector('[name="_method"]');
                    if (!method) {
                        method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        form.appendChild(method);
                    }
                    method.value = 'PUT';
                    year.value = button.dataset.tahunId;
                    destroyRowSelects(rows);
                    rows.replaceChildren();
                    addRow();
                    const values = { pegawai_id: button.dataset.pegawaiId, kelas_id: button.dataset.kelasId, is_active: button.dataset.status, keterangan: button.dataset.notes };
                    rows.querySelectorAll('[name]').forEach(input => {
                        const field = input.name.match(/\[([^\]]+)\]$/)[1];
                        input.name = field;
                        input.value = values[field] ?? '';
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    rows.querySelector('[data-remove-row]').style.display = 'none';
                    setMode(true);
                    showPanel();
                });
            });
            addButton?.addEventListener('click', addRow);
            rows?.addEventListener('click', event => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                if (rows.children.length > 1) {
                    const row = button.closest('[data-assignment-row]');
                    destroyRowSelects(row);
                    row.remove();
                } else {
                    alert('Minimal satu baris data harus ada.');
                }
            });
            const filters = document.getElementById('assignment-filters');
            filters.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => filters.requestSubmit());
            });
            let searchTimeout;
            document.getElementById('search-wali').addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => filters.requestSubmit(), 1000);
            });
            const updateStatusColor = select => {
                const active = select.value === '1';
                ['bg-green-50', 'text-green-700', 'border-green-300', 'focus:ring-green-500'].forEach(name => select.classList.toggle(name, active));
                ['bg-red-50', 'text-red-700', 'border-red-300', 'focus:ring-red-500'].forEach(name => select.classList.toggle(name, !active));
            };
            document.querySelectorAll('[data-status-form]').forEach(statusForm => {
                const select = statusForm.querySelector('select');
                let previousStatus = select.value;
                let messageTimeout;
                select.addEventListener('change', async () => {
                    const message = statusForm.querySelector('[data-status-error]');
                    const data = new FormData(statusForm);
                    const relatedForms = Array.from(document.querySelectorAll('[data-status-form]')).filter(item => item.dataset.kelasId === statusForm.dataset.kelasId);
                    relatedForms.forEach(item => item.querySelector('select').disabled = true);
                    clearTimeout(messageTimeout);
                    message.classList.remove('text-green-600', 'text-red-600');
                    message.classList.add('text-gray-500');
                    message.textContent = 'Menyimpan...';
                    message.classList.remove('hidden');
                    try {
                        const response = await fetch(statusForm.action, { method: 'POST', body: data, headers: { Accept: 'application/json' } });
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message);
                        result.statuses.forEach(status => {
                            const rowForm = document.querySelector(`[data-status-form][data-assignment-id="${status.id}"]`);
                            if (rowForm) {
                                const rowSelect = rowForm.querySelector('select');
                                rowSelect.value = status.is_active ? '1' : '0';
                                updateStatusColor(rowSelect);
                                rowSelect.dispatchEvent(new Event('status-saved'));
                            }
                            const editButton = document.querySelector(`[data-edit-assignment][data-assignment-id="${status.id}"]`);
                            if (editButton) editButton.dataset.status = status.is_active ? '1' : '0';
                        });
                        message.classList.remove('text-gray-500');
                        message.classList.add('text-green-600');
                        message.textContent = 'Tersimpan!';
                        messageTimeout = setTimeout(() => message.classList.add('hidden'), 2000);
                    } catch (failure) {
                        select.value = previousStatus;
                        updateStatusColor(select);
                        message.classList.remove('text-gray-500');
                        message.classList.add('text-red-600');
                        message.textContent = 'Gagal menyimpan status. Silakan coba lagi.';
                    } finally {
                        relatedForms.forEach(item => item.querySelector('select').disabled = false);
                    }
                });
                select.addEventListener('status-saved', () => previousStatus = select.value);
            });
            const exportLink = document.getElementById('assignment-export');
            exportLink?.addEventListener('click', event => {
                event.preventDefault();
                const url = new URL(exportLink.href);
                document.querySelectorAll('[form="assignment-bulk-delete"]:checked').forEach(input => url.searchParams.append('ids[]', input.value));
                window.location.href = url.toString();
            });
            const checkAll = document.getElementById('check-all-assignments');
            const checkboxes = Array.from(document.querySelectorAll('[form="assignment-bulk-delete"]'));
            const deleteSelected = document.getElementById('delete-selected-assignments');
            const updateSelection = () => {
                if (deleteSelected) deleteSelected.disabled = !checkboxes.some(item => item.checked);
            };
            checkAll?.addEventListener('change', () => {
                checkboxes.forEach(input => input.checked = checkAll.checked);
                updateSelection();
            });
            checkboxes.forEach(input => input.addEventListener('change', () => {
                updateSelection();
                checkAll.checked = checkboxes.every(item => item.checked);
                checkAll.indeterminate = checkboxes.some(item => item.checked) && !checkAll.checked;
            }));
        })();
    </script>
</x-layout.layout>
