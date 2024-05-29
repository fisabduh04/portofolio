<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Manage Siswa ke Kelas</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/css/adminlte.min.css">
  <style>
    .hidden {
      display: none;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <button id="btn-tambah" class="btn btn-success mb-3" onclick="toggleForm(true)">Tambah Siswa ke
                Kelas</button>
              <div id="form-container" class="card card-primary hidden">
                <div class="card-header">
                  <h3 class="card-title" id="form-title">Tambah Siswa ke Kelas</h3>
                </div>
                <form id="form-siswa-kelas" method="POST" action="{{ route('siswa_kelas.store') }}">
                  @csrf
                  <input type="hidden" id="form-method" name="_method" value="POST">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="kelas_id">Kelas</label>
                      <select class="form-control" id="kelas_id" name="kelas_id">
                        @foreach ($kelases as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="siswa_id">Siswa</label>
                      <select class="form-control" id="siswa_id" name="siswa_id[]" multiple>
                        @foreach ($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="tahun_ajaran_id">Tahun Ajaran</label>
                      <select class="form-control" id="tahun_ajaran_id" name="tahun_ajaran_id">
                        @foreach ($tahunAjarans as $tahunAjaran)
                        <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="form-submit">Submit</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm(false)">Cancel</button>
                  </div>
                </form>
              </div>
              <!-- Filter section -->
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                  <form action="{{ route('siswa_kelas.filtered') }}" method="GET">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="filter_kelas_id">Filter Kelas:</label>
                          <select class="form-control" id="filter_kelas_id" name="kelas_id">
                            <option value="">Pilih Kelas</option>
                            @foreach ($kelases as $kelas)
                            <option value="{{ $kelas->id }}" {{ request('kelas_id')==$kelas->id ? ' selected' : '' }}>{{
                              $kelas->nama_kelas }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="filter_tahun_ajaran_id">Filter Tahun Ajaran:</label>
                          <select class="form-control" id="filter_tahun_ajaran_id" name="tahun_ajaran_id">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ request('tahun_ajaran_id')==$tahunAjaran->id ? '
                              selected' : '' }}>{{ $tahunAjaran->tahun }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                  </form>
                </div>
              </div>
              <!-- Tabel dengan ikon edit dan hapus -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Daftar Siswa per Kelas</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Kelas</th>
                        <th>Nama Siswa</th>
                        <th>Tahun Ajaran</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($pemetaans as $pemetaan)
                      <tr>
                        <td>{{ $pemetaan->kelas->nama_kelas }}</td>
                        <td>{{ $pemetaan->siswa->nama }}</td>
                        <td>{{ $pemetaan->tahun_ajaran->tahun }}</td>
                        <td>
                          <a href="#" class="btn btn-xs btn-primary"
                            onclick="editData({{ $pemetaan->id }}, '{{ $pemetaan->kelas_id }}', '{{ $pemetaan->tahun_ajaran_id }}', {{ json_encode($pemetaan->siswas->pluck('id')) }})"><i
                              class="fas fa-edit"></i></a>
                          <form id="delete-form-{{ $pemetaan->id }}"
                            action="{{ route('siswa_kelas.destroy', $pemetaan->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-xs btn-danger"
                              onclick="confirmDelete('{{ $pemetaan->id }}')"><i class="fas fa-trash"></i></button>
                          </form>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
  <script>
    function toggleForm(show) {
    document.getElementById('form-container').classList.toggle('hidden', !show);
    document.getElementById('btn-tambah').classList.toggle('hidden', show);
    if (!show) {
        resetForm();
    }
}

function resetForm() {
    document.getElementById('form-title').innerText = 'Tambah Siswa ke Kelas';
    document.getElementById('form-method').value = 'POST';
    document.getElementById('form-siswa-kelas').action = "{{ route('siswa_kelas.store') }}";
    document.getElementById('form-submit').innerText = 'Submit';
    document.getElementById('kelas_id').value = '';
    document.getElementById('siswa_id').value = '';
    document.getElementById('tahun_ajaran_id').value = '';
}

function editData(id, kelasId, tahunAjaranId, siswaIds) {
    toggleForm(true);
    document.getElementById('form-title').innerText = 'Edit Siswa ke Kelas';
    document.getElementById('form-method').value = 'PUT';
    document.getElementById('form-siswa-kelas').action = "{{ url('siswa-kelas/update') }}/" + id;
    document.getElementById('form-submit').innerText = 'Update';
    document.getElementById('kelas_id').value = kelasId;
    document.getElementById('tahun_ajaran_id').value = tahunAjaranId;

    const siswaSelect = document.getElementById('siswa_id');
    for (let option of siswaSelect.options) {
        option.selected = siswaIds.includes(parseInt(option.value));
    }
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
  </script>
</body>

</html>
