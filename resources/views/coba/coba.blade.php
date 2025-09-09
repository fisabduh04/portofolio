<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Data User Uji Coba', 'href' => ''],
    ]" />

    <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-5">
        <div class="p-4 mt-5">
            <h1 class="text-2xl font-bold mb-4">Daftar User</h1>

            <div class="flex flex-wrap sm:flex-nowrap gap-4 mb-4">
                <input type="text" id="search-input" placeholder="Search..."
                    class="border rounded p-2 flex-grow bg-white dark:bg-gray-800 dark:text-white" />

                <select id="per-page-select" class="border rounded p-2 bg-white dark:bg-gray-800 dark:text-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <table class="w-full text-sm text-left border">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="p-2">No</th> <!-- Tambah Kolom No -->
                        <th class="p-2 cursor-pointer sort" data-field="name">Nama</th>
                        <th class="p-2 cursor-pointer sort" data-field="email">Email</th>
                        <th class="p-2 cursor-pointer sort" data-field="created_at">Tanggal</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>

            <div id="pagination" class="flex justify-center mt-4 gap-1"></div>
        </div>
    </div>

    <script>
        const apiUrl = '/coba';
        let state = {
            page: 1,
            perPage: 10,
            search: '',
            sort: 'name',
            order: 'asc',
            editingId: null
        };

        const el = {
            body: document.getElementById('table-body'),
            pagination: document.getElementById('pagination'),
            perPage: document.getElementById('per-page-select'),
            search: document.getElementById('search-input'),
            sortButtons: document.querySelectorAll('.sort'),
        };

        el.perPage.addEventListener('change', () => {
            state.perPage = parseInt(el.perPage.value);
            state.page = 1;
            load();
        });

        let debounceTimer;
        el.search.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state.search = el.search.value.trim();
                state.page = 1;
                load();
            }, 300);
        });

        el.sortButtons.forEach(th => {
            th.addEventListener('click', () => {
                const field = th.dataset.field;
                if (state.sort === field) {
                    state.order = state.order === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sort = field;
                    state.order = 'asc';
                }
                state.page = 1;
                load();
            });
        });

        async function load() {
            const params = new URLSearchParams({
                page: state.page,
                per_page: state.perPage,
                search: state.search,
                sort: state.sort,
                order: state.order,
            });
            const res = await fetch(`${apiUrl}?${params}`);
            if (!res.ok) return alert('Failed to load data');
            const d = await res.json();
            renderTable(d.data);
            renderPagination(d.current_page, d.last_page);
            updateSortIndicators();
        }

        function renderTable(users) {
            const startIndex = (state.page - 1) * state.perPage;
            el.body.innerHTML = users.map((u, index) => {
                const isEditing = state.editingId === u.id;
                return `
        <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
            <td class="p-2 text-center">${startIndex + index + 1}</td>
            <td class="p-2">
                ${isEditing ? `<input type="text" class="w-full p-1 border rounded" value="${u.name}" id="edit-name-${u.id}">` : u.name}
            </td>
            <td class="p-2">
                ${isEditing ? `<input type="email" class="w-full p-1 border rounded" value="${u.email}" id="edit-email-${u.id}">` : u.email}
            </td>
            <td class="p-2">${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
            <td class="p-2 flex gap-2">
                ${isEditing
                    ? `
                                                <button onclick="saveUser(${u.id})" class="text-green-600">Simpan</button>
                                                <button onclick="cancelEdit()" class="text-gray-600">Batal</button>
                                            `
                    : `
                                                <button onclick="editUser(${u.id})" class="text-blue-600">Edit</button>
                                                <button onclick="deleteUser(${u.id})" class="text-red-600">Hapus</button>
                                            `}
            </td>
        </tr>
        `;
            }).join('');
        }


        function renderPagination(current, total) {
            const pages = [];
            if (current > 1) pages.push({
                label: '«',
                p: current - 1
            });
            for (let i = 1; i <= total; i++) pages.push({
                label: i,
                p: i
            });
            if (current < total) pages.push({
                label: '»',
                p: current + 1
            });

            el.pagination.innerHTML = pages.map(pg => `
                <button
                    class="px-3 py-1 rounded border ${pg.p === current ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'}"
                    data-page="${pg.p}">${pg.label}</button>`).join('');

            el.pagination.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', () => {
                    state.page = parseInt(btn.dataset.page);
                    load();
                });
            });
        }

        function updateSortIndicators() {
            el.sortButtons.forEach(th => {
                const f = th.dataset.field;
                th.textContent = f.charAt(0).toUpperCase() + f.slice(1) +
                    (state.sort === f ? (state.order === 'asc' ? ' ↑' : ' ↓') : '');
            });
        }

        function editUser(id) {
            state.editingId = id;
            load();
        }

        function cancelEdit() {
            state.editingId = null;
            load();
        }

        async function saveUser(id) {
            const name = document.getElementById(`edit-name-${id}`).value;
            const email = document.getElementById(`edit-email-${id}`).value;

            const res = await fetch(`/users/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    name,
                    email
                })
            });

            if (res.ok) {
                state.editingId = null;
                load();
            } else {
                alert('Gagal menyimpan data');
            }
        }

        async function deleteUser(id) {
            if (!confirm('Yakin ingin menghapus?')) return;
            const res = await fetch(`/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            });
            if (res.ok) {
                load();
            } else {
                alert('Gagal menghapus data');
            }
        }

        // Load pertama kali
        load();
    </script>
</x-layout.layout>
