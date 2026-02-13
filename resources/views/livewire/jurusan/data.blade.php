<div>
    <div class="flex items-center gap-2 mb-6">
        <x-btn text="Tambah" icon="plus" wireClick="add" color="blue" />
        
        @if ($kepala_table)
            <x-btn text="Simpan Semua" icon="save" type="submit" form="formSave" color="green" />
        @endif
    </div>

    <div class="relative overflow-x-auto bg-white dark:bg-gray-800 shadow-xs rounded-base border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">No</th>
                    <th scope="col" class="px-6 py-3 font-medium">Nama Jurusan</th>
                    <th scope="col" class="px-6 py-3 font-medium">Kode</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dynamic Input Rows --}}
                <form id="formSave" wire:submit.prevent="store">
                    @foreach ($kode as $index => $val)
                        <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 animate-in fade-in slide-in-from-left-2">
                            <td class="px-6 py-4 text-gray-900 dark:text-white">#</td>
                            <td class="px-6 py-4">
                                <input type="text" wire:model="jurusan.{{ $index }}"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Nama Jurusan" />
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" wire:model="kode.{{ $index }}"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Kode" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" wire:click="remove({{ $index }})"
                                    class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                                    title="Hapus Baris">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </form>

                {{-- Data Table Rows --}}
                @foreach ($semuajurusan as $key => $k)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($editStudentIndex === $k->id)
                                <input type="text" wire:model="editjurusan"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @else
                                <span class="text-gray-900 dark:text-white font-semibold">{{ $k->jurusan }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($editStudentIndex === $k->id)
                                <input type="text" wire:model="editkode"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            @else
                                <span class="font-medium text-gray-900 dark:text-white">{{ $k->kode }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" wire:click="del({{ $k->id }})"
                                    wire:confirm.prompt="Apakah Anda yakin akan menghapus data ini? Data Jurusan DIHAPUS maka semua data yang berhubungan dengannya juga akan terhapus?\n\nType DELETE to confirm|DELETE"
                                    class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                                    title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                @if ($editStudentIndex === $k->id)
                                    <button type="button" wire:click="update({{ $k->id }})"
                                        class="p-2 text-green-600 bg-green-50 rounded-base hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/40 transition-colors"
                                        title="Simpan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" wire:click="editStudent({{ $k->id }})"
                                        class="p-2 text-blue-600 bg-blue-50 rounded-base hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($semuajurusan->isEmpty() && !$kepala_table)
        <div class="px-6 py-12 text-center bg-white dark:bg-gray-800 rounded-b-base border border-t-0 border-gray-200 dark:border-gray-700">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Belum ada data jurusan</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silahkan klik tombol Tambah untuk membuat data baru.</p>
            </div>
        </div>
    @endif
</div>

