<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Siswa', 'href' => route('siswa.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <!-- Top Controls -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <!-- Actions Logic -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('siswa.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-base hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah
                    </a>

                    <!-- Bulk Delete (Hidden by default) -->
                    <button id="bulk-delete-btn" data-modal-target="popup-modal-bulk" data-modal-toggle="popup-modal-bulk" class="hidden inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 transition-colors bg-white border border-red-200 rounded-base hover:bg-red-50 focus:ring-4 focus:ring-red-100 dark:bg-gray-800 dark:text-red-400 dark:border-red-900 dark:hover:bg-gray-700 dark:focus:ring-gray-700 shadow-sm animate-fade-in">
                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                        </svg>
                        Hapus (<span id="selected-count">0</span>)
                    </button>

                     <!-- Import -->
                     <button data-modal-target="small-modal" data-modal-toggle="small-modal" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-base hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 shadow-sm">
                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                             <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import
                    </button>
                    
                    <!-- Dynamic Export Button -->
                    <a id="export-btn" href="{{ route('exportsiswa') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-base hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 shadow-sm">
                         <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span id="export-text">Export</span>
                    </a>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('siswa.index') }}" class="relative w-full sm:w-64 lg:w-96 group">
                    <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-focus-within:text-blue-500 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" name="search" value="{{ request('search') }}"
                           class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-base bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-all" 
                           placeholder="Cari Nama, NISN, NIPD..." required />
                </form>
            </div>

            <!-- Table -->
            <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                <form id="bulk-action-form" method="POST" action="">
                    @csrf 
                    @method('DELETE') <!-- Default method for delete -->
                    
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-xs text-heading uppercase bg-neutral-secondary-soft border-b border-default">
                            <tr>
                                <th scope="col" class="p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">
                                    <a href="{{ route('siswa.index', array_merge(request()->query(), ['sort' => 'nama', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center group">
                                        Nama Siswa
                                        <svg class="w-3 h-3 ms-1.5 {{ request('sort') == 'nama' && request('direction') == 'desc' ? 'rotate-180' : '' }} text-gray-400 group-hover:text-blue-500 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"/>
                                        </svg>
                                    </a>
                                </th>
                                <th scope="col" class="px-6 py-3 hidden md:table-cell">NIPD / NISN</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswa as $index => $sis)
                                <tr class="bg-neutral-primary border-b border-default hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="ids[]" value="{{ $sis->id }}" class="checkbox-item w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $siswa->firstItem() + $index }}</td>
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center gap-3">
                                            <div class="relative w-9 h-9 overflow-hidden rounded-full bg-gray-100 border border-gray-200 shadow-sm flex-shrink-0">
                                                @if($sis->foto)
                                                    <img src="{{ asset('storage/' . $sis->foto) }}" class="w-full h-full object-cover" alt="Foto">
                                                @else
                                                     <div class="flex items-center justify-center w-full h-full text-xs font-bold text-gray-600 uppercase bg-gray-200">
                                                        {{ substr($sis->nama, 0, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-heading">{{ $sis->nama }}</span>
                                                <span class="text-xs text-body-subtle">{{ $sis->email ?? 'No Email' }}</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="px-6 py-4 hidden md:table-cell">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium">{{ $sis->nipd }}</span>
                                            <span class="text-xs text-gray-500">{{ $sis->nisn }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 min-w-[140px]">
                                        <select onchange="updateSiswaStatus({{ $sis->id }}, this)"
                                            class="text-xs rounded-full cursor-pointer block w-full p-2 border transition-colors duration-200 focus:outline-none focus:ring-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="Aktif" class="text-green-600 bg-green-50" {{ $sis->aktif == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Lulus" class="text-blue-600 bg-blue-50" {{ $sis->aktif == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                            <option value="Non-Aktif" class="text-red-600 bg-red-50" {{ $sis->aktif == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                        </select>
                                        <span id="status-msg-{{ $sis->id }}" class="hidden text-[10px] mt-1 ml-2 font-medium italic"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <x-btn href="{{ route('siswa.show', $sis->id) }}" icon="eye" color="gray" variant="ghost" size="sm" class="!p-2" />
                                            <x-btn href="{{ route('siswa.edit', $sis->id) }}" icon="pencil-square" color="blue" variant="ghost" size="sm" class="!p-2" />
                                            <!-- Separate Delete Form for Single Item -->
                                            <x-btn data-modal-target="popup-modal-{{ $sis->id }}" data-modal-toggle="popup-modal-{{ $sis->id }}" icon="trash" color="red" variant="ghost" size="sm" class="!p-2" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center py-6">
                                            <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-base font-medium">Belum ada data siswa.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            <!-- Individual Deletion Modals (Moved outside form to prevent nested forms) -->
            @foreach ($siswa as $sis)
                <x-modal.hapus :id="$sis->id" :action="route('siswa.destroy', $sis->id)" />
            @endforeach

            <!-- Footer Pagination -->
            <div class="flex flex-col items-center justify-between gap-4 mt-6 sm:flex-row">
                 <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>Tampilkan</span>
                    <form method="GET" action="{{ route('siswa.index') }}">
                         <input type="hidden" name="search" value="{{ request('search') }}">
                         <input type="hidden" name="sort" value="{{ request('sort') }}">
                         <input type="hidden" name="direction" value="{{ request('direction') }}">
                        <select name="perPage" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>data per halaman</span>
                </div>
                
                <div class="w-full sm:w-auto">
                    {{ $siswa->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bulk Delete Warning Modal -->
    <!-- Bulk Delete Warning Modal -->
    <x-modal.hapus id="bulk" action="#" formTarget="bulk-action-form" message="Apakah Anda yakin ingin menghapus data siswa yang dipilih?" />

    <!-- Import Modal -->
    <div id="small-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-gray-600">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white">Import Data Siswa</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="small-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 space-y-4 md:p-5">
                    <form action="{{ route('importsiswa') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">XLSX, CSV, XLS (Max 2MB)</p>
                                </div>
                                <input id="dropzone-file" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required />
                            </label>
                        </div> 
                        <div class="flex justify-end mt-4">
                             <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Logic for Bulk Actions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
             const checkAll = document.getElementById('checkbox-all');
            const checkboxes = document.querySelectorAll('.checkbox-item');
            const selectedCount = document.getElementById('selected-count');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const exportBtn = document.getElementById('export-btn');
            const exportText = document.getElementById('export-text');
            const bulkActionForm = document.getElementById('bulk-action-form');
            
            // Store original export URL and classes
            const originalExportHref = exportBtn.href;
            const defaultClasses = ['text-gray-700', 'bg-white', 'border-gray-300', 'hover:bg-gray-50', 'focus:ring-gray-100'];
            const activeClasses = ['text-green-700', 'bg-green-50', 'border-green-200', 'hover:bg-green-100', 'focus:ring-green-100'];

            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.checkbox-item:checked').length;
                selectedCount.textContent = checkedCount;
                
                if (checkedCount > 0) {
                    // Show Delete Button
                    bulkDeleteBtn.classList.remove('hidden');
                    
                    // Update Export Button Style & Text
                    exportBtn.classList.remove(...defaultClasses);
                    exportBtn.classList.add(...activeClasses);
                    exportText.textContent = `Export (${checkedCount})`;
                    
                } else {
                    // Hide Delete Button
                    bulkDeleteBtn.classList.add('hidden');
                    
                    // Reset Export Button Style & Text
                    exportBtn.classList.remove(...activeClasses);
                    exportBtn.classList.add(...defaultClasses);
                    exportText.textContent = 'Export';
                }
            }

            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateBulkActions();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    checkAll.checked = allChecked;
                    updateBulkActions();
                });
            });

            // Handle Bulk Delete - Mengatur URL target tanpa menggunakan browser confirm()
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function(e) {
                    // Set action form ke route bulk delete
                    bulkActionForm.action = "{{ route('siswa.bulkDelete') }}";
                    // Modal akan terbuka otomatis via data-modal-target
                });
            }
            
            // Handle Dynamic Export
            exportBtn.addEventListener('click', function(e) {
                const checkedCount = document.querySelectorAll('.checkbox-item:checked').length;
                
                if (checkedCount > 0) {
                    e.preventDefault();
                    const selectedIds = Array.from(document.querySelectorAll('.checkbox-item:checked')).map(cb => cb.value);
                    
                    // Redirect to export route with IDs
                    const url = new URL(originalExportHref);
                    url.searchParams.set('ids', selectedIds.join(','));
                    window.location.href = url.toString();
                }
                // If 0 selected, let standard href work (Export All)
            });

            // Auto-submit search with debounce
            const searchInput = document.querySelector('input[name="search"]');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.form.submit();
                    }, 800); // 800ms delay for auto-submit
                });
            }
        });

        // --- Inline Status Update for Siswa (Fetch API) ---
        function updateSiswaStatus(id, selectElement) {
            const statusBaru = selectElement.value;
            const msgSpan = document.getElementById(`status-msg-${id}`);

            // Update Color Immediately
            updateSiswaSelectColor(selectElement);

            // Show "Menyimpan..."
            if(msgSpan) {
                msgSpan.innerText = 'Menyimpan...';
                msgSpan.classList.remove('hidden', 'text-green-600', 'text-red-600');
                msgSpan.classList.add('text-gray-500');
            }
            selectElement.disabled = true;

            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

            fetch(`/siswa/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    aktif: statusBaru
                })
            })
            .then(response => {
                if (response.ok) {
                    if(msgSpan) {
                        msgSpan.innerText = 'Tersimpan!';
                        msgSpan.classList.remove('text-gray-500');
                        msgSpan.classList.add('text-green-600');
                        setTimeout(() => { msgSpan.classList.add('hidden'); }, 2000);
                    }
                } else {
                    throw new Error('Gagal menyimpan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Gagal menyimpan perubahan status.");
                if(msgSpan) {
                    msgSpan.innerText = 'Gagal!';
                    msgSpan.classList.remove('text-gray-500');
                    msgSpan.classList.add('text-red-600');
                }
            })
            .finally(() => {
                selectElement.disabled = false;
            });
        }

        function updateSiswaSelectColor(select) {
            select.className = "text-xs rounded-full font-medium shadow-sm cursor-pointer block w-full p-2 border transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:bg-gray-700 dark:border-gray-600";
            switch(select.value) {
                case 'Aktif':
                    select.classList.add('bg-green-50', 'text-green-700', 'border-green-300', 'focus:ring-green-500');
                    break;
                case 'Lulus':
                    select.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-300', 'focus:ring-blue-500');
                    break;
                case 'Non-Aktif':
                default:
                    select.classList.add('bg-red-50', 'text-red-700', 'border-red-300', 'focus:ring-red-500');
            }
        }

        // Initialize colors on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('select[onchange^="updateSiswaStatus"]').forEach(select => {
                updateSiswaSelectColor(select);
            });
        });
    </script>
</x-layout.layout>
