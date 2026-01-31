<div>
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-300 dark:border-gray-700">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">
                    <form class="flex items-center">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search" required="">
                        </div>
                    </form>
                </div>
                <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    
                    {{-- Tambah Data Button --}}
                    <button type="button" wire:click="add" class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-base text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                        </svg>
                        Tambah Data
                    </button>

                     {{-- Simpan Data (Conditional) --}}
                    @if ($tombol_tambah)
                         <button type="button" wire:click="store()" class="flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-base text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                            </svg>
                            Simpan Data
                        </button>
                    @endif

                    <div class="flex items-center space-x-3 w-full md:w-auto">
                        {{-- Actions Dropdown --}}
                        <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                            Actions
                        </button>
                        <div id="actionsDropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actionsDropdownButton">
                                <li>
                                    <a href="{{ route('exportkelas') }}" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Export Excel</a>
                                </li>
                                <li>
                                     <button type="button" onclick="document.getElementById('file-upload').click()" class="block w-full text-left py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Import Excel</button>
                                </li>
                            </ul>
                            @if ($kelas_selected_id)
                                <div class="py-1">
                                    <button type="button" wire:click.prevent="del()" wire:confirm="Apakah anda yakin ingin menghapus data yang dipilih?" class="block w-full text-left py-2 px-4 text-sm text-red-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-200 dark:hover:text-white">Delete Selected ({{ count($kelas_selected_id) }})</button>
                                </div>
                            @endif
                        </div>
                        
                         {{-- Hidden Import Form --}}
                        <form wire:submit.prevent="import" class="hidden" enctype="multipart/form-data">
                             <input type="file" id="file-upload" wire:model="file" onchange="this.form.dispatchEvent(new Event('submit', { bubbles: true }))" accept=".xlsx,.xls">
                        </form>


                        {{-- Filter Dropdown (Per Page) --}}
                        <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                            </svg>
                            Show {{ $perPage == -1 ? 'All' : $perPage }}
                            <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                        </button>
                        <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-base shadow dark:bg-gray-700">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Items per page</h6>
                            <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                <li><a href="#" wire:click.prevent="$set('perPage', 10)" class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">10</a></li>
                                <li><a href="#" wire:click.prevent="$set('perPage', 25)" class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">25</a></li>
                                <li><a href="#" wire:click.prevent="$set('perPage', 50)" class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">50</a></li>
                                <li><a href="#" wire:click.prevent="$set('perPage', 100)" class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">100</a></li>
                                 <li><a href="#" wire:click.prevent="$set('perPage', -1)" class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">All</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-heading uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all-search" type="checkbox" wire:model.live="SelectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('id')">
                                 <div class="flex items-center cursor-pointer group">
                                    No
                                    <svg class="w-3 h-3 ms-1.5 text-gray-400 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                    </svg>
                                </div>
                            </th>
                             <th scope="col" class="px-4 py-3">Aksi</th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('kelas')">
                                <div class="flex items-center cursor-pointer group">
                                    Kelas
                                    <svg class="w-3 h-3 ms-1.5 text-gray-400 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('jurusan_id')">
                                <div class="flex items-center cursor-pointer group">
                                    Jurusan
                                    <svg class="w-3 h-3 ms-1.5 text-gray-400 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('ket')">
                                <div class="flex items-center cursor-pointer group">
                                    Keterangan
                                    <svg class="w-3 h-3 ms-1.5 text-gray-400 group-hover:text-blue-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z" />
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                         @php
                            $counter = ($kelaslist->firstItem() ?? 1) - 1;
                        @endphp
                        
                        {{-- New Data Input Rows --}}
                        @foreach ($kelas as $index => $val)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="px-4 py-3"></td>
                                <td class="px-4 py-3">{{ ++$counter }}</td>
                                <td class="px-4 py-3">
                                    <button type="button" wire:click="remove({{ $index }})" class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="kelas.{{ $index }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Kelas">
                                </td>
                                <td class="px-4 py-3">
                                    <select wire:model="jurusan.{{ $index }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach ($jurusanlist as $j)
                                            <option value="{{ $j->id }}">{{ $j->jurusan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="ket.{{ $index }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Keterangan">
                                </td>
                            </tr>
                        @endforeach

                        {{-- Existing Data Rows --}}
                        @foreach ($kelaslist as $index => $m)
                            <tr wire:key="kelas-{{ $m->id }}" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-table-search-{{ $m->id }}" type="checkbox" wire:key="{{ $m->id }}" wire:model.live="kelas_selected_id" value="{{ $m->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-{{ $m->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ ++$counter }}</td>
                                <td class="px-4 py-3">
                                    @if ($editkelasindex === $m->id)
                                        <button type="button" wire:click="update({{ $m->id }})" class="p-2 text-green-600 bg-green-50 rounded-base hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors" title="Save">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @else
                                        <div class="flex items-center space-x-2">
                                            <x-btn wire:click="edit({{ $m->id }})" icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" />
                                            <x-btn data-modal-target="popup-modal-{{ $m->id }}" data-modal-toggle="popup-modal-{{ $m->id }}" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" title="Delete" />
                                        </div>
                                        <x-modal.hapus :id="$m->id" :action="route('kelas.destroy', $m->id)" />
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editkelasindex === $m->id)
                                        <input type="text" wire:model="editkelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        {{ $m->kelas }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editkelasindex === $m->id)
                                        <select wire:model="editjurusan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach ($jurusanlist as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == $m->jurusan_id ? 'selected' : '' }}>{{ $item->jurusan }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $m->jurusan->jurusan }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editkelasindex === $m->id)
                                        <input type="text" wire:model="editket" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        {{ $m->ket }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
             {{-- Updated Pagination --}}
            <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $kelaslist->firstItem() ?? 0 }}-{{ $kelaslist->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $kelaslist->total() }}</span>
                </span>
                
                {{ $kelaslist->links() }}
            </nav>
    </div>
</div>
