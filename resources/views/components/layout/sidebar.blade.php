<aside id="top-bar-sidebar" class="fixed top-0 left-0 z-50 sm:z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
      <div class="flex items-center justify-between mb-5">
        <a href="{{ route('dashboard.index') }}" class="flex items-center ps-2.5">
            <img src="{{ $logo ?? asset('img/logo.png') }}" class="h-6 me-3" alt="{{ $sekolah->nama_sekolah ?? 'SMK AL-MIFTAH' }} Logo" />
            <span class="self-center text-lg text-heading font-semibold whitespace-nowrap">{{ $sekolah->nama_sekolah ?? 'SMK AL-MIFTAH' }}</span>
        </a>
        <button type="button" data-drawer-hide="top-bar-sidebar" aria-controls="top-bar-sidebar" class="text-body bg-transparent hover:bg-neutral-tertiary rounded-lg text-sm w-8 h-8 md:hidden inline-flex justify-center items-center">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
      </div>
      <ul class="space-y-2 font-medium">
         <li>
            <a href="{{ route('dashboard.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
                               <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         <li>
            <a href="{{ route('sekolah.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
                               <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="7" height="9" x="3" y="3" rx="1" /><rect width="7" height="5" x="14" y="3" rx="1" /><rect width="7" height="9" x="14" y="12" rx="1" /><rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
               <span class="ms-3">Data Sekolah</span>
            </a>
         </li>

         @can('viewAny', \App\Models\User::class)

         <li>
             <a href="{{ route('operator.users.index') }}" class="flex items-center px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="ms-3">Manajemen Akun</span>
             </a>
         </li>
         @endcan

         <li>
            <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-akademik" data-collapse-toggle="dropdown-akademik">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c3.333 3 8.667 3 12 0v-5" />
                </svg>
                <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Data Master</span>
                <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <ul id="dropdown-akademik" class="hidden py-2 space-y-2">
                <li>
                    <a href="{{ route('sekolah.index') }}" class="flex items-center w-full pl-11 px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">Data Sekolah</a>
                </li>

                <li>
                    <a href="{{ route('operator.users.index') }}" class="flex items-center w-full pl-11 px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand">Manajemen Akun</a>
                </li>

                <li>
                    <a href="{{ route('tahun.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Tahun Akademik</a>
                </li>

                <li>
                    <a href="{{ route('jurusan.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jurusan</a>
                </li>
                <li>
                    <a href="{{ route('pegawai.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Data Pegawai</a>
                </li>
                <li>
                    <a href="{{ route('kelas.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Kelas</a>
                </li>
                <li>
                    <a href="{{ route('mapel.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Mata Pelajaran</a>
                </li>
                <li>
                    <a href="{{ route('siswa.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Siswa</a>
                </li>
                <li>
                    <a href="{{ route('kelassiswa.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Rombongan Belajar</a>
                </li>
                
            </ul>
         </li>

         <!-- Menu Kepegawaian Baru -->
         <li>
            <button type="button" 
                class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" 
                aria-controls="dropdown-kepegawaian" 
                data-collapse-toggle="dropdown-kepegawaian">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Kepegawaian</span>
                <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <ul id="dropdown-kepegawaian" class="hidden py-2 space-y-2">
                <li>
                    <a href="{{ route('attendance.rules.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Aturan Kehadiran</a>
                </li>
                <li>
                    <a href="{{ route('attendance.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Dashboard Absensi</a>
                </li>
                <li>
                    <a href="{{ route('attendance.payroll.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Payroll & Gaji</a>
                </li>
                <li>
                    <a href="{{ route('attendance.report') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Laporan Absensi</a>
                </li>
                <li>
                    <a href="{{ route('attendance.wajib-hadir.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal Wajib Hadir</a>
                </li>
                <li>
                    <a href="{{ route('attendance.events.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Event Khusus</a>
                </li>
                <li>
                    <a href="{{ route('attendance.fingerprint.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Mesin Fingerprint</a>
                </li>
                <li>
                    <a href="{{ route('attendance.setting') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Konfigurasi</a>
                </li>
            </ul>
         </li>

         <li>
            <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-presensi" data-collapse-toggle="dropdown-presensi">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Presensi</span>
                <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <ul id="dropdown-presensi" class="hidden py-2 space-y-2">
                <li>
                    <a href="{{ route('jadwal.presensiHarian') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Presensi Siswa</a>
                </li>
                
                <li>
                    <a href="{{ route('absensi.harian.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Absensi Harian</a>
                </li>                
                @if(auth()->user()->isPiketToday())
                    <li>
                        <a href="{{ route('absensi.piket') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Guru Piket</a>
                    </li>
                @endif
            </ul>
         </li>

         <li>
            <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-absensi" data-collapse-toggle="dropdown-absensi">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                </svg>
                <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Jadwal</span>
                <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <ul id="dropdown-absensi" class="hidden py-2 space-y-2">
                <li>
                    <a href="{{ route('jadwal.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal Mata Pelajaran</a>
                </li>
                <li>
                    <a href="{{ route('jadwal-piket.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal Piket</a>
                </li>
                <li>
                    <a href="{{ route('hari-libur.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal Libur</a>
                </li>
            </ul>
         </li>

         <li>
             <button type="button" 
                 class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base group {{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}" 
                 aria-controls="dropdown-rekap" 
                 data-collapse-toggle="dropdown-rekap"
                 aria-expanded="{{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? 'true' : 'false' }}">
                 <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                     <path d="M3 3v18h18" /><path d="M18 17V9" /><path d="M13 17V5" /><path d="M8 17v-3" />
                 </svg>
                 <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Rekapitulasi</span>
                 <svg class="w-3 h-3 {{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                 </svg>
             </button>
             <ul id="dropdown-rekap" class="{{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? '' : 'hidden' }} py-2 space-y-2">
                  <li>
                      <a href="{{ route('absensi.rekap-harian') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-harian') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Laporan Harian</a>
                  </li>
                 <li>
                     <a href="{{ route('absensi.rekap-bulanan') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-bulanan') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Bulanan</a>
                 </li>
                 <li>
                     <a href="{{ route('absensi.rekap-tahunan') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-tahunan') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Tahunan</a>
                 </li>
                 <li>
                     <a href="{{ route('absensi.rekap-periode') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-periode') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Laporan Periode</a>
                 </li>
                 
             </ul>
         </li>

        
      </ul>
      
   </div>
</aside>
