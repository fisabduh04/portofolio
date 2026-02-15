<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Presensi', 'href' => '#'],
    ]" />

    <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-5">
        <button type="button"
            class="text-white bg-gradient-to-br from-purple-600 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Tambah
            Data
        </button>


        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div class="flex items-center gap-2">
                <label for="entries-per-page" class="text-sm text-gray-700 dark:text-gray-200">Show</label>
                <select name="per_page" id="entries-per-page" name="entries-per-page"
                    class="block w-20 px-2 py-1 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-700 dark:text-gray-200">entries</span>
            </div>
            <div class="pb-4 bg-white dark:bg-gray-900">
                <label for="table-search" class="sr-only">Search</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="table-search"
                        class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search for items">
                </div>
            </div>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="p-4">
                        <div class="flex items-center">
                            <input id="checkbox-all-search" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-all-search" class="sr-only">checkbox</label>
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Nama User
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal
                    </th>

                </tr>
            </thead>
            <tbody id="table-body"></tbody>
        </table>

    </div>


    <script>
        document.getElementById('entries-per-page').addEventListener('change', (event) => {
            const perPage = event.target.value;
            init(perPage); // fetch ulang data sesuai pilihan
        });

        async function getData(perPage = 10) {
            const response = await fetch(`/coba?per_page=${perPage}`);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            return await response.json();
        }

        // getData();
        async function init(perPage = 10) {
            const result = await getData(perPage);
            const users = result.is_all ? result.data : result.data;
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = ''; // Clear existing rows

            users.forEach(user => {
                const row = document.createElement('tr');
                row.className =
                    'bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600';
                row.innerHTML = `
                    <td class="w-4 p-4">
                        <div class="flex items-center">
                            <input id="checkbox-table-search-1" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>
                        </div>
                    </td>
                     <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        ${user.id}
                    </th>
                    <td class="px-6 py-4">
                        <button type="button"
                            class="px-3 py-2 text-xs font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-4 h-4 text-white me-2" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"   
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1                     
                                    1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                            </svg>
                            Edit
                        </button>
                    </td>
                   
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        ${user.name}
                    </th>
                    <td class="px-6 py-4">
                        ${user.email}
                    </td>
                    <td class="px-6 py-4">
                        ${new Date(user.created_at).toLocaleDateString('id-ID')}
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        init();

        // Optional: Add event listeners for pagination, search, etc.
        document.getElementById('entries-per-page').addEventListener('change', (event) => {
            const entriesPerPage = event.target.value;
            // Implement logic to handle entries per page change
            console.log(`Entries per page changed to: ${entriesPerPage}`);
        });
        document.getElementById('table-search').addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            const rows = document.querySelectorAll('#table-body tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td, th');
                const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
                row.style.display = rowText.includes(searchTerm) ? '' : 'none';
            });
        });



        // Optional: Add event listeners for checkbox selection
        document.getElementById('checkbox-all-search').addEventListener('change', (event) => {
            const isChecked = event.target.checked;
            const checkboxes = document.querySelectorAll('#table-body input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
        document.querySelectorAll('#table-body input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const allCheckboxes = document.querySelectorAll('#table-body input[type="checkbox"]');
                const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                document.getElementById('checkbox-all-search').checked = allChecked;
            });
        });
    </script>
</x-layout.layout>
