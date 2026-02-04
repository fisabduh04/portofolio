<x-layout.layout>
    <div class="container mx-auto px-4 py-8">
        <x-breadcrumb :breadcrumbs="[
            ['name' => 'Home', 'href' => route('dashboard.index')],
            ['name' => 'Presensi Piket', 'href' => '#'],
        ]" />

        <div class="mt-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Presensi Siswa & Piket</h1>
            <p class="text-gray-500 dark:text-gray-400">Kelola kehadiran siswa, input data terlambat, dan pantau aktivitas kelas hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card 1: Input Absensi Kelas -->
            <a href="{{ route('jadwal.index', ['filter_hari' => \Carbon\Carbon::now()->isoFormat('dddd')]) }}" class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl group-hover:bg-primary-100 transition-colors">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Absensi Kelas</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Input kehadiran siswa (Hadir, Sakit, Izin, Alpha) untuk kelas yang sedang berlangsung.</p>
                <span class="inline-flex items-center text-sm font-semibold text-primary-600">
                    Buka Jadwal Hari Ini <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </span>
            </a>

            <!-- Card 2: Monitoring / Rekap Harian -->
            <a href="{{ route('absensi.rekap-harian') }}" class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Monitor Kehadiran</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Lihat rekapitulasi kehadiran siswa secara real-time hari ini.</p>
                <span class="inline-flex items-center text-sm font-semibold text-emerald-600">
                    Lihat Laporan Harian <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </span>
            </a>

            <!-- Card 3: Siswa Terlambat (Placeholder / Future Feature) -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 opacity-75">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-24 h-24 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Siswa Terlambat</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Fitur pencatatan siswa terlambat & izin keluar (Segera Hadir).</p>
                <span class="inline-flex items-center text-xs font-semibold bg-gray-100 text-gray-500 px-2 py-1 rounded">
                    Coming Soon
                </span>
            </div>
        </div>
    </div>
</x-layout.layout>
