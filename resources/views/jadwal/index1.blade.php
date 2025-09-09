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


        <div class="grid gap-6 mb-6 md:grid-cols-2" id="formJadwal">
            <div>
                <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun
                    Ajaran</label>
                <select id="tahun"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Tahun Ajaran</option>
                    <option value="">Tidak ada tahun ajaran</option>
                    <option value="">Tahun Semester</option>

                </select>
            </div>

            <div>
                <label for="kelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                <select id="kelas"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Pilih Kelas</option>
                    <option value="">Tidak ada kelas</option>
                    <option value="">Kelas</option>
                </select>
            </div>
        </div>

        <hr class="my-1 border-gray-200 dark:border-gray-700" />

        {{-- Table data  --}}

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <div
                class="pb-4 bg-white dark:bg-gray-900 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                <div class="flex items-center space-x-2 order-2 md:order-1">
                    <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">Show</label>
                    <select id="perPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-20 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700 dark:text-gray-300">data</span>
                </div>
                <div class="flex items-center order-1 md:order-2">
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative mt-1 w-full md:w-auto">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="text" id="search" name="search"
                            class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for items">
                    </div>
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
                            HARI
                        </th>
                        <th scope="col" class="px-3 py-3">
                            MATA PELAJARAN
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

                </tbody>
            </table>

        </div>


    </div>



    <script>
        function tambah() {
            var table = document.getElementById('jadwalTable');
            var row = table.insertRow(0);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            var cell8 = row.insertCell(7);
            var cell9 = row.insertCell(8);
            var cell10 = row.insertCell(9);

            // Cell 1: Checkbox
            cell1.innerHTML = `<td class="p-4 w-4">
                            <div class="flex items-center">
                                <input id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                            </div>
                        </td>`;
            // Cell 2: Actions (Edit/Delete buttons)
            cell2.innerHTML = `<td class="px-3 py-3 w-24">
                            <div class="flex space-x-1">
                                <button type="button" class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center text-blue-700 rounded-lg hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 group" title="Edit">
                                    <svg class="w-5 h-5 text-blue-700 group-hover:text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                    </svg>
                                </button>
                                <button type="button" class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline-none focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group" title="Hapus">
                                    <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                            </div>
                        </td>`;
            // Cell 3: No
            cell3.innerHTML = `<td class="px-3 py-3 text-center align-middle font-bold w-16">12</td>`;
            // Cell 4: Hari (Select)
            cell4.innerHTML = `<td class="px-3 py-3 w-32">
                            <select name="kelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Hari</option>
                                <option value="senin">senin</option>
                                <option value="selasa">selasa</option>
                                <option value="rabu">rabu</option>
                                <option value="kamis">kamis</option>
                                <option value="jumat">jumat</option>
                                <option value="sabtu">sabtu</option>
                                <option value="ahad">ahad</option>
                            </select>
                        </td>`;
            // Cell 5: Mapel (Select)
            cell5.innerHTML = `<td class="px-3 py-3 w-40">
                            <select name="mapel" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Mapel</option>
                                <option value="Matematika">Matematika</option>
                                <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                                <option value="Bahasa Inggris">Bahasa Inggris</option>
                                <option value="Fisika">Fisika</option>
                                <option value="Kimia">Kimia</option>
                                <option value="Biologi">Biologi</option>
                                <option value="Ekonomi">Ekonomi</option>
                                <option value="Geografi">Geografi</option>
                                <option value="Sejarah">Sejarah</option>
                            </select>
                        </td>`;
            // Cell 6: Jam (Input number)
            cell6.innerHTML = `<td class="px-3 py-3 w-12">
                            <input type="number" name="jam" value=""
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                data-tooltip-target="jam-tooltip" />
                            <div id="jam-tooltip" role="tooltip" class="absolute z-10 invisible inline-block px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip dark:bg-gray-700">
                                Masukkan jumlah jam pelajaran
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </td>`;
            // Cell 7: Mulai (Input time)
            cell7.innerHTML = `<td class="px-3 py-3 w-28">
                            <input type="time" name="mulai" value="07:00"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                data-tooltip-target="mulai-tooltip" />
                            <div id="mulai-tooltip" role="tooltip" class="absolute z-10 invisible inline-block px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip dark:bg-gray-700">
                                Waktu mulai pelajaran
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </td>`;
            // Cell 8: Akhir (Input time)
            cell8.innerHTML = `<td class="px-3 py-3 w-28">
                            <input type="time" name="akhir" value="08:00"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                data-tooltip-target="akhir-tooltip" />
                            <div id="akhir-tooltip" role="tooltip" class="absolute z-10 invisible inline-block px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip dark:bg-gray-700">
                                Waktu akhir pelajaran
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </td>`;
            // Cell 9: Guru (Select)
            cell9.innerHTML = `<td class="px-3 py-3 w-32">
                            <select name="guru" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Guru</option>
                                <option value="guru1">Guru 1</option>
                                <option value="guru2">Guru 2</option>
                                <option value="guru3">Guru 3</option>
                                <option value="guru4">Guru 4</option>
                            </select>
                        </td>`;
            // Cell 10: Keterangan (Input text)
            cell10.innerHTML = `<td class="px-3 py-3 w-48">
                            <input type="text" name="ket" placeholder="Keterangan"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        </td>`;
        }
    </script>


    {{-- <script>
        document.getElementById('batalBtn').addEventListener('click', function() {
            reset(); // Reset form saat batal   
            document.getElementById('simpanBtn').classList.add('hidden');
            document.getElementById('batalBtn').classList.add('hidden');
            document.getElementById('formJadwal').classList.add('hidden');
            document.getElementById('errorBox').classList.add('hidden'); // Sembunyikan errorBox
            reset(); // Reset form saat batal
        });


        function muncul() {
            reset(); // Reset form sebelum menampilkan
            document.getElementById('simpanBtn').classList.remove('hidden');
            document.getElementById('batalBtn').classList.remove('hidden');
            document.getElementById('formJadwal').classList.remove('hidden');
            document.getElementById('errorBox').classList.add('hidden'); // Sembunyikan errorBox
        }




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
    </script> --}}







</x-layout.layout>
