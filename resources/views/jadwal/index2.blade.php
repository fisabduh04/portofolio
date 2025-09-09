<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jadwal', 'href' => 'jadwal'],
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Jadwal Mata Pelajaran</h1>

        <div class="flex flex-col space-y-2 md:flex-row md:space-x-4 md:space-y-0">
            <x-btn text="Tambah" icon="plus" onclick="tambahBaris()" />
            <x-btn text="Hapus" icon="trash" color="red" type="button" onclick="hapusTerpilih()" />
        </div>

        <hr class="my-1 border-gray-200 dark:border-gray-700" />

        <form action="{{ route('jadwal.updateAll') }}" method="POST">
            @csrf

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="pb-4 bg-white dark:bg-gray-900">
                    <label for="table-search" class="sr-only">Search</label>
                    <div class="relative mt-1">
                        <div
                            class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
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

                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                            <th scope="col" class="px-10 py-3">
                                KELAS
                            </th>
                            <th scope="col" class="px-10 py-3">
                                HARI
                            </th>
                            <th scope="col" class="px-15 py-3">
                                MATA PELAJARAN
                            </th>
                            <th scope="col" class="px-15 py-3">
                                Guru
                            </th>
                            <th scope="col" class="px-4 py-3">
                                JAM
                            </th>
                            <th scope="col" class="px-4 py-3">
                                Mulai
                            </th>
                            <th scope="col" class="px-4 py-3">
                                Akhir
                            </th>
                            <th scope="col" class="px-10 py-3">
                                Keterangan
                            </th>
                        </tr>
                    </thead>
                    <tbody id="jadwalBody">
                        @foreach ($jadwals as $index => $j)
                            <input type="hidden" name="tahun_id" value="{{ $j->tahun_id ?? '' }}">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="w-4 p-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="ids[]" value="{{ $j->id }}"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label class="sr-only">checkbox</label>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex space-x-2">
                                        <button type="button" onclick="hapusBaris(this)"
                                            class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group">
                                            <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-2 py-4 text-center">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">
                                    <select name="kelas_id[{{ $j->id }}]" class="w-full border rounded p-1">
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $j->kelas_id == $k->id ? 'selected' : '' }}>
                                                {{ $k->kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <select name="hari[{{ $j->id }}]" class="w-full border rounded p-1">
                                        @foreach ($hari as $h)
                                            <option value="{{ $h }}"
                                                {{ $j->hari == $h ? 'selected' : '' }}>
                                                {{ $h }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <select name="mapel_id[{{ $j->id }}]" class="w-full border rounded p-1">
                                        @foreach ($mapel as $m)
                                            <option value="{{ $m->id }}"
                                                {{ $j->mapel_id == $m->id ? 'selected' : '' }}>
                                                {{ $m->mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <select name="pegawai_id[{{ $j->id }}]" class="w-full border rounded p-1">
                                        @foreach ($pegawai as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $j->pegawai_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="jam[{{ $j->id }}]"
                                        value="{{ $j->jam }}" class="w-full border rounded p-1">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="time" name="mulai[{{ $j->id }}]"
                                        value="{{ $j->mulai }}" class="w-full border rounded p-1">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="time" name="akhir[{{ $j->id }}]"
                                        value="{{ $j->akhir }}" class="w-full border rounded p-1">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" name="ket[{{ $j->id }}]"
                                        value="{{ $j->ket }}" class="w-full border rounded p-1">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="button" onclick="tambahBaris()"
                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                    + Tambah Baris
                </button>
                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                    Simpan Semua
                </button>
            </div>
        </form>
    </div>

    <script>
        // Fungsi untuk menambah baris baru
        function tambahBaris() {
            const tbody = document.getElementById('jadwalBody');
            const newIndex = tbody.children.length;

            const row = document.createElement('tr');
            row.classList.add('bg-white', 'border-b', 'dark:bg-gray-800', 'dark:border-gray-700');

            row.innerHTML = `
                <td class="w-4 p-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="new_ids[]"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label class="sr-only">checkbox</label>
                    </div>
                </td>
                <td class="px-3 py-4">
                    <div class="flex space-x-2">
                        <button type="button" onclick="hapusBaris(this)"
                            class="inline-flex items-center justify-center px-2 py-2 text-sm font-medium text-center rounded-lg text-rose-700 hover:text-white hover:bg-rose-800 focus:ring-4 focus:outline focus:ring-rose-300 dark:bg-rose-600 dark:hover:bg-rose-700 dark:focus:ring-rose-800 group">
                            <svg class="w-5 h-5 text-rose-500 group-hover:text-white dark:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                            </svg>
                        </button>
                    </div>
                </td>
                <td class="px-2 py-4 text-center">${newIndex + 1}</td>
                <td class="px-3 py-2">
                    <select name="new_kelas_id[]" class="w-full border rounded p-1">
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <select name="new_hari[]" class="w-full border rounded p-1">
                        @foreach ($hari as $h)
                            <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <select name="new_mapel_id[]" class="w-full border rounded p-1">
                        @foreach ($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->mapel }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <select name="new_pegawai_id[]" class="w-full border rounded p-1">
                        @foreach ($pegawai as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2">
                    <input type="number" name="new_jam[]" class="w-full border rounded p-1">
                </td>
                <td class="px-3 py-2">
                    <input type="time" name="new_mulai[]" class="w-full border rounded p-1">
                </td>
                <td class="px-3 py-2">
                    <input type="time" name="new_akhir[]" class="w-full border rounded p-1">
                </td>
                <td class="px-3 py-2">
                    <input type="text" name="new_ket[]" class="w-full border rounded p-1">
                </td>
            `;

            tbody.appendChild(row);
        }

        // Fungsi untuk menghapus baris
        function hapusBaris(button) {
            const row = button.closest('tr');
            row.remove();

            // Perbarui nomor urut setelah penghapusan
            const tbody = document.getElementById('jadwalBody');
            const rows = tbody.querySelectorAll('tr');

            rows.forEach((row, index) => {
                const noCell = row.querySelector('td:nth-child(3)');
                if (noCell) {
                    noCell.textContent = index + 1;
                }
            });
        }

        // Fungsi untuk menghapus data terpilih
        function hapusTerpilih() {
            const selected = [];
            const checkboxes = document.querySelectorAll('input[name="ids[]"]:checked');

            checkboxes.forEach(checkbox => {
                selected.push(checkbox.value);
            });

            if (selected.length === 0) {
                alert('Pilih setidaknya satu data untuk dihapus');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus data terpilih?')) {
                // Submit form untuk penghapusan
                // Di sini Anda perlu mengimplementasikan logika penghapusan
                // Contoh: menggunakan AJAX atau form submit terpisah
                console.log('Data yang akan dihapus:', selected);
                alert('Fungsi hapus belum diimplementasikan');
            }
        }

        // Fungsi untuk memilih semua checkbox
        document.getElementById('checkAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</x-layout.layout>
