<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Payroll', 'href' => '']
    ]" />

    <div class="px-4 py-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Payroll & Slip Gaji</h1>
            
            <form action="{{ route('attendance.payroll.index') }}" method="GET" class="flex items-center space-x-2 mt-4 sm:mt-0">
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

        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Pegawai</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kehadiran</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Terima (THP)</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                @forelse ($pegawais as $data)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="p-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $data['pegawai']->name }}
                                        <div class="text-xs text-gray-500">{{ $data['pegawai']->attendanceRule->name ?? 'No Rule' }}</div>
                                    </td>
                                    <td class="p-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">H: {{ $data['hadir_count'] }}</span>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">T: {{ $data['telat_count'] }}</span>
                                        <span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">A: {{ $data['alpha_count'] }}</span>
                                    </td>
                                    <td class="p-4 text-base font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($data['grand_total'], 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 space-x-2 whitespace-nowrap">
                                        <a href="{{ route('attendance.payroll.slip', ['id' => $data['pegawai']->id, 'month' => $month, 'year' => $year]) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-indigo-700 hover:bg-indigo-800 focus:ring-4 focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            Cetak Slip
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-500 dark:text-gray-400">Tidak ada pegawai aktif.</td>
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
