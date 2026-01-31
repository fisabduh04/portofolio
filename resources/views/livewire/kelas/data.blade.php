<div class="flex flex-col sm:flex-row flex-wrap gap-2">
    <x-btn text="Tambah Data" icon="plus" wireClick="add" />
    <x-btn text="Export Excel" color="cyan" icon="export" href="{{ route('exportkelas') }}" />
    <form wire:submit.prevent="import" method="POST" enctype="multipart/form-data">
        <!-- Tombol trigger input file -->
        <x-btn text="Import File" color="green" icon="import" for="file" />

        <!-- Input file tersembunyi -->
        <input type="file" id="file" wire:model="file" class="hidden" accept=".xlsx,.xls">

        <!-- Tampilkan tombol submit jika file sudah dipilih -->
        @if ($file)
            <x-btn text="Unggah" type="submit" color="green" icon="import" />
        @endif
    </form>
    @if ($tombol_tambah)
        <x-btn text="Simpan Data" color="blue" icon="save" wireClick="store()" />
    @endif
    @if ($kelas_selected_id)
        <x-btn text="Hapus {{ count($kelas_selected_id) }} Data" color="red" icon="trash" wireClick="del()" />
    @endif

    <hr class="h-px my-4 bg-gray-200 border-0 dark:bg-gray-700">

    {{-- format table --}}
    <div class="w-full relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex flex-wrap items-center justify-between pb-4 space-y-4 flex-column sm:flex-row sm:space-y-0">
            <div>
                <span class="text-gray-700 dark:text-gray-400">Showing</span>
                <select id="table-limit" wire:model.live="perPage"
                    class="w-20 mx-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                </select>
                <span class="text-gray-700 dark:text-gray-400">entries</span>
            </div>
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div
                    class="absolute inset-y-0 left-0 flex items-center pointer-events-none rtl:inset-r-0 rtl:right-0 ps-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <input type="text" id="table-search" wire:model.live.debounce.500ms="search"
                    class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg ps-10 w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Search for items">
            </div>
        </div>

        <table class="w-full my-3 text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-all-search" type="checkbox" wire:model.live="SelectAll"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3" wire:click="sortBy('id')">
                        <div class="flex items-center">
                            No
                            <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                </svg></a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                    <th scope="col" class="px-6 py-3" wire:click="sortBy('kelas')">
                        <div class="flex items-center">
                            Kelas
                            <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                </svg></a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3" wire:click="sortBy('jurusan_id')">
                        <div class="flex items-center">
                            Jurusan
                            <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                </svg></a>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3" wire:click="sortBy('ket')">
                        <div class="flex items-center">
                            Keterangan
                            <a href="#"><svg class="w-3 h-3 ms-1.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                </svg></a>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- continuous counter (respect paginator offset) --}}
                @php
                    // If $kelaslist is a paginator, firstItem() returns the global index of the first item on the page.
                    // We subtract 1 so that ++$counter in the rows yields the correct 1-based numbering.
                    $counter = ($kelaslist->firstItem() ?? 1) - 1;
                @endphp
                {{-- Form input --}}
                @foreach ($kelas as $index => $val)
                    <tr>
                        <td></td>
                        <td>{{ ++$counter }}</td>
                        <td class="px-6 py-4">
                            <x-btn color="red" icon="trash" size="xs" wireClick="remove({{ $index }})" />
                        </td>
                        <td class="px-2 py-4 text-base">
                            <input type="text" wire:model="kelas.{{ $index }}"
                                class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </td>
                        <td class="px-2 py-4 text-base">
                            <select wire:model="jurusan.{{ $index }}"
                                class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Pilih Jurusan</option>
                                @foreach ($jurusanlist as $j)
                                    <option value="{{ $j->id }}">{{ $j->jurusan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-2 py-4 text-base">
                            <input type="text" wire:model="ket.{{ $index }}"
                                class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </td>
                    </tr>
                @endforeach



                {{-- Data Kelas --}}
                @foreach ($kelaslist as $index => $m)
                    <tr wire:key="kelas-{{ $m->id }}"
                        class="bg-white dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-{{ $m->id }}" type="checkbox"
                                    wire:key="{{ $m->id }}" wire:model.live="kelas_selected_id"
                                    value="{{ $m->id }}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-search-{{ $m->id }}"
                                    class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ ++$counter }}
            </td>
                        <td class="px-2 py-4">
                            @if ($editkelasindex === $m->id)
                                <x-btn color="blue" icon="check" size="sm" outline wireClick="update({{ $m->id }})" />
                            @else
                                <x-btn color="blue" icon="pencil" size="sm" outline wireClick="edit({{ $m->id }})" />
                            @endif
                        </td>
                        <td class="px-1 py-4 text-base">
                            @if ($editkelasindex === $m->id)
                                <input type="text" wire:model="editkelas"
                                    class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @else
                                {{ $m->kelas }}
                            @endif
                        </td>
                        <th scope="row"
                            class="px-1 py-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            @if ($editkelasindex === $m->id)
                                <select wire:model="editjurusan"
                                    class="w-full px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach ($jurusanlist as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $item->id == $m->jurusan_id ? 'selected' : '' }}>{{ $item->jurusan }}
                                        </option>
                                    @endforeach
                                @else
                                    {{ $m->jurusan->jurusan }}
                            @endif
                        </th>
                        <td class="px-1 py-4 text-base">
                            @if ($editkelasindex === $m->id)
                                <input type="text" wire:model="editket"
                                    class="w-full px-1 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @else
                                {{ $m->ket }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $kelaslist->links() }}
    </div>
</div>
