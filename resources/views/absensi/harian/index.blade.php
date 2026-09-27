<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Presensi', 'href' => '#'],
        ['name' => 'Harian', 'href' => route('absensi.harian.index')],
    ]" />

    <form action="{{ route('absensi.harian.index') }}" method="GET" class="flex flex-wrap items-center gap-3 mt-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <label for="date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal presensi:</label>
        <input type="date" id="date" name="date" value="{{ $date }}" required
            class="rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Tampilkan</button>
        <a href="{{ route('absensi.harian.index') }}" class="text-sm text-blue-600 dark:text-blue-400">Hari ini</a>
    </form>

    @error('date')
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <p class="mt-4 text-sm text-gray-700 dark:text-gray-300">
        Presensi untuk <strong>{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</strong>.
    </p>

    @if (! $isPiket)
        <p class="mt-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800 dark:bg-gray-800 dark:text-blue-400" role="status">
            Anda tidak bertugas piket pada tanggal ini. Pilih tanggal yang sesuai dengan jadwal piket Anda.
        </p>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
            @foreach($kelas as $k)
             <div class="relative group bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 p-4 transition-all">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 text-center">{{ $k->kelas }}</h3>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('absensi.harian.create', ['kelas_id' => $k->id, 'type' => 'masuk', 'date' => $date]) }}"
                       class="w-full text-center px-3 py-1.5 text-xs font-medium bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 transition-colors">
                        Absen Masuk
                    </a>
                    <a href="{{ route('absensi.harian.create', ['kelas_id' => $k->id, 'type' => 'pulang', 'date' => $date]) }}"
                       class="w-full text-center px-3 py-1.5 text-xs font-medium bg-pink-100 text-pink-700 rounded hover:bg-pink-200 transition-colors">
                        Absen Pulang
                    </a>
                </div>
            </div>
            @endforeach
    </div>
</x-layout.layout>
