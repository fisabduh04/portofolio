<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link">
    <img src="img/almiftah.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">SMK AL-MIFTAN</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="img\almiftah.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Sistem Informasi Terpadu</a>
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
        <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              DATA MASTER
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="tahun" class="nav-link {{ (request()->is('tahun')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tahun Ajaran</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="jurusan" class="nav-link {{ (request()->is('jurusan')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jurusan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="kelas" class="nav-link {{ (request()->is('kelas')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Kelas</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pegawai" class="nav-link {{ (request()->is('pegawai')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pegawai</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link {{ (request()->is('tahun')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tahun</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="mapel" class="nav-link {{ (request()->is('mapel')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Mata Pelajaran</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="tabel" class="nav-link {{ (request()->is('tabel')?'active':'') }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tabel</p>
              </a>
            </li>

          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Simple Link
              <span class="right badge badge-danger">New</span>
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
