<div>


    @if ($form)
        <form wire:submit="store">
            <div class="grid gap-6 mb-6 md:grid-cols-2">

                <x-form.input wire:model="tahun" name="tahun" label="Tahun Akademik" />
                <x-form.select 
                    wire:model="semester" 
                    name="semester" 
                    label="Semester" 
                    placeholder="Pilih Semester" 
                    :options="['Ganjil' => 'Ganjil', 'Genap' => 'Genap']" 
                    :selected="$semester" />

                <x-form.input wire:model="tanggalmulai" name="tanggalmulai" type="date" label="Tanggal Mulai" />
                <x-form.input wire:model="tanggalakhir" name="tanggalakhir" type="date" label="Tanggal Berakhir" />

                <x-form.select 
                    wire:model="isActive" 
                    name="isActive" 
                    label="Status Aktif" 
                    :options="[0 => 'Non-Aktif', 1 => 'Aktif']" 
                    :selected="$isActive" />
            </div>

            <div class="flex items-center space-x-2">
                <x-btn type="submit" color="blue" icon="save" :text="$tombol_simpan" />
                
                <x-btn color="red" icon="x" text="Close" wireClick="close" />
            </div>
        </form>
    @endif


    @if (!$form)
        <div class="mb-4">
            <x-btn text="Tambah Tahun Akademik" icon="plus" color="blue" wireClick="tambah" pill />
        </div>
    @endif


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                    <th scope="col" class="px-6 py-3">Tahun Akademik</th>
                    <th scope="col" class="px-6 py-3">Semester</th>
                    <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                    <th scope="col" class="px-6 py-3">Tanggal Akhir</th>
                    <th scope="col" class="px-6 py-3 text-center">Aktif</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($tahunlist as $t)
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <x-btn color="blue" icon="pencil" outline size="sm" wireClick="edit({{ $t->id }})" />
                                
                                <x-btn color="red" icon="trash" outline size="sm" 
                                    wireClick="del({{ $t->id }})" 
                                    wire:confirm="Apakah Anda Yakin akan menghapus? Jika data ini dihapus maka semua data yang berhubungan dengan data ini akan terhapus juga" />
                            </div>
                        </td>
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $t->tahun }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $t->semester }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($t->tanggalmulai)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($t->tanggalakhir)->format('d M Y') }}

                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <label class="inline-flex items-center mr-4">
                                    <input wire:click.prevent="toggleIsActive({{ $t->id }})" type="checkbox"
                                        id="isActiveToggle{{ $t->id }}" class="sr-only peer"
                                        @if ($t->isActive) checked @endif>
                                    <div
                                        class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                    </div>
                                    @if ($t->isActive)
                                        <span
                                            class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 ">Aktif</span>
                                    @else
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-red-400 ">Non
                                            aktif</span>
                                    @endif
                                </label>
                            </div>
                        </td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
