<div>
    {{--
    <x-form.session /> --}}

    {{-- Table format flowbite --}}
    <div>
        <button type="button" wire:click="add"
            class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">

            <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z"
                    clip-rule="evenodd" />
            </svg>

            Tambah
        </button>
        @if($kepala_table)
        <button type="submit" form="formSave"
            class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 inline-flex items-center">
            <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M4 5a1 1 0 0 1 1-1h11.586a1 1 0 0 1 .707.293l2.414 2.414a1 1 0 0 1 .293.707V19a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" />
                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                    d="M8 4h8v4H8V4Zm7 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            Save All
        </button>
        @endif
    </div>

    <hr>
    {{-- Table format tailwind --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        No
                    </th>
                    <th scope="col" class="px-5 py-3">
                        Aksi
                    </th>
                    <th scope="col" class="px-5 py-3">
                        Kode
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Jurusan
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- Kode Data Input Dinamis --}}
                <form id="formSave" wire:submit.prevent="store">
                    @foreach($kode as $index => $val)
                    <tr>
                        <td>#</td>
                        <td>
                            <button type="button" wire:click="remove({{ $index }})"
                                class="px-2.5 py-2 text-xs font-medium text-center inline-flex items-center justify-center text-red-700 hover:text-white rounded-lg hover:bg-red-600 focus:ring-4 focus:outline focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 group">

                                <svg class="w-4 h-4 text-red-700 group-hover:text-white dark:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                </svg>
                                Del
                            </button>
                        </td>
                        <td>
                            <input type="text" wire:model="kode.{{ $index }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " />
                        </td>
                        <td>
                            <input type="text" wire:model="jurusan.{{ $index }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " />
                        </td>
                    </tr>
                    @endforeach
                </form>
                {{-- End Kode Data Input Dinamis --}}





                {{-- Data Table --}}
                @foreach ($semuajurusan as $key=>$k)
                <tr
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-3 py-3">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-2 py-3">
                        <button type="button" wire:click="del({{ $k->id }})"
                            wire:confirm.prompt="Apakah Anda yakin akan menghapus data ini Data Jurusan DIHAPUS maka semua data yang berhubungan dengannya juga akan terhapus?\n\nType DELETE to confirm|DELETE"
                            class="px-2.5 py-2 text-xs font-medium text-center inline-flex items-center justify-center text-red-700 hover:text-white rounded-lg hover:bg-red-600 focus:ring-4 focus:outline focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 group">

                            <svg class="w-4 h-4 text-red-700 group-hover:text-white dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                            </svg>
                            Del
                        </button>
                        @if ($editStudentIndex === $k->id)
                        <button type="button" wire:click="update({{ $k->id }})"
                            class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-blue-700 rounded-lg hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group">
                            <svg class="w-4 h-4 text-blue-700 group-hover:text-white dark:text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Save
                        </button>
                        @else
                        <button type="button" wire:click="editStudent({{ $k->id }})"
                            class="px-2.5 py-2 text-xs font-medium text-center inline-flex items-center justify-center text-blue-700 hover:text-white rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group">

                            <svg class="w-4 h-4 text-blue-700 group-hover:text-white dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                            </svg>
                            Edit
                        </button>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @if ($editStudentIndex === $k->id)
                        <div>
                            <input type="text" id="editkode" wire:model="editkode"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        @else
                        {{ $k->kode }}
                        @endif
                    </td>
                    <th scope="row" class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        @if ($editStudentIndex === $k->id)
                        <div>
                            <input type="text" id="editkode" wire:model="editjurusan"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </div>
                        @else
                        {{ $k->jurusan}}
                        @endif
                    </th>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>