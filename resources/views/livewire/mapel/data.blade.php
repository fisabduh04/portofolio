<div>
    <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-base overflow-hidden border border-gray-200 dark:border-gray-700">
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
                    <x-btn wire:click="add" color="blue" size="sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span>Tambah Data</span>
                    </x-btn>

                     {{-- Simpan Data (Conditional) --}}
                    @if ($tombol_simpan)
                         <x-btn wire:click="store()" color="green" size="sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Data</span>
                        </x-btn>
                    @endif

                    {{-- Actions Dropdown --}}
                    <div class="relative">
                        <button id="actionsDropdownButton" data-dropdown-toggle="actionsDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-base border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all duration-200" type="button">
                            <span>Actions</span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="actionsDropdown" class="hidden z-10 w-48 bg-white rounded-base divide-y divide-gray-100 shadow-xl dark:bg-gray-700 dark:divide-gray-600 border border-gray-200 dark:border-gray-600">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actionsDropdownButton">
                                <li>
                                    <button wire:click="export" wire:loading.attr="disabled" class="flex items-center w-full gap-3 py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors duration-200 text-left">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>Export Excel</span>
                                    </button>
                                </li>
                                <li>
                                     <button type="button" onclick="document.getElementById('file-upload').click()" class="flex items-center w-full gap-3 py-2 px-4 text-left hover:bg-gray-50 dark:hover:bg-gray-600 dark:hover:text-white transition-colors duration-200">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span>Import Excel</span>
                                    </button>
                                </li>
                            </ul>
                            @if ($mapel_selected_id)
                                <div class="py-2">
                                    <button type="button" wire:click.prevent="del()" wire:confirm="Apakah anda yakin ingin menghapus data yang dipilih?" class="flex items-center w-full gap-3 py-2 px-4 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 dark:text-red-400 dark:hover:text-red-300 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Hapus Terpilih ({{ count($mapel_selected_id) }})</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Show Items per page --}}
                    <div class="flex items-center space-x-3 w-full md:w-auto">
                        
                        {{-- Hidden Import Input --}}
                        <input type="file" id="file-upload" wire:model="file" wire:change="import" class="hidden" accept=".xlsx,.xls">

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
                                    <input id="checkbox-all-search" type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all-search" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('id')">
                                <div class="flex items-center cursor-pointer">
                                    No
                                    <svg class="w-3 h-3 ms-1.5 {{ $sortField == 'id' && $sortDirection == 'desc' ? 'rotate-180' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">Action</th>
                            <th scope="col" class="px-4 py-3" wire:click="sortBy('kode')">
                                <div class="flex items-center cursor-pointer">
                                    Kode
                                    <svg class="w-3 h-3 ms-1.5 {{ $sortField == 'kode' && $sortDirection == 'desc' ? 'rotate-180' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                             <th scope="col" class="px-4 py-3" wire:click="sortBy('mapel')">
                                <div class="flex items-center cursor-pointer">
                                    Mata Pelajaran
                                    <svg class="w-3 h-3 ms-1.5 {{ $sortField == 'mapel' && $sortDirection == 'desc' ? 'rotate-180' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                             <th scope="col" class="px-4 py-3" wire:click="sortBy('jurusan_id')">
                                <div class="flex items-center cursor-pointer">
                                    Jurusan
                                    <svg class="w-3 h-3 ms-1.5 {{ $sortField == 'jurusan_id' && $sortDirection == 'desc' ? 'rotate-180' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                             <th scope="col" class="px-4 py-3" wire:click="sortBy('ket')">
                                <div class="flex items-center cursor-pointer">
                                    Keterangan
                                    <svg class="w-3 h-3 ms-1.5 {{ $sortField == 'ket' && $sortDirection == 'desc' ? 'rotate-180' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                    </svg>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mapel as $index => $val)
                            <tr class="bg-gray-50 border-b dark:bg-gray-700 dark:border-gray-600">
                                <td class="w-4 p-4"></td>
                                <td class="px-4 py-3">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <button type="button" wire:click="remove({{ $index }})" class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                                        Remove
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="kode.{{ $index }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Kode">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" wire:model="mapel.{{ $index }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Mapel">
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
                        @foreach ($mapellist as $index => $m)
                            <tr wire:key="mapel-{{ $m->id }}" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-table-search-{{ $m->id }}" type="checkbox" wire:key="{{ $m->id }}" wire:model.live="mapel_selected_id" value="{{ $m->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-table-search-{{ $m->id }}" class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $loop->iteration + ($mapellist->currentPage() - 1) * $mapellist->perPage() }}</td>
                                <td class="px-4 py-3">
                                    @if ($editmapelindex === $m->id)
                                        <button type="button" wire:click="update({{ $m->id }})" class="p-2 text-green-600 bg-green-50 rounded-base hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors" title="Save">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </button>
                                    @else
                                        <div class="flex items-center space-x-2">
                                            <x-btn wire:click="edit({{ $m->id }})" icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" title="Edit" />
                                            <x-btn data-modal-target="popup-modal-{{ $m->id }}" data-modal-toggle="popup-modal-{{ $m->id }}" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" title="Delete" />
                                        </div>
                                        <x-modal.hapus :id="$m->id" :action="route('mapel.destroy', $m->id)" />
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editmapelindex === $m->id)
                                        <input type="text" wire:model="editkode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        {{ $m->kode }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editmapelindex === $m->id)
                                        <input type="text" wire:model="editmapel" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    @else
                                        {{ $m->mapel }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editmapelindex === $m->id)
                                        <select wire:model="editjurusan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach ($jurusanlist as $item)
                                                <option value="{{ $item->id }}" {{ $item->id == $m->jurusan_id ? 'selected' : '' }}>{{ $item->jurusan }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        @if ($m->jurusan->id == 1)
                                            <x-badge color="blue" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @elseif($m->jurusan->id == 2)
                                            <x-badge color="green" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @elseif($m->jurusan->id == 3)
                                            <x-badge color="yellow" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @elseif($m->jurusan->id == 4)
                                            <x-badge color="purple" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @elseif($m->jurusan->id == 5)
                                            <x-badge color="red" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @elseif($m->jurusan->id == 6)
                                            <x-badge color="indigo" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @else
                                            <x-badge color="gray" text="{{ $m->jurusan->jurusan ?? 'Semua Jurusan' }}" />
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($editmapelindex === $m->id)
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
            
            <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
                <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $mapellist->firstItem() }}</span>
                    to
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $mapellist->lastItem() }}</span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $mapellist->total() }}</span>
                </span>
                
                {{ $mapellist->links() }}
            </nav>
    </div>
</div>
