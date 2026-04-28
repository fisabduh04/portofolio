<x-layout.layout>
    {{-- Toast Notification (will show if session has success/error) --}}
    @if (session('success'))
        <div id="toast-success" class="flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 fixed top-5 right-5 z-50 animate-in slide-in-from-right" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>
                <span class="sr-only">Check icon</span>
            </div>
            <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div id="toast-danger" class="flex items-center w-full max-w-sm p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 fixed top-5 right-5 z-50 animate-in slide-in-from-right" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>
                <span class="sr-only">Error icon</span>
            </div>
            <div class="ml-3 text-sm font-normal">{{ session('error') }}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-danger" aria-label="Close">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    @endif

    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 mb-8">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Manajemen Akun', 'href' => '#'],
            ]" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola akses, aktivasi akun, dan peran (role) pegawai dalam sistem.</p>
        </div>
    </div>

    {{-- Stats Cards (Optional for "Premium" feel) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pegawai</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $pegawais->total() }}</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pegawais->total() }}</div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Akun Aktif</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Online</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{-- Approximate count or just a placeholder if expensive query --}}
                {{ App\Models\User::where('is_active', 1)->count() }}
            </div>
        </div>
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Punya Akun</h3>
                <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-amber-900 dark:text-amber-300">Pending</span>
            </div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ App\Models\Pegawai::doesntHave('user')->count() }}
            </div>
        </div>
    </div>

    <section class="min-h-screen pb-12">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            
            {{-- Toolbar: Search & Info --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 flex flex-col md:flex-row items-center justify-between gap-4">
                <form id="searchForm" method="GET" class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" name="q" value="{{ $q }}" 
                        class="block w-full p-2.5 ps-10 pr-36 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                        placeholder="Cari Nama / Email / NUPTK..." oninput="clearTimeout(this.delay); this.delay = setTimeout(() => { this.form.submit() }, 1000);">
                        
                    {{-- Role Filter --}}
                    <select name="filter_role" onchange="this.form.submit()" class="absolute right-0 top-0 h-full border-l border-gray-300 bg-gray-50 text-gray-900 text-sm rounded-r-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-32">
                        <option value="" class="text-gray-500">Semua Role</option>
                        @foreach(\App\Enums\UserRole::cases() as $roleCase)
                            <option value="{{ $roleCase->value }}" {{ request('filter_role') == $roleCase->value ? 'selected' : '' }}>{{ $roleCase->label() }}</option>
                        @endforeach
                    </select>
                </form>
                
                <div class="flex items-center gap-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1.5 rounded-full shadow-sm">
                        <span class="font-bold text-gray-900 dark:text-white">{{ $pegawais->firstItem() ?? 0 }}-{{ $pegawais->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-900 dark:text-white">{{ $pegawais->total() }}</span>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 dark:bg-gray-700 dark:text-gray-400 border-b dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-4">Pegawai</th>
                            <th scope="col" class="px-6 py-4">Identitas</th>
                            <th scope="col" class="px-6 py-4 text-center">Role System</th>
                            <th scope="col" class="px-6 py-4 text-center">Status Akun</th>
                            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($pegawais as $p)
                            @php
                                $user = $p->user;
                                $hasAccount = (bool) $user;
                                
                                // Generate Initials
                                $names = explode(' ', $p->name ?? 'User');
                                $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                                
                                // Helper to check if row user is "higher" or "equal" to curr auth user
                                $canEdit = false;
                                if ($hasAccount) {
                                    $canEdit = auth()->user()->can('manage', $user);
                                }
                            @endphp
                            <tr class="bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 transition-colors duration-200">
                                {{-- Column: Pegawai --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            @if($p->foto)
                                                 <img class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" src="{{ asset('storage/'.$p->foto) }}" alt="">
                                            @else
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-xs shadow-sm">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $p->email ?? 'Belum ada email' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Column: Identitas --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 w-fit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .883-.393 1.688-1.019 2.227C12.378 9.773 13.921 13 16 13h1c1 1 2.079 3 0 3v0a6.002 6.002 0 00-3.951-1.049A5.996 5.996 0 009 13H8c-2.079 0-3.622-3.227-3.981-4.773C3.393 7.688 3 6.883 3 6v0"/></svg>
                                            NUPTK: {{ $p->nuptk ?? '-' }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">ID: {{ $p->id }}</span>
                                    </div>
                                </td>

                                {{-- Column: Role System --}}
                                <td class="px-6 py-4 text-center">
                                    @if($hasAccount)
                                        <div class="relative inline-block text-left group">
                                            {{-- Role Badge --}}
                                            @php
                                                $roleColors = [
                                                    'kepala' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                                    'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                    'operator' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    'guru' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                    'siswa' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                ];
                                                $colorClass = $roleColors[$user->role] ?? $roleColors['siswa'];
                                            @endphp
                                            
                                            <button type="button" 
                                                @can('updateRole', $user)
                                                    data-dropdown-toggle="roleDropdown-{{ $user->id }}" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $colorClass }} border border-transparent hover:border-current cursor-pointer hover:shadow-sm transition-all"
                                                @else
                                                    disabled
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $colorClass }} border border-transparent opacity-80 cursor-not-allowed transition-all"
                                                @endcan
                                                >
                                                {{ ucfirst($user->role) }}
                                                @can('updateRole', $user)
                                                    <svg class="w-2.5 h-2.5 opacity-60" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                                                @endcan
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum terdaftar</span>
                                    @endif
                                </td>

                                {{-- Column: Status Akun --}}
                                <td class="px-6 py-4 text-center">
                                    @if($hasAccount)
                                        <div class="flex flex-col items-center gap-2">
                                            @can('toggleStatus', $user)
                                                <form action="{{ route('operator.users.toggle-active', $user->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" value="" class="sr-only peer" onchange="this.form.submit()" {{ $user->is_active ? 'checked' : '' }}>
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    </label>
                                                </form>
                                            @else
                                                <div class="opacity-50 cursor-not-allowed" title="Anda tidak memiliki akses untuk mengubah status user ini">
                                                    <label class="relative inline-flex items-center cursor-not-allowed">
                                                        <input type="checkbox" disabled class="sr-only peer" {{ $user->is_active ? 'checked' : '' }}>
                                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    </label>
                                                </div>
                                            @endcan

                                            <span class="text-[10px] font-medium {{ $user->is_active ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400' }}">
                                                {{ $user->is_active ? 'Web Access: ON' : 'Web Access: OFF' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>

                                {{-- Column: Aksi --}}
                                <td class="px-6 py-4 text-center">
                                    @if($hasAccount)
                                        {{-- Resend Reset Link --}}
                                        @if($user->is_active)
                                            @can('manage', $user)
                                                <form action="{{ route('operator.users.resend-reset') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                                    <button type="submit" class="text-gray-500 hover:text-blue-600 transition-colors p-1 rounded-md hover:bg-gray-100" title="Kirim Ulang Reset Password">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 cursor-not-allowed" title="Akses Dibatasi">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                </span>
                                            @endcan
                                        @endif
                                    @else
                                        {{-- Buat Akun Button --}}
                                        <button data-modal-target="createUserModal-{{ $p->id }}" data-modal-toggle="createUserModal-{{ $p->id }}" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-md hover:shadow-lg transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Buat Akun
                                        </button>

                                        {{-- Modal Create --}}
                                        <div id="createUserModal-{{ $p->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative w-full max-w-md max-h-full">
                                                <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="createUserModal-{{ $p->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                                        <span class="sr-only">Close modal</span>
                                                    </button>
                                                    <div class="px-6 py-6 lg:px-8">
                                                        <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                                            <div class="p-2 bg-blue-100 rounded-lg text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                                            </div>
                                                            Buat Akun Baru
                                                        </h3>
                                                        <form class="space-y-4" action="{{ route('operator.users.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="pegawai_id" value="{{ $p->id }}">
                                                            
                                                            <div class="p-3 bg-gray-50 rounded-lg dark:bg-gray-800 border border-gray-100 dark:border-gray-600">
                                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $p->name }}</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">NUPTK: {{ $p->nuptk ?? 'Tidak ada' }}</p>
                                                            </div>

                                                            <div>
                                                                <label for="email-{{ $p->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Login</label>
                                                                <input type="email" name="email" id="email-{{ $p->id }}" value="{{ $p->email }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="name@school.com" required>
                                                            </div>
                                                            <div>
                                                                <label for="role-{{ $p->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role Awal</label>
                                                                <select name="role" id="role-{{ $p->id }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                                                    @foreach(\App\Enums\UserRole::cases() as $roleCase)
                                                                        @if($roleCase->value === 'kepala' && !auth()->user()->isKepala())
                                                                            @continue
                                                                        @endif
                                                                        <option value="{{ $roleCase->value }}">{{ $roleCase->label() }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="flex items-start p-3 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800" role="alert">
                                                                <svg class="flex-shrink-0 inline w-4 h-4 mr-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                                                </svg>
                                                                <span class="sr-only">Info</span>
                                                                <div>
                                                                    <span class="font-medium">Info Penting:</span> Akun akan dibuat dengan status <span class="font-bold">Non-Aktif</span>. Anda harus mengaktifkannya secara manual di tabel untuk memicu pengiriman email set password.
                                                                </div>
                                                            </div>

                                                            <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-md">Simpan & Buat Akun</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg">Tidak ada data pegawai ditemukan.</p>
                                        <p class="text-gray-400 text-sm">Coba ubah kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800">
                {{ $pegawais->links() }}
            </div>
        </div>
    </section>
    {{-- Dropdowns for Role Selection (Placed outside table to prevent clipping) --}}
    @foreach ($pegawais as $p)
        @php $user = $p->user; @endphp
        @if($user && auth()->user()->can('manage', $user))
            <div id="roleDropdown-{{ $user->id }}" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-xl w-44 dark:bg-gray-700">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                    @foreach(\App\Enums\UserRole::cases() as $roleCase)
                        @php 
                            $r = $roleCase->value;
                            if ($r === 'kepala' && !auth()->user()->isKepala()) continue; 
                        @endphp
                        <li>
                            <form action="{{ route('operator.users.update-role', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="role" value="{{ $r }}">
                                <button type="submit" class="block w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white {{ $user->role === $roleCase ? 'bg-gray-50 font-bold' : '' }}">
                                    {{ $roleCase->label() }}
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach

</x-layout.layout>
