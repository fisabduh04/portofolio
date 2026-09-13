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
                <x-btn id="toggle-assignment" aria-controls="assignment-panel" aria-expanded="{{ $editing || $errors->any() ? 'true' : 'false' }}" color="blue" size="sm">
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
            <section id="assignment-panel" @class(['bg-gray-50 dark:bg-gray-800 p-6 border-b border-gray-200 dark:border-gray-700 transition-all', 'hidden' => ! $editing && ! $errors->any()])>
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

                <div class="mb-6 bg-white dark:bg-gray-900 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tahun Ajaran <span class="text-xs font-normal text-gray-500">(Berlaku untuk semua data di bawah)</span></p>
                    <p class="mt-2 text-sm text-gray-900 dark:text-white">{{ $selectedTahun->tahun }} — {{ $selectedTahun->semester }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Mengaktifkan wali kelas akan menonaktifkan wali lain pada kelas dan periode yang sama.</p>
                </div>
                    <form method="POST" action="{{ $editing ? route('walikelas.update', $editing) : '' }}" id="assignment-edit-form" @class(['space-y-4', 'hidden' => ! $editing])>
                        @csrf
                        @method('PUT')
                        <p id="assignment-identity" class="text-sm font-medium text-gray-900 dark:text-white">{{ $editing?->kelas?->kelas }} — {{ $editing?->pegawai?->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Untuk mengganti pegawai, tambahkan penugasan baru agar data sebelumnya tetap tersimpan.</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="edit-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                <select id="edit-status" name="is_active" required class="block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="1" @selected(old('is_active', $editing?->is_active) == 1)>Aktif</option>
                                    <option value="0" @selected(old('is_active', $editing?->is_active) == 0)>Nonaktif</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit-keterangan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                                <textarea id="edit-keterangan" name="keterangan" maxlength="2000" rows="3" class="block w-full rounded-lg border border-gray-300 bg-white p-3 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('keterangan', $editing?->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-btn data-close-assignment color="light">Batal</x-btn>
                            <x-btn type="submit">Simpan Perubahan</x-btn>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('walikelas.store') }}" @class(['space-y-4', 'hidden' => (bool) $editing]) id="assignment-form">
                        @csrf
                        <input type="hidden" name="tahun_id" value="{{ $selectedTahun->id }}">
                        <div class="hidden md:grid grid-cols-12 gap-4 mb-2 px-4 font-semibold text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <div class="col-span-4">Wali Kelas</div><div class="col-span-2">Kelas</div><div class="col-span-2">Status</div><div class="col-span-3">Keterangan</div><div class="col-span-1 text-center">Aksi</div>
                        </div>
                        <div id="assignment-rows" class="space-y-3 mb-6">
                            @foreach ($inputRows as $index => $row)
                                @include('walikelas.row', ['index' => $index, 'row' => $row])
                            @endforeach
                        </div>
                <button type="button" id="add-assignment-row" class="w-full py-3 mb-6 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 font-medium hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:border-gray-600 dark:hover:border-blue-400 dark:hover:text-blue-400 dark:hover:bg-gray-800 transition-all duration-200 flex items-center justify-center group">
                    <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 group-hover:bg-blue-100 group-hover:text-blue-600 flex items-center justify-center mr-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Tambah Wali Kelas Lain
                </button>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-btn data-close-assignment color="light">Batal</x-btn>
                            <x-btn type="submit">Simpan Semua</x-btn>
                        </div>
                    </form>
                    <template id="assignment-row-template">
                        @include('walikelas.row', ['index' => '__INDEX__', 'row' => []])
                    </template>
            </section>
        @else
            <div class="p-4 text-sm text-gray-600 dark:text-gray-300">Belum ada periode. <a href="{{ route('tahun.index') }}" class="font-medium text-blue-600 underline dark:text-blue-400">Tambahkan tahun dan semester</a> terlebih dahulu.</div>
        @endif

        <form method="GET" action="{{ route('walikelas.index') }}" class="grid grid-cols-1 gap-4 p-4 md:grid-cols-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700" id="assignment-filters">
            <div class="min-w-0 md:col-span-3">
                <label for="search-wali" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" /></svg>
                </div>
                <input id="search-wali" name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama wali atau kelas..." maxlength="100" class="h-11 pl-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                <label for="filter-status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="filter-status" name="status" class="h-11 min-w-0 pr-10 block w-full rounded-base border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
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

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-xs text-heading uppercase bg-neutral-secondary-medium border-b border-default">
                    <tr>
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3 text-center">Action</th>
                        <th scope="col" class="px-4 py-3">Kelas</th>
                        <th scope="col" class="px-4 py-3">Wali Kelas</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penugasans as $penugasan)
                        <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                            <td class="px-4 py-4">{{ $penugasans->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <x-btn type="button" data-edit-assignment
                                        data-assignment-id="{{ $penugasan->id }}" data-update-url="{{ route('walikelas.update', $penugasan) }}"
                                        data-identity="{{ $penugasan->kelas->kelas }} — {{ $penugasan->pegawai->name }}"
                                        data-status="{{ (int) $penugasan->is_active }}" data-notes="{{ $penugasan->keterangan }}"
                                        icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" aria-label="Edit wali kelas {{ $penugasan->pegawai->name }}" />
                                    <form method="POST" action="{{ route('walikelas.destroy', $penugasan) }}" data-delete-assignment>
                                        @csrf
                                        @method('DELETE')
                                        <x-btn type="submit" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" title="Hapus" aria-label="Hapus wali kelas {{ $penugasan->pegawai->name }}" />
                                    </form>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $penugasan->kelas->kelas }}</td>
                            <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ $penugasan->pegawai->name }}</td>
                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('walikelas.update', $penugasan) }}" data-status-form>
                                    @csrf
                                    @method('PUT')
                                    <select name="is_active" aria-label="Status wali kelas {{ $penugasan->pegawai->name }}" class="text-xs rounded-lg block w-full p-2 border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:bg-gray-700 dark:border-gray-600 {{ $penugasan->is_active ? 'bg-green-50 text-green-700 border-green-300 focus:ring-green-500' : 'bg-red-50 text-red-700 border-red-300 focus:ring-red-500' }}">
                                        <option value="1" @selected($penugasan->is_active)>Aktif</option>
                                        <option value="0" @selected(! $penugasan->is_active)>Nonaktif</option>
                                    </select>
                                    <noscript><x-btn type="submit" size="sm">Simpan Status</x-btn></noscript>
                                    <span role="status" class="hidden text-xs text-red-600" data-status-error></span>
                                </form>
                            </td>
                            <td class="max-w-sm whitespace-pre-line break-words px-4 py-4">{{ $penugasan->keterangan ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada penugasan yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $penugasans->links() }}</div>
    </div>

    <script>
        (() => {
            const panel = document.getElementById('assignment-panel');
            const toggle = document.getElementById('toggle-assignment');
            const createForm = document.getElementById('assignment-form');
            const editForm = document.getElementById('assignment-edit-form');
            const title = document.getElementById('assignment-title');
            const showPanel = () => {
                panel.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };
            toggle?.addEventListener('click', () => {
                if (!panel) return;
                const url = new URL(window.location.href);
                url.searchParams.delete('edit');
                window.history.replaceState(null, '', url);
                createForm.reset();
                rows.replaceChildren();
                addRow();
                createForm.classList.remove('hidden');
                editForm.classList.add('hidden');
                title.textContent = 'Tambah Data (Bulk Input)';
                showPanel();
            });
            document.querySelectorAll('[data-close-assignment]').forEach(button => {
                button.addEventListener('click', () => {
                    panel.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });
            document.querySelectorAll('[data-edit-assignment]').forEach(button => {
                button.addEventListener('click', () => {
                    const url = new URL(window.location.href);
                    url.searchParams.set('edit', button.dataset.assignmentId);
                    url.searchParams.set('tahun_id', createForm.querySelector('[name="tahun_id"]').value);
                    window.history.replaceState(null, '', url);
                    editForm.action = button.dataset.updateUrl;
                    editForm.querySelector('[name="is_active"]').value = button.dataset.status;
                    editForm.querySelector('[name="keterangan"]').value = button.dataset.notes;
                    document.getElementById('assignment-identity').textContent = button.dataset.identity;
                    createForm.classList.add('hidden');
                    editForm.classList.remove('hidden');
                    title.textContent = 'Edit Penugasan';
                    showPanel();
                });
            });
            const rows = document.getElementById('assignment-rows');
            let nextIndex = rows ? Math.max(-1, ...Array.from(rows.querySelectorAll('[name]')).map(input => Number(input.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1 : 0;
            const addRow = () => {
                if (rows.children.length >= 100) return;
                const template = document.getElementById('assignment-row-template');
                rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));
            };
            document.getElementById('add-assignment-row')?.addEventListener('click', addRow);
            rows?.addEventListener('click', event => {
                const button = event.target.closest('[data-remove-row]');
                if (button && rows.children.length > 1) button.closest('[data-assignment-row]').remove();
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
            document.querySelectorAll('[data-status-form]').forEach(form => {
                const select = form.querySelector('select');
                const previousStatus = select.value;
                select.addEventListener('change', async () => {
                    const error = form.querySelector('[data-status-error]');
                    const data = new FormData(form);
                    select.disabled = true;
                    error.classList.add('hidden');
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST', body: data, headers: { Accept: 'application/json' }
                        });
                        if (!response.ok || new URL(response.url).pathname !== new URL(filters.action).pathname) {
                            throw new Error('Gagal menyimpan status. Silakan coba lagi.');
                        }
                        window.location.reload();
                    } catch (failure) {
                        select.value = previousStatus;
                        error.textContent = 'Gagal menyimpan status. Silakan coba lagi.';
                        error.classList.remove('hidden');
                        select.disabled = false;
                    }
                });
            });
            document.querySelectorAll('[data-delete-assignment]').forEach(form => {
                form.addEventListener('submit', event => {
                    if (!confirm('Hapus penugasan ini? Pilih Edit dan Nonaktif jika ingin menyimpan riwayat.')) event.preventDefault();
                });
            });
        })();
    </script>
</x-layout.layout>
