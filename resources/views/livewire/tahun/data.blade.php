<div>
    @if (!$form)
        <div class="flex justify-start mb-6">
            <x-btn text="Tambah Tahun" icon="plus" wireClick="tambah" color="blue" />
        </div>
    @endif

    <!-- Form Section -->
    @if ($form)
        <div class="mb-8 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center gap-2 mb-6">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-base dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $tahunID ? 'Edit' : 'Tambah' }} Tahun Akademik
                </h3>
            </div>

            <form wire:submit="store" class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <x-form.input wire:model="tahun" name="tahun" label="Tahun Akademik" placeholder="Contoh: 2024/2025" />
                    
                    <div>
                        <label for="semester" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Semester</label>
                        <select id="semester" wire:model="semester"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('semester') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        @error('semester')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form.input wire:model="tanggalmulai" name="tanggalmulai" type="date" label="Tanggal Mulai" />
                    <x-form.input wire:model="tanggalakhir" name="tanggalakhir" type="date" label="Tanggal Berakhir" />

                    <div>
                        <label for="isActive" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="isActive" wire:model="isActive"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('isActive') border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500 @enderror">
                            <option value="0">Non-Aktif</option>
                            <option value="1">Aktif</option>
                        </select>
                        @error('isActive')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <x-btn :text="$tombol_simpan" icon="check" type="submit" color="blue" />
                    <x-btn text="Batal" wireClick="close" color="light" />
                </div>
            </form>
            <div class="my-8 border-t border-gray-100 dark:border-gray-700"></div>
        </div>
    @endif

    <!-- Table Section -->
    <div class="relative overflow-x-auto bg-white dark:bg-gray-800 shadow-xs rounded-base border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">No</th>
                    <th scope="col" class="px-6 py-3 font-medium">Tahun Akademik</th>
                    <th scope="col" class="px-6 py-3 font-medium text-center">Semester</th>
                    <th scope="col" class="px-6 py-3 font-medium text-center">Periode</th>
                    <th scope="col" class="px-6 py-3 font-medium text-center">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tahunlist as $t)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $t->tahun }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($t->semester == 'Ganjil')
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-purple-900/40 dark:text-purple-300">
                                    Ganjil
                                </span>
                            @else
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full dark:bg-indigo-900/40 dark:text-indigo-300">
                                    Genap
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span>
                                {{ \Carbon\Carbon::parse($t->tanggalmulai)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($t->tanggalakhir)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <button wire:click.prevent="toggleIsActive({{ $t->id }})" 
                                    class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" @if ($t->isActive) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium {{ $t->isActive ? 'text-blue-600' : 'text-gray-400' }}">
                                        {{ $t->isActive ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" wire:click="edit({{ $t->id }})"
                                    class="p-2 text-blue-600 bg-blue-50 rounded-base hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" wire:click="del({{ $t->id }})"
                                    wire:confirm.prompt="Apakah Anda Yakin akan menghapus? Jika data ini dihapus maka semua data yang berhubungan dengan data ini akan terhapus juga|DELETE"
                                    class="p-2 text-red-600 bg-red-50 rounded-base hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-body-subtle">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-4 text-lg font-medium text-heading">Tidak ada data</p>
                                <p class="mt-1 text-sm">Silahkan tambah data tahun akademik baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
