<aside id="top-bar-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
      <a href="{{ route('dashboard.index') }}" class="flex items-center ps-2.5 mb-5">
         <img src="{{ asset('img/logo.png') }}" class="h-6 me-3" alt="SMK AL-MIFTAH Logo" />
         <span class="self-center text-lg text-heading font-semibold whitespace-nowrap">SMK AL-MIFTAH</span>
      </a>
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
            <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-akademik" data-collapse-toggle="dropdown-akademik">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c3.333 3 8.667 3 12 0v-5" />
                </svg>
                <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Akademik</span>
                <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <ul id="dropdown-akademik" class="hidden py-2 space-y-2">
                <li>
                    <a href="{{ route('tahun.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Tahun Akademik</a>
                </li>
                <li>
                    <a href="{{ route('jurusan.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jurusan</a>
                </li>
                <li>
                    <a href="{{ route('pegawai.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Pegawai</a>
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
                <li>
                    <a href="{{ route('jadwal.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal</a>
                </li>
            </ul>
         </li>

         <li>
            <a href="{{ route('absensi.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4"/>
               </svg>
               <span class="ms-3">Presensi</span>
            </a>
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
                     <a href="{{ route('absensi.rekap') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Harian</a>
                 </li>
                 <li>
                     <a href="{{ route('absensi.rekap-bulanan') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-bulanan') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Bulanan</a>
                 </li>
                 <li>
                     <a href="{{ route('absensi.rekap-tahunan') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('absensi.rekap-tahunan') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Tahunan</a>
                 </li>
                 <li>
                     <a href="{{ route('jadwal.rekap') }}" class="flex items-center w-full px-2 py-1.5 transition duration-75 rounded-base pl-11 group {{ request()->routeIs('jadwal.rekap') ? 'bg-neutral-tertiary text-fg-brand' : 'text-body hover:bg-neutral-tertiary hover:text-fg-brand' }}">Jadwal Mengajar</a>
                 </li>
             </ul>
         </li>

         <li>
             <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-coba" data-collapse-toggle="dropdown-coba">
                <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4.5 3h15" /><path d="M6 3v16a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V3" /><path d="M6 14h12" />
                </svg>
                 <span class="flex-1 text-left ms-3 rtl:text-right whitespace-nowrap">Uji Coba</span>
                 <svg class="w-3 h-3 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                 </svg>
             </button>
             <ul id="dropdown-coba" class="hidden py-2 space-y-2">
                 <li>
                     <a href="/coba" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Uji Coba</a>
                 </li>
                 <li>
                     <a href="/users" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">User</a>
                 </li>
             </ul>
         </li>
      </ul>
   </div>
</aside>
