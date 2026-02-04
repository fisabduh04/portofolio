<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => ''],
        ['name' => 'Presensi Harian', 'href' => route('jadwal.presensiHarian')],
    ]" />

    <div class="mt-8">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">
                    Presensi Harian
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Kelola kehadiran siswa untuk jadwal hari ini.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                {{-- View Toggle Tabs (Only for Admin/Operator or Guru Piket) --}}
                @if(auth()->user()->role != 'guru' || (isset($isPiket) && $isPiket))
                <div class="bg-gray-100 dark:bg-gray-800 p-1 rounded-lg flex shadow-inner">
                    <a href="{{ route('jadwal.presensiHarian', ['date' => $date, 'view_mode' => 'all']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $viewMode == 'all' ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-700 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Semua Jadwal
                    </a>
                    <a href="{{ route('jadwal.presensiHarian', ['date' => $date, 'view_mode' => 'mine']) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ $viewMode == 'mine' ? 'bg-white text-blue-600 shadow-sm dark:bg-gray-700 dark:text-blue-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Jadwal Saya
                    </a>
                </div>
                @endif

                {{-- Date Filter Form --}}
                <form action="{{ route('jadwal.presensiHarian') }}" method="GET" class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 flex-1 md:flex-none">
                    <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                    <label for="date" class="text-sm font-medium text-gray-700 dark:text-gray-300 pl-2">Tanggal:</label>
                    <div class="relative flex-1 md:w-auto">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                               <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input type="date" id="date" name="date" value="{{ $date }}" onchange="this.form.submit()" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Alert --}}
        <div class="flex items-center p-4 mb-6 text-sm text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400 dark:border-blue-800" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                Menampilkan jadwal untuk hari <span class="font-bold">{{ $dayName }}</span>, tanggal <span class="font-bold">{{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }}</span>.
                @if(auth()->user()->role == 'guru')
                    @if(isset($isPiket) && $isPiket)
                        <span class="font-bold text-indigo-700 dark:text-indigo-400">[MODE GURU PIKET]</span> Anda melihat seluruh jadwal sekolah. <br class="hidden sm:inline">
                        <span class="text-xs font-normal text-gray-600 dark:text-gray-400 mt-1 block sm:inline">
                            Jadwal mengajar Anda ditandai dengan label <span class="px-1.5 py-0.5 text-[10px] font-bold text-indigo-700 bg-indigo-100 rounded border border-indigo-200">KELAS SAYA</span> dan border biru.
                        </span>
                    @else
                        Anda melihat jadwal mengajar Anda sendiri.
                    @endif
                @else
                    <span class="font-bold text-blue-700 dark:text-blue-400">[MODE ADMINISTRATOR]</span> Anda memiliki hak akses penuh untuk membuat, mengedit, dan melihat presensi seluruh guru/kelas.
                @endif
            </div>
        </div>

        {{-- Table Card --}}
        <div class="relative overflow-hidden bg-white dark:bg-gray-900 shadow-md rounded-xl border border-gray-200 dark:border-gray-800">
            @if($jadwals->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-4">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Tidak Ada Jadwal Mengajar</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md">
                        Tidak ditemukan jadwal pelajaran aktif pada hari <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $dayName }}</span> untuk tanggal ini.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 rounded-tl-lg">Jam / Waktu</th>
                                <th scope="col" class="px-6 py-4">Kelas</th>
                                <th scope="col" class="px-6 py-4">Mata Pelajaran</th>
                                @if(auth()->user()->role != 'guru' || (isset($isPiket) && $isPiket))
                                    <th scope="col" class="px-6 py-4">Guru Pengajar</th>
                                @endif
                                <th scope="col" class="px-6 py-4">Status Presensi</th>
                                <th scope="col" class="px-6 py-4 rounded-tr-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($jadwals as $jadwal)
                            @php
                                $isMySchedule = auth()->user()->pegawai_id == $jadwal->pegawai_id && auth()->user()->role == 'guru';
                                $rowClass = $isMySchedule 
                                    ? 'bg-indigo-50/60 dark:bg-indigo-900/10 border-l-4 border-l-indigo-500' 
                                    : 'bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800 border-l-4 border-l-transparent';
                            @endphp
                            <tr class="{{ $rowClass }} transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-md mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">Jam Ke-{{ $jadwal->jam }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($jadwal->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->akhir)->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold text-purple-700 bg-purple-100 dark:bg-purple-900/30 dark:text-purple-300 rounded-full border border-purple-200 dark:border-purple-800">
                                        {{ $jadwal->kelas->kelas ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $jadwal->mapel->mapel ?? '-' }}</span>
                                        @if($isMySchedule)
                                            <span class="px-2 py-0.5 text-[10px] font-bold text-indigo-700 bg-indigo-100 rounded-full border border-indigo-200 uppercase tracking-wide">
                                                KELAS SAYA
                                            </span>
                                        @endif
                                    </div>
                                    @if($jadwal->ket)
                                        <div class="text-xs text-gray-500 mt-1 italic">{{ Str::limit($jadwal->ket, 30) }}</div>
                                    @endif
                                </td>
                                @if(auth()->user()->role != 'guru' || (isset($isPiket) && $isPiket))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                                {{ substr($jadwal->pegawai->name ?? '?', 0, 1) }}
                                            </div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $jadwal->pegawai->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($jadwal->status_presensi == 'sudah')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Sudah Absen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-rose-700 bg-rose-100 dark:bg-rose-900/30 dark:text-rose-300 rounded-full border border-rose-200 dark:border-rose-800">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Belum Absen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($jadwal->status_presensi == 'sudah')
                                        <a href="{{ route('absensi.create', ['jadwal_id' => $jadwal->id]) }}" 
                                           class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                                            <svg class="w-3 h-3 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            Edit Absensi
                                        </a>
                                    @else
                                        <a href="{{ route('absensi.create', ['jadwal_id' => $jadwal->id]) }}" 
                                           class="inline-flex items-center px-4 py-2 text-xs font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-all duration-200 shadow-sm hover:shadow-md transform active:scale-95">
                                            <svg class="w-3 h-3 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                            Isi Absensi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layout.layout>
