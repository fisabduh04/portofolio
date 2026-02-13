<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Presensi', 'href' => '#'],
        ['name' => 'Harian', 'href' => route('absensi.harian.index')],
    ]" />

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-4">
            @foreach($kelas as $k)
             <div class="relative group bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 p-4 transition-all">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3 text-center">{{ $k->kelas }}</h3>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('absensi.harian.create', ['kelas_id' => $k->id, 'type' => 'masuk']) }}" 
                       class="w-full text-center px-3 py-1.5 text-xs font-medium bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200 transition-colors">
                        Absen Masuk
                    </a>
                    <a href="{{ route('absensi.harian.create', ['kelas_id' => $k->id, 'type' => 'pulang']) }}" 
                       class="w-full text-center px-3 py-1.5 text-xs font-medium bg-pink-100 text-pink-700 rounded hover:bg-pink-200 transition-colors">
                        Absen Pulang
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-layout.layout>
