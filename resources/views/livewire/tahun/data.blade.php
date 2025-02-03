<div>
    {{-- @if($errors->any())
    @foreach ($errors->all() as $error)
    {{ $error }} <br>
    @endforeach
    @endif--}}
    <x-form.session />

    @if($form)
    <form wire:submit="store">
        <div class="grid gap-6 mb-6 md:grid-cols-2">

            <x-form.input wire:model="tahun" name="tahun" label="Tahun Akademik" />
            <div>
                <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Semester</label>
                <select id="countries" wire:model="semester"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('semester') bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500 @enderror">
                    <option selected>Pilih Semester</option>
                    <option value="Ganjil" {{ $semester=='Ganjil' ? 'selected' :''}}>Ganjil</option>
                    <option value="Genap" {{ $semester=='Genap' ? 'selected' :''}}>Genap</option>
                </select>
                @error('semester')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <x-form.input wire:model="tanggalmulai" name="tanggalmulai" type=date label="Tanggal Mulai" />
            <x-form.input wire:model="tanggalakhir" name="tanggalakhir" type=date label="Tanggal Berakhir" />
            <div>
                <label for="isActive" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Aktif</label>
                <select id="isActive" wire:model="isActive"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error('isActive') bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500 @enderror">
                    <option selected>Choose a country</option>
                    <option value="0">Non-Aktif</option>
                    <option value="1">Aktif</option>
                </select>
                @error('isActive')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <hr>
        <button type="submit"
            class="px-5 py-2.5 text-sm font-medium text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="w-3.5 h-3.5 text-white me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 6c0 1.657-3.134 3-7 3S5 7.657 5 6m14 0c0-1.657-3.134-3-7-3S5 4.343 5 6m14 0v6M5 6v6m0 0c0 1.657 3.134 3 7 3s7-1.343 7-3M5 12v6c0 1.657 3.134 3 7 3s7-1.343 7-3v-6" />
            </svg>

            {{ $tombol_simpan }}
        </button>
        <button wire:click="close" type="reset"
            class="inline-flex items-center text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-3 py-2.5 text-center me-2 mb-2">
            <svg class="w-4 h-4 text-white me-1 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Close</button>
    </form>
    @endif


    @if(!$form)
    <button wire:click="tambah" type="button"
        class="inline-flex items-center text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-3 py-2.5 text-center me-2 mb-2 ">
        <svg class="w-3.5 h-3.5 text-white me-1 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>

        Tambah
    </button>
    @endif
    <hr>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Aksi
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tahun Akademik
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Semester
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Mulai
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Akhir
                    </th>
                    <th scope="col" class="px-6 py-3 text-center">
                        Aktif
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tahunlist as $t)


                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">

                        <button type="button" wire:click="edit({{ $t->id }})">
                            <span
                                class="inline-flex items-center justify-center p-2 text-sm font-semibold text-blue-800 hover:bg-blue-500 hover:text-white hover:border rounded-lg dark:bg-blue-700 dark:text-blue-300 group">
                                <svg class="w-4 h-4 text-blue-800 group-hover:text-white dark:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                </svg>

                                <span class="sr-only">Icon description</span>
                            </span>
                        </button>

                        <button wire:click="del({{ $t->id }})"
                            wire:confirm="Apakah Anda Yakin akan menghapus? Jika data ini dihapus maka semua data ynag berhubungan dengan data ini akan terhapus juga"
                            class="relative inline-flex items-center justify-center p-2 text-sm font-medium text-red-500 rounded-lg hover:bg-red-500 hover:text-white hover:border border-red-500 group">
                            <svg class="w-4 h-4 text-red-500 group-hover:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                            </svg>
                        </button>



                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
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
                                    id="isActiveToggle{{ $t->id }}" class="sr-only peer" @if ($t->isActive) checked
                                @endif>
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