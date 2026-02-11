<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Laparan Bulanan', 'href' => '']
    ]" />

    <div class="px-4 py-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Laporan Gaji & Kehadiran Bulanan</h1>
            
            <form action="{{ route('attendance.report') }}" method="GET" class="flex items-center space-x-2 mt-4 sm:mt-0">
                @php
                    $months = [];
                    for ($i = 1; $i <= 12; $i++) {
                        $months[$i] = \Carbon\Carbon::create()->month($i)->format('F');
                    }
                    
                    $years = [];
                    for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++) {
                        $years[$y] = $y;
                    }
                @endphp

                <div class="w-40">
                    <x-form.select name="month" :options="$months" :selected="$month" />
                </div>
                <div class="w-32">
                    <x-form.select name="year" :options="$years" :selected="$year" />
                </div>
                
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Filter
                </button>
            </form>
        </div>

        <!-- Actions: Re-Calculate / Export -->
        <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tools Kalkulasi</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gunakan ini jika mengubah aturan gaji di tengah bulan.</p>
            </div>
            <form action="{{ route('attendance.process') }}" method="POST" class="flex space-x-2">
                @csrf
                <input type="hidden" name="start_date" value="{{ \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString() }}">
                <input type="hidden" name="end_date" value="{{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString() }}">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    Hitung Ulang Bulan Ini
                </button>
            </form>
        </div>

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Pegawai</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aturan</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kehadiran</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Gaji Pokok</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Uang Makan</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($report as $data)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="p-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $data['pegawai']->name }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $data['pegawai']->attendanceRule->name ?? 'Tanpa Aturan' }}
                                    </td>
                                    <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div>Hadir: {{ $data['hadir_count'] }}</div>
                                        <div class="text-yellow-600">Telat: {{ $data['telat_count'] }}</div>
                                        <div class="text-red-600">Alpha: {{ $data['alpha_count'] }}</div>
                                    </td>
                                    <td class="p-4 text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($data['total_gaji'], 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ number_format($data['total_makan'], 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-sm font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($data['grand_total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data untuk bulan ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.layout>
