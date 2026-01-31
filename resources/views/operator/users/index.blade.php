<x-layout.layout>
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 mb-8">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('home')],
                ['name' => 'Operator', 'href' => '#'],
                ['name' => 'Manajemen User', 'href' => route('operator.users.index')],
            ]" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-1">Kelola akses pegawai ke sistem. Buat akun login dan atur role pengguna.</p>
        </div>
        <div class="flex items-center gap-2">
            <x-btn text="Export Data" icon="download" color="gray" variant="outline" size="sm" />
        </div>
    </div>

    <section class="min-h-screen pb-12">
        <div class="max-w-7xl mx-auto">
            {{-- Table Card --}}
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                {{-- Toolbar --}}
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20">
                    <form id="pegawaiSearchForm" method="GET" class="flex flex-col md:flex-row items-center gap-4"
                        data-debounce="500">
                        <div class="relative w-full md:max-w-md">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="pegawai-search" name="q" value="{{ $q }}"
                                class="block w-full ps-11 pr-4 py-3 text-sm text-gray-900 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white transition-all duration-300 shadow-sm placeholder:text-gray-400"
                                placeholder="Cari berdasarkan nama, email, atau NUPTK..." autocomplete="off">
                        </div>

                        <div
                            class="flex items-center gap-2 text-xs font-medium text-gray-400 uppercase tracking-widest ml-auto">
                            Menampilkan {{ $pegawais->count() }} Pegawai
                        </div>
                    </form>
                </div>

                {{-- Table content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead
                            class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th scope="col" class="px-8 py-4 font-medium">Pegawai</th>
                                <th scope="col" class="px-6 py-4 font-medium">NUPTK</th>
                                {{-- <th scope="col" class="px-6 py-4 font-medium">Status Kerja</th> --}}
                                <th scope="col" class="px-6 py-4 font-medium">Posisi (role)</th>
                                <th scope="col" class="px-8 py-4 font-medium text-center">Pengaturan Akun</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse ($pegawais as $p)
                                @php
                                    $sudahPunyaAkun = (bool) $p->user;

                                    $initials = collect(explode(' ', trim((string) ($p->name ?? 'P'))))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => mb_substr((string) $w, 0, 1))
                                        ->implode(''); // <- ini yang benar, hasilnya string

                                    $initials = $initials !== '' ? $initials : 'P';

                                    $aktif = strtolower((string) ($p->aktif ?? ''));
                                    $isOnline = in_array($aktif, ['aktif', 'active', '1', 'ya', 'yes']);
                                @endphp
                                <tr
                                    class="group hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition-all duration-300">
                                    <td class="px-8 py-5 text-gray-900 dark:text-white whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="relative inline-flex">
                                                @if ($p->foto)
                                                    <img src="{{ asset('storage/' . $p->foto) }}"
                                                        alt="{{ $p->name }}"
                                                        class="w-10 h-10 rounded-full object-cover shadow-sm ring-2 ring-white dark:ring-gray-900">
                                                @else
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center font-semibold text-white shadow-sm ring-2 ring-white dark:ring-gray-900">
                                                        {{ strtoupper($initials) }}
                                                    </div>
                                                @endif

                                                {{-- Status Kerja (Top Right) --}}
                                                <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 {{ $isOnline ? 'bg-green-500' : 'bg-gray-300' }}"
                                                    title="Status Kerja: {{ $isOnline ? 'Aktif' : 'Non-Aktif' }}">
                                                    @if ($isOnline)
                                                        <span
                                                            class="absolute inset-0 rounded-full bg-green-500 animate-ping opacity-75"></span>
                                                    @endif
                                                </div>

                                                {{-- Status Akun (Bottom Right) --}}
                                                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-gray-900 {{ $sudahPunyaAkun ? 'bg-emerald-500' : 'bg-amber-500' }}"
                                                    title="{{ $sudahPunyaAkun ? 'Sudah Terdaftar' : 'Belum Memiliki Akun' }}">
                                                    @if (!$sudahPunyaAkun)
                                                        <span
                                                            class="absolute inset-0 rounded-full bg-amber-500 animate-pulse opacity-75"></span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                                    {{ $p->name ?? '-' }}
                                                    <span
                                                        class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-[10px] font-medium text-gray-400">ID:
                                                        {{ $p->nuptk }}</span>
                                                </div>
                                                <div class="text-xs font-normal text-gray-400 mt-0.5">
                                                    {{ $p->email ?? 'Email belum diatur' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div
                                            class="font-mono text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-lg inline-block">
                                            {{ $p->nuptk ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- <td class="px-6 py-5">
                                        @if ($isOnline)
                                            <x-badge text="Aktif" color="green" variant="subtle" />
                                        @else
                                            <x-badge text="{{ $p->aktif ?: 'Non-Aktif' }}" color="gray"
                                                variant="subtle" />
                                        @endif
                                    </td> --}}
                                    <td class="px-6 py-3">
                                        @if ($p->user)
                                            <form method="POST"
                                                action="{{ route('operator.users.update-role', $p->user) }}">
                                                @csrf
                                                @method('PATCH')

                                                <select name="role" onchange="this.form.submit()"
                                                    class="text-xs rounded-lg border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800
                       focus:ring-blue-500 focus:border-blue-500">
                                                    @foreach (['guru', 'siswa', 'kepala', 'operator', 'admin'] as $role)
                                                        <option value="{{ $role }}"
                                                            @selected($p->user->role === $role)>
                                                            {{ ucfirst($role) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>


                                    <td class="px-8 py-5 text-center">
                                        @if ($sudahPunyaAkun)
                                            <div class="inline-flex items-center justify-end gap-3">

                                                {{-- Status badge --}}
                                                <span
                                                    class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $p->user->is_active
                                                        ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300'
                                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                                                    {{ $p->user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>

                                                {{-- Toggle aktif/nonaktif (switch) --}}
                                                <form method="POST"
                                                    action="{{ route('operator.users.toggle-active', $p->user) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit"
                                                        class="group relative inline-flex h-7 w-12 items-center rounded-full transition
                    {{ $p->user->is_active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-700' }}"
                                                        title="{{ $p->user->is_active ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}">
                                                        <span
                                                            class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition
                        {{ $p->user->is_active ? 'translate-x-6' : 'translate-x-1' }}">
                                                        </span>
                                                    </button>
                                                </form>

                                                {{-- Kirim ulang reset (button kecil outline) --}}
                                                @if ($p->user->is_active)
                                                    <form method="POST"
                                                        action="{{ route('operator.users.resend-reset') }}">
                                                        @csrf
                                                        <input type="hidden" name="email"
                                                            value="{{ $p->user->email }}">
                                                        <button type="submit" class="...">Reset</button>
                                                    </form>
                                                @else
                                                    <button type="button" class="... opacity-50 cursor-not-allowed"
                                                        disabled title="Aktifkan akun dulu">
                                                        Reset
                                                    </button>
                                                @endif


                                            </div>
                                        @else
                                            <x-btn text="Buat Akun" icon="plus" color="blue" size="sm"
                                                data-modal-target="create-user-modal"
                                                data-modal-toggle="create-user-modal"
                                                data-pegawai-id="{{ $p->id }}"
                                                data-pegawai-name="{{ $p->name }}"
                                                data-pegawai-email="{{ $p->email }}"
                                                data-pegawai-foto="{{ $p->foto ? asset('storage/' . $p->foto) : '' }}" />
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-10 h-10 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-gray-900 dark:text-white font-semibold">Pegawai Tidak
                                                Ditemukan</h3>
                                            <p class="text-gray-500 text-sm mt-1">Coba gunakan kata kunci pencarian
                                                yang berbeda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-6 bg-gray-50/30 dark:bg-gray-800/20 border-t border-gray-50 dark:border-gray-800">
                    {{ $pegawais->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- CREATE USER MODAL --}}
    <div id="create-user-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/20">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div
                class="relative bg-white dark:bg-gray-900 rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-800 animate-in zoom-in-95 duration-300">

                {{-- Decorative top bar --}}
                <div class="h-2 bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600"></div>

                <div class="p-8">
                    {{-- Modal header --}}
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Buat Akun User
                            </h3>
                            <p
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">
                                Akses Operator Sistem</p>
                        </div>
                        <button type="button"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-white bg-gray-50 dark:bg-gray-800 rounded-xl p-2 transition-colors"
                            data-modal-hide="create-user-modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal body --}}
                    <form class="space-y-6" method="POST" action="{{ route('operator.users.store') }}">
                        @csrf
                        <input type="hidden" name="pegawai_id" id="modal_pegawai_id" value="">

                        <div
                            class="bg-blue-50/50 dark:bg-blue-900/10 p-5 rounded-3xl border border-blue-100/50 dark:border-blue-800/30 flex items-center gap-4">
                            <div id="modal_avatar_container" class="relative">
                                <div id="modal_avatar"
                                    class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-semibold text-lg shadow-lg shadow-blue-600/20 overflow-hidden">
                                    P
                                </div>
                                <img id="modal_avatar_img" src=""
                                    class="hidden w-12 h-12 rounded-2xl object-cover shadow-lg shadow-blue-600/20">
                            </div>
                            <div>
                                <div
                                    class="text-[10px] font-semibold text-blue-600/60 uppercase tracking-tighter mb-0.5">
                                    Pegawai Terpilih</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate"
                                    id="modal_pegawai_label">-</div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <x-form.input type="email" name="email" id="modal_email" label="Alamat Email Login"
                                placeholder="nama@sekolah.sch.id" required
                                helper="Email ini akan digunakan untuk login sistem." />
                        </div>

                        <div class="space-y-2">
                            <x-form.select label="Role" name="role" id="modal_role" :required="true"
                                placeholder="Pilih role..." :options="[
                                    'guru' => 'Guru',
                                    'siswa' => 'Siswa',
                                    'kepala' => 'Kepala',
                                    'operator' => 'Operator',
                                    'admin' => 'Admin',
                                ]" selected="guru" />
                        </div>

                        <div
                            class="p-4 rounded-2xl bg-amber-50/50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs leading-relaxed text-amber-800 dark:text-amber-400 font-medium">
                                    “Setelah akun dibuat, operator perlu mengaktifkan akun untuk mengirim link atur kata
                                    sandi.”
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <x-btn type="submit" text="Buat Akun Sekarang" icon="plus" color="blue"
                                variant="solid" shadow="md" />
                            <x-btn type="button" text="Batalkan" color="gray" variant="ghost"
                                data-modal-hide="create-user-modal" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-modal-target="create-user-modal"]');
            if (!btn) return;

            const id = btn.getAttribute('data-pegawai-id') || '';
            const name = btn.getAttribute('data-pegawai-name') || '-';
            const email = btn.getAttribute('data-pegawai-email') || '';
            const foto = btn.getAttribute('data-pegawai-foto') || '';

            document.getElementById('modal_pegawai_id').value = id;
            document.getElementById('modal_pegawai_label').textContent = name;
            document.getElementById('modal_email').value = email;

            const initials = name.trim().split(' ').filter(Boolean).slice(0, 2).map(w => w[0]).join('')
                .toUpperCase();
            const avatarDiv = document.getElementById('modal_avatar');
            const avatarImg = document.getElementById('modal_avatar_img');

            if (foto) {
                avatarDiv.classList.add('hidden');
                avatarImg.src = foto;
                avatarImg.classList.remove('hidden');
            } else {
                avatarImg.classList.add('hidden');
                avatarDiv.textContent = initials || 'P';
                avatarDiv.classList.remove('hidden');
            }
        });

        // Debounce search
        (function() {
            const form = document.getElementById('pegawaiSearchForm');
            const input = document.getElementById('pegawai-search');
            if (!form || !input) return;

            let t = null;
            let lastValue = input.value;

            input.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(() => {
                    const value = input.value.trim();
                    if (value === lastValue) return;
                    lastValue = value;
                    form.submit();
                }, 500);
            });
        })();
    </script>
</x-layout.layout>
