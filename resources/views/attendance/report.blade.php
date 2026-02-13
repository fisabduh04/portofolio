<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Laparan Bulanan', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <!-- Top Controls -->
            <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
                <!-- Wrapper for Actions and Filters -->
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-between items-center">
                    <!-- Tools Kalkulasi (Left Side) -->
                    <form action="{{ route('attendance.process') }}" method="POST" class="flex space-x-2">
                        @csrf
                        <input type="hidden" name="start_date" value="{{ \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString() }}">
                        <input type="hidden" name="end_date" value="{{ \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString() }}">
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-yellow-600 rounded-base hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 shadow-sm" title="Gunakan ini jika mengubah aturan gaji di tengah bulan.">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Hitung Ulang
                        </button>
                    </form>

                    <!-- Filter (Right Side) -->
                    <form action="{{ route('attendance.report') }}" method="GET" class="flex items-center gap-2">
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

                        <div class="w-32">
                            <select name="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @foreach($months as $key => $val)
                                    <option value="{{ $key }}" {{ $key == $month ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <select name="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @foreach($years as $val)
                                    <option value="{{ $val }}" {{ $val == $year ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Container -->
            <div class="relative overflow-x-auto bg-white dark:bg-gray-800 shadow-xs rounded-base border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">Pegawai</th>
                            <th scope="col" class="px-6 py-3 font-medium">Aturan</th>
                            <th scope="col" class="px-6 py-3 font-medium">Kehadiran</th>
                            <th scope="col" class="px-6 py-3 font-medium">Total Gaji Pokok</th>
                            <th scope="col" class="px-6 py-3 font-medium">Total Uang Makan</th>
                            <th scope="col" class="px-6 py-3 font-medium">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report as $data)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $data->pegawai->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $data->pegawai->attendanceRule->name ?? 'Tanpa Aturan' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">H: {{ $data->hadir_count }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">T: {{ $data->telat_count }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">A: {{ $data->alpha_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($data->total_gaji, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($data->total_makan, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($data->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-body-subtle">Tidak ada data untuk bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.layout>
