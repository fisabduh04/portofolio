<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jadwal', 'href' => 'jadwal'],
    ]" />
    <livewire:toast />
    <div><input id="errorBox" /></div>

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Jadwal Mata Pelajaran</h1>

        <x-btn text="Tambah" icon="plus" onclick="tambah()" />


        <div class="grid gap-6 mb-6 md:grid-cols-2">
            <div>
                <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                    Ajaran</label>
                <select id="tahun"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Tahun Ajaran</option>
                    @if ($tahun->isEmpty())
                        <option value="">Tidak ada tahun ajaran</option>
                    @endif
                    @foreach ($tahun as $t)
                        <option value="{{ $t->id }}">{{ $t->tahun }}-{{ $t->semester }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="kelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                <select id="kelas"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Kelas</option>
                    @if ($kelas->isEmpty())
                        <option value="">Tidak ada kelas</option>
                    @endif
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="pegawai"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">pegawai</label>
                <select id="pegawai"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Pegawai</option>
                    @if ($pegawai->isEmpty())
                        <option value="">Tidak ada pegawai</option>
                    @endif
                    @foreach ($pegawai as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="mapel" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mata
                    Pelajaran</label>
                <select id="mapel"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach ($mapel as $m)
                        <option value="{{ $m->id }}">{{ $m->mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="hari" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari</label>
                <select id="hari"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Hari</option>
                    @foreach ($hari as $h)
                        <option value="{{ $h }}">{{ $h }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <div class="flex gap-4">
                    <!-- Jam ke -->
                    <div class="flex flex-col w-1/4">
                        <label for="jam" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                            ke:</label>
                        <input type="number" id="jam" placeholder="Jam ke"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required>
                    </div>

                    <!-- Jam Mulai -->
                    <div class="flex flex-col w-1/3">
                        <label for="mulai" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                            Mulai:</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="time" id="mulai"
                                class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                min="09:00" max="18:00" value="00:00" required />
                        </div>
                    </div>

                    <!-- Jam Akhir -->
                    <div class="flex flex-col w-1/3">
                        <label for="akhir" class="mb-1 text-sm font-medium text-gray-900 dark:text-white">Jam
                            Akhir:</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="time" id="akhir"
                                class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                min="09:00" max="18:00" value="00:00" required />
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label for="ket"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                <input type="text" id="ket" placeholder="Keterangan"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>

        <button type="button" onclick="simpan()"
            class="text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Simpan</button>
        <hr class="my-1 border-gray-200 dark:border-gray-700" />

        {{-- Table data  --}}

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div class="pb-4 bg-white dark:bg-gray-900">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="search" name="search"
                        class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for items">
                </div>
            </div>
            <table id="data" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkAll" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkAll" class="sr-only">checkbox</label>
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Action
                        </th>
                        <th scope="col" class="px-3 py-3">
                            No
                        </th>
                        <th scope="col" class="px-3 py-3">
                            KELAS
                        </th>
                        <th scope="col" class="px-3 py-3">
                            HARI
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Mata Peajaran
                        </th>
                        <th scope="col" class="px-3 py-3">
                            JAM
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Mulai
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Akhir
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Guru
                        </th>
                        <th scope="col" class="px-3 py-3">
                            Keterangan
                        </th>

                    </tr>
                </thead>
                <tbody id="jadwalTable">
                    {{-- <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-1" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                            </div>
                        </td>
                        <td class="px-2 py-4">
                            <div class="flex space-x-2">
                                <a type="button" href=""
                                    class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center text-blue-700 rounded-lg hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group">

                                    <svg class="w-5 h-5 text-blue-700 group-hover:text-white dark:text-white"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                    </svg>
                                </a>
                                <button type="submit"
                                    onclick="return confirm('Apakah anda akan menghapus data ini?')"
                                    class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group">
                                    <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            1
                        </td>
                        <td class="px-3 py-4">
                            Senin
                        </td>
                        <th scope="row"
                            class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Matematika
                        </th>
                        <td class="px-3 py-4">
                            1
                        </td>
                        <td class="px-3 py-4">
                            08:00
                        </td>
                        <td class="px-3 py-4">
                            09:00
                        </td>
                        <td class="px-3 py-4">
                            Aktif
                        </td>

                    </tr> --}}

                </tbody>
            </table>

        </div>


    </div>



    <script>
        async function simpan() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const errorBox = document.getElementById('errorBox');

            const data = {
                tahun_id: document.getElementById('tahun').value,
                kelas_id: document.getElementById('kelas').value,
                mapel_id: document.getElementById('mapel').value,
                pegawai_id: document.getElementById('pegawai').value,
                hari: document.getElementById('hari').value,
                jam: document.getElementById('jam').value,
                mulai: document.getElementById('mulai').value,
                akhir: document.getElementById('akhir').value,
                ket: document.getElementById('ket').value,
            };

            // Validasi kosong
            for (let key in data) {
                if (!data[key]) {
                    alert("Semua field harus diisi!");
                    return;
                }
            }

            fetch('/jadwal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) return response.json().then(err => Promise.reject(err));
                    return response.json();
                })
                .then(data => {
                    console.log('Sukses:', data);
                    Livewire.dispatch('showToast', {
                        type: 'success',
                        message: data.message
                    });
                    reset(); // Reset form setelah sukses
                    loadJadwal(); // refresh tabel tanpa reload halaman

                })
                .catch(error => {
                    console.error('Gagal:', error); // Di sinilah kamu akan lihat detail validasi 422
                    Livewire.dispatch('showToast', {
                        type: 'error',
                        message: 'Gagal menyimpan jadwal!'
                    });
                });
        }
        // Fungsi untuk mereset
        function reset() {
            document.getElementById('tahun').value = '';
            document.getElementById('kelas').value = '';
            document.getElementById('mapel').value = '';
            document.getElementById('pegawai').value = '';
            document.getElementById('hari').value = '';
            document.getElementById('jam').value = '';
            document.getElementById('mulai').value = '00:00';
            document.getElementById('akhir').value = '00:00';
            document.getElementById('ket').value = '';
        }


        function loadJadwal() {
            fetch('/jadwal/data', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })

                .then(response => {
                    if (!response.ok) throw new Error("HTTP error " + response.status);
                    return response.json();
                })
                .then(res => {
                    console.log("DEBUG DATA:", res); // ✅ Cek respon di console
                    const tbody = document.getElementById('jadwalTable');
                    tbody.innerHTML = '';
                    (res.data ?? []).forEach((item, index) => {
                        tbody.innerHTML += `
                <tr>
                <td class="w-4 p-4">
                <div class="flex items-center">
                                <input id="checkbox-${item.id}" type="checkbox" name="id[]"
                                    value="${item.id}"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                </div>
                </td> 
                <td class="px-3 py-4">
                <div class="flex space-x-2">
                    <button type="button" onclick="editJadwal(${item.id})"
                        class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center text-blue-700 rounded-lg hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group"
                        title="Edit">
                        <svg class="w-5 h-5 text-blue-700 group-hover:text-white dark:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                        </svg>
                    </button>
                    <button type="button" onclick="hapusJadwal(${item.id})"
                        class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group"
                        title="Hapus">
                        <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                        </svg>
                    </button>
                </div>
                
                </td>
                    <td class="px-3 py-4">${index + 1}</td>
                    <td class="px-3 py-4">${item.kelas?.kelas ?? '-'}</td>
                    <td class="px-3 py-4">${item.hari}</td>
                    <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    ${item.mapel?.mapel ?? '-'}</th>
                    <td class="px-3 py-4">${item.jam}</td>
                    <td class="px-3 py-4">${item.mulai}</td>
                    <td class="px-3 py-4">${item.akhir}</td>
                    <td class="px-3 py-4">${item.pegawai?.name ?? '-'}</td>
                    <td class="px-3 py-4">${item.ket ?? '-'}</td>
                </tr>`;
                    });
                })
                .catch(err => console.error('Gagal load jadwal:', err));
        }

        document.addEventListener('DOMContentLoaded', loadJadwal);



        document.getElementById('checkAll').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="id[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>







</x-layout.layout>
