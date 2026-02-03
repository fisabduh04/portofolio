<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard.index') }}" class="brand-link">
        <img src="{{ $logo ?? asset('img/almiftah.jpg') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light"> <strong>{{ $sekolah->nama_sekolah ?? 'SMK AL-MIFTAH' }}</strong></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="{{ asset('img/almiftah.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name ?? 'Tamu' }}</a>
                <small class="text-muted text-xs">{{ auth()->user()->email ?? '' }}</small>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- DATA MASTER -->
                <li class="nav-header text-xs font-bold text-gray-400 uppercase tracking-widest mt-2 ml-3">DATA MASTER</li>
                
                <li class="nav-item">
                    <a href="{{ route('tahun.index') }}" class="nav-link {{ request()->routeIs('tahun*') ? 'active' : '' }}">
                        <i class="nav-icon far fa-calendar-check"></i>
                        <p>Tahun Ajaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jurusan.index') }}" class="nav-link {{ request()->routeIs('jurusan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>Jurusan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kelas.index') }}" class="nav-link {{ request()->routeIs('kelas.*') || request()->routeIs('kelas.index') ? 'active' : '' }}">
                         {{-- Note: 'kelas.*' matches kelas.index, kelas.create etc. --}}
                        <i class="nav-icon fas fa-school"></i>
                        <p>Kelas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kelassiswa.index') }}" class="nav-link {{ request()->routeIs('kelassiswa*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>Rombongan Belajar</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.index') }}" class="nav-link {{ request()->routeIs('pegawai*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Pegawai / Guru</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('siswa.index') }}" class="nav-link {{ request()->routeIs('siswa*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Siswa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mapel.index') }}" class="nav-link {{ request()->routeIs('mapel*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book"></i>
                        <p>Mata Pelajaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>

                <!-- ABSENSI SECTION -->
                <li class="nav-header text-xs font-bold text-gray-400 uppercase tracking-widest mt-4 ml-3">ABSENSI</li>
                <li class="nav-item">
                    <a href="{{ route('jadwal.index') }}" class="nav-link {{ request()->routeIs('jadwal.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal & Presensi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('jadwal-piket.index') }}" class="nav-link {{ request()->routeIs('jadwal-piket*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Jadwal Piket</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('absensi.piket') }}" class="nav-link {{ request()->routeIs('absensi.piket') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clock"></i>
                        <p>Presensi Piket</p>
                    </a>
                </li>

                <!-- LAPORAN SECTION -->
                <li class="nav-header text-xs font-bold text-gray-400 uppercase tracking-widest mt-4 ml-3">LAPORAN</li>
                <li class="nav-item {{ request()->routeIs('absensi.rekap*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('absensi.rekap*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Rekapitulasi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('absensi.rekap') }}" class="nav-link {{ request()->fullUrl() == route('absensi.rekap') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ringkasan (Summary)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('absensi.rekap', ['view' => 'detail']) }}" class="nav-link {{ request()->input('view') == 'detail' ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Detail Jurnal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('absensi.rekap-harian') }}" class="nav-link {{ request()->routeIs('absensi.rekap-harian') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Laporan Harian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('absensi.rekap-bulanan') }}" class="nav-link {{ request()->routeIs('absensi.rekap-bulanan') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matriks Bulanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('absensi.rekap-tahunan') }}" class="nav-link {{ request()->routeIs('absensi.rekap-tahunan') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rekap Tahunan</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>