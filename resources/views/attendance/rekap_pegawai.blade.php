<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Rekap Pegawai', 'href' => '']
    ]" />

    <x-layout.sidebar.sub-menu :items="[
        ['name' => 'Dashboard', 'href' => route('attendance.index')],
        ['name' => 'Rekap Per Pegawai', 'href' => route('attendance.rekap-pegawai'), 'active' => true],
        ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
        ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
        ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
    ]" />

    <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Rekap Absensi Per Pegawai</h2>

            {{-- Filter Form --}}
            <form action="{{ route('attendance.rekap-pegawai') }}" method="GET" class="flex flex-col md:flex-row gap-4 mb-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pegawai</label>
                    <select name="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}" {{ $pegawaiId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bulan</label>
                    <select name="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translate('month_name') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun</label>
                    <select name="year" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        @for($y=date('Y'); $y>=2020; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Tampilkan
                    </button>
                </div>
            </form>

            {{-- Table --}}
            @if($selectedPegawai)
                <div class="relative overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Tanggal</th>
                                <th scope="col" class="px-6 py-3">Jam Masuk</th>
                                <th scope="col" class="px-6 py-3">Jam Pulang</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Sumber</th>
                                <th scope="col" class="px-6 py-3">Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceData as $row)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d F Y') }}
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l') }}</div>
                                    </td>
                                    <td class="px-6 py-4">{{ $row->jam_masuk ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $row->jam_pulang ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded text-xs font-medium 
                                            @if($row->status == 'Hadir') bg-green-100 text-green-800 
                                            @elseif($row->status == 'Telat') bg-yellow-100 text-yellow-800 
                                            @elseif($row->status == 'Alpha') bg-red-100 text-red-800 
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $row->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($row->attendance_source == 'Fingerprint')
                                            <span class="text-green-600 font-semibold text-xs">FINGERPRINT</span>
                                        @elseif($row->attendance_source == 'Manual')
                                            <span class="text-yellow-600 font-semibold text-xs">MANUAL</span>
                                        @elseif($row->attendance_source == 'Event')
                                            <span class="text-purple-600 font-semibold text-xs">EVENT</span>
                                        @else
                                            <span class="text-gray-500 text-xs">{{ $row->attendance_source ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono">{{ $row->durasi_kerja ?? '00:00:00' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center">Tidak ada data absensi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-right">TOTAL KEHADIRAN:</td>
                                <td class="px-6 py-3 text-blue-600">{{ $stats['total_hadir'] }} Hari</td>
                                <td class="px-6 py-3 text-right">TOTAL JAM:</td>
                                <td class="px-6 py-3 text-blue-600">{{ $stats['total_durasi_formatted'] }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-sm text-blue-800 dark:text-blue-300">
                    <p><strong>Info:</strong> Data ini tersinkronisasi otomatis dengan Laporan Gaji & Payroll.</p>
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    Silakan pilih pegawai untuk melihat rekap.
                </div>
            @endif
        </div>
    </div>
</x-layout.layout>
