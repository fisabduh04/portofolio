<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Wali Kelas', 'href' => route('walikelas.index')],
    ]" />

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 p-4 dark:border-gray-700">
            <div>
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Wali Kelas</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Data: {{ $penugasans->total() }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($editing)
                    <x-btn :href="route('walikelas.index', ['tahun_id' => $selectedTahun->id])" size="sm">Tambah Data</x-btn>
                @else
                    <x-btn id="toggle-assignment" size="sm" :disabled="! $selectedTahun" aria-controls="assignment-panel" aria-expanded="{{ $errors->any() ? 'true' : 'false' }}">+ Tambah Data</x-btn>
                @endif
                <x-btn :href="route('walikelas.index', ['tahun_id' => $selectedTahun?->id])" color="light" size="sm">Refresh</x-btn>
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
            <section id="assignment-panel" @class(['border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900', 'hidden' => ! $editing && ! $errors->any()])>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $editing ? 'Edit Penugasan' : 'Tambah Penugasan Wali Kelas' }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Periode: <strong>{{ $selectedTahun->tahun }} — {{ $selectedTahun->semester }}</strong></p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Mengaktifkan wali kelas akan menonaktifkan wali lain pada kelas dan periode yang sama.</p>

                @if ($editing)
                    <form method="POST" action="{{ route('walikelas.update', $editing) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $editing->kelas->kelas }} — {{ $editing->pegawai->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Untuk mengganti pegawai, tambahkan penugasan baru agar data sebelumnya tetap tersimpan.</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="edit-status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                <select id="edit-status" name="is_active" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="1" @selected(old('is_active', $editing->is_active) == 1)>Aktif</option>
                                    <option value="0" @selected(old('is_active', $editing->is_active) == 0)>Nonaktif</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit-keterangan" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                                <textarea id="edit-keterangan" name="keterangan" maxlength="2000" rows="3" class="block w-full rounded-lg border border-gray-300 bg-white p-3 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('keterangan', $editing->keterangan) }}</textarea>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <x-btn :href="route('walikelas.index', ['tahun_id' => $selectedTahun->id])" color="light">Batal</x-btn>
                            <x-btn type="submit">Simpan Perubahan</x-btn>
                        </div>
                    </form>
                @else
                    <form method="POST" action="{{ route('walikelas.store') }}" class="mt-4 space-y-4" id="assignment-form">
                        @csrf
                        <input type="hidden" name="tahun_id" value="{{ $selectedTahun->id }}">
                        <div id="assignment-rows" class="space-y-3">
                            @foreach ($inputRows as $index => $row)
                                @include('walikelas.row', ['index' => $index, 'row' => $row])
                            @endforeach
                        </div>
                        <button type="button" id="add-assignment-row" class="w-full rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-medium text-gray-600 hover:border-blue-500 hover:text-blue-600 dark:border-gray-600 dark:text-gray-300">+ Tambah Baris Penugasan</button>
                        <div class="flex justify-end gap-3">
                            <x-btn id="cancel-assignment" color="light">Batal</x-btn>
                            <x-btn type="submit">Simpan Penugasan</x-btn>
                        </div>
                    </form>
                    <template id="assignment-row-template">
                        @include('walikelas.row', ['index' => '__INDEX__', 'row' => []])
                    </template>
                @endif
            </section>
        @else
            <div class="p-4 text-sm text-gray-600 dark:text-gray-300">Belum ada periode. <a href="{{ route('tahun.index') }}" class="font-medium text-blue-600 underline dark:text-blue-400">Tambahkan tahun dan semester</a> terlebih dahulu.</div>
        @endif

        <form method="GET" action="{{ route('walikelas.index') }}" class="flex flex-col gap-3 border-b border-gray-200 p-4 lg:flex-row lg:flex-wrap lg:items-end dark:border-gray-700" id="assignment-filters">
            <div class="w-full lg:min-w-[240px] lg:flex-1">
                <label for="search-wali" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                <input id="search-wali" name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama wali atau kelas..." maxlength="100" class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="w-full shrink-0 lg:w-[300px]">
                <label for="filter-tahun" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun / Semester</label>
                <select id="filter-tahun" name="tahun_id" class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" @disabled(! $selectedTahun)>
                    @forelse ($tahun as $item)
                        <option value="{{ $item->id }}" @selected($selectedTahun?->id == $item->id)>{{ $item->tahun }} — {{ $item->semester }}</option>
                    @empty
                        <option value="">Belum ada periode</option>
                    @endforelse
                </select>
            </div>
            <div class="w-full shrink-0 lg:w-36">
                <label for="filter-kelas" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas</label>
                <select id="filter-kelas" name="kelas_id" class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}" @selected(request('kelas_id') == $item->id)>{{ $item->kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full shrink-0 lg:w-36">
                <label for="filter-status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="filter-status" name="status" class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="w-full shrink-0 lg:w-28">
                <label for="per-page" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tampilkan</label>
                <select id="per-page" name="per_page" class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected(request('per_page', 10) == $size)>{{ $size }} Data</option>
                    @endforeach
                </select>
            </div>
            <x-btn type="submit" class="h-11">Cari</x-btn>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 text-xs uppercase text-gray-900 dark:bg-gray-700 dark:text-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-4">No</th>
                        <th scope="col" class="px-4 py-4">Aksi</th>
                        <th scope="col" class="px-4 py-4">Kelas</th>
                        <th scope="col" class="px-4 py-4">Wali Kelas</th>
                        <th scope="col" class="px-4 py-4">Status</th>
                        <th scope="col" class="px-4 py-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penugasans as $penugasan)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-4">{{ $penugasans->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('walikelas.index', ['tahun_id' => $selectedTahun->id, 'edit' => $penugasan->id]) }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400" aria-label="Edit wali kelas {{ $penugasan->pegawai->name }}">Edit</a>
                                    <form method="POST" action="{{ route('walikelas.destroy', $penugasan) }}" data-delete-assignment>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 hover:underline dark:text-red-400" aria-label="Hapus wali kelas {{ $penugasan->pegawai->name }}">Hapus</button>
                                    </form>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">{{ $penugasan->kelas->kelas }}</td>
                            <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ $penugasan->pegawai->name }}</td>
                            <td class="px-4 py-4">
                                <span @class(['rounded-full px-3 py-1 text-xs font-medium', 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' => $penugasan->is_active, 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $penugasan->is_active])>{{ $penugasan->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </td>
                            <td class="max-w-sm whitespace-pre-line break-words px-4 py-4">{{ $penugasan->keterangan ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada penugasan yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $penugasans->links() }}</div>
    </div>

    <script>
        (() => {
            const panel = document.getElementById('assignment-panel');
            const toggle = document.getElementById('toggle-assignment');
            toggle?.addEventListener('click', () => {
                if (!panel) return;
                panel.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!panel.classList.contains('hidden')));
            });
            document.getElementById('cancel-assignment')?.addEventListener('click', () => {
                panel.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            });
            const rows = document.getElementById('assignment-rows');
            let nextIndex = rows ? Math.max(-1, ...Array.from(rows.querySelectorAll('[name]')).map(input => Number(input.name.match(/\[(\d+)\]/)?.[1] ?? -1))) + 1 : 0;
            document.getElementById('add-assignment-row')?.addEventListener('click', () => {
                if (rows.children.length >= 100) return;
                const template = document.getElementById('assignment-row-template');
                rows.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)));
            });
            rows?.addEventListener('click', event => {
                const button = event.target.closest('[data-remove-row]');
                if (button && rows.children.length > 1) button.closest('[data-assignment-row]').remove();
            });
            const filters = document.getElementById('assignment-filters');
            filters.querySelectorAll('select').forEach(select => {
                select.addEventListener('change', () => filters.requestSubmit());
            });
            document.querySelectorAll('[data-delete-assignment]').forEach(form => {
                form.addEventListener('submit', event => {
                    if (!confirm('Hapus penugasan ini? Pilih Edit dan Nonaktif jika ingin menyimpan riwayat.')) event.preventDefault();
                });
            });
        })();
    </script>
</x-layout.layout>
