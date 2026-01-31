<aside id="top-bar-sidebar" class="fixed top-0 left-0 z-40 w-64 h-full transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 py-4 overflow-y-auto bg-neutral-primary-soft border-e border-default">
      <a href="{{ route('dashboard.index') }}" class="flex items-center ps-2.5 mb-5">
         <img src="{{ asset('img/almiftah.jpg') }}" class="h-6 me-3 rounded-full" alt="SMK AL-MIFTAH Logo" />
         <span class="self-center text-lg text-heading font-semibold whitespace-nowrap">SMK AL-MIFTAH</span>
      </a>
      <ul class="space-y-2 font-medium">
         <li>
            <a href="/coba" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
               <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/>
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/>
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>

         <li>
             <button type="button" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base group hover:bg-neutral-tertiary hover:text-fg-brand" aria-controls="dropdown-akademik" data-collapse-toggle="dropdown-akademik">
                 <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023"/>
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
                     <a href="{{ route('kelassiswa.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Siswa Per Kelas</a>
                 </li>
                 <li>
                     <a href="{{ route('jadwal.index') }}" class="flex items-center w-full px-2 py-1.5 text-body transition duration-75 rounded-base pl-11 group hover:bg-neutral-tertiary hover:text-fg-brand">Jadwal</a>
                 </li>
             </ul>
         </li>

         <li>
            <a href="{{ route('absensi.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
               <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
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
                 <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('absensi.rekap*', 'jadwal.rekap') ? 'text-fg-brand' : 'group-hover:text-fg-brand' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-4.5"/>
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
                 <svg class="w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m8-2h3m-3 3h3m-4 3v6m4-3H8M19 4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
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
         
         <li>
             <form action="{{ route('logout') }}" method="POST">
                 @csrf
                 <button type="submit" class="flex items-center w-full px-2 py-1.5 text-body rounded-base hover:bg-red-100 hover:text-red-600 group transition duration-75">
                     <svg class="flex-shrink-0 w-5 h-5 transition duration-75 group-hover:text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                         <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                     </svg>
                     <span class="flex-1 ms-3 whitespace-nowrap text-left">Sign Out</span>
                 </button>
             </form>
         </li>

      </ul>
   </div>
</aside>
