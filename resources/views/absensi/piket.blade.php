<x-layout.layout>
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Presensi Piket', 'href' => '#'],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Presensi Kehadiran Piket</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Check-in Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center text-emerald-600 mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Check-in Kedatangan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Catat kehadiran Anda saat mulai bertugas piket hari ini.</p>
            
            <form action="{{ route('absensi.piket.check-in') }}" method="POST" enctype="multipart/form-data" class="w-full">
                @csrf
                <div class="mb-6 w-full text-left">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Foto Bukti (Opsional)</label>
                    <input type="file" name="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-100 rounded-xl">
                </div>
                <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-emerald-500/30">
                    Konfirmasi Check-in
                </button>
            </form>
        </div>

        <!-- Check-out Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-2xl flex items-center justify-center text-rose-600 mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h1a3 3 0 013 3v1"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Check-out Kepulangan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Catat waktu selesai bertugas piket hari ini sebelum meninggalkan lokasi.</p>
            
            <form action="{{ route('absensi.piket.check-out') }}" method="POST" enctype="multipart/form-data" class="w-full">
                @csrf
                <div class="mb-6 w-full text-left">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Foto Bukti (Opsional)</label>
                    <input type="file" name="foto" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-100 rounded-xl">
                </div>
                <button type="submit" class="w-full py-4 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-rose-500/30">
                    Konfirmasi Check-out
                </button>
            </form>
        </div>
    </div>
</div>
</x-layout.layout>
