@extends('tampilan.main')

@section('content')

{{-- Alert --}}
@if (session('success'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('delete'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('delete') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Siswa dalam Kelas SMK AL-MIFTAH Pamekasan</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('siswa_kelas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row p-3" id="myForm" style="display: none">
                        <div class="row">
                            <div class="form-group col-sm-10 col-md-4">
                                <label>Tahun Ajaran</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="tahun">
                                    @foreach ($pemetaans as $t)
                                    <option value="{{ $t->id }}">{{ $t->tahun }}-{{ $t->semester }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-10 col-md-4">
                                <label>Kelas</label>
                                <select class="form-control select2bs4" style="width: 100%;" name="kelas">
                                    @foreach ($pemetaans as $k)
                                    <option value="{{ $k->id }}">{{ $k->kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-10 col-md-4">
                                <label>Keterangan</label>
                                <input type="text" class="form-control" placeholder="Keterangan" id="ket" name="ket">
                            </div>
                        </div>

                        <div class="form-group ml-3 mx-auto">
                            <label>Siswa SMK </label>
                            <select class="duallistbox" multiple="multiple" name="siswa[]">
                                @foreach ($pemetaans as $s)
                                <option value="{{ $s->id }}">{{ $s->nipd }}-{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- /.form-group -->
                        <!-- /.col -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

                <div>
                    <div class="d-flex flex-wrap justify-content-start">
                        <button style="display: none" id="TombolHapus"
                            class="btn btn-outline-danger mb-2 col-12 col-md-3 col-lg-2 mr-1">
                            <i class="fas fa-trash-alt"></i>Hapus Data</button>

                        <button type="button" id="TombolTambah" onclick="toggleForm()" class=" btn btn-success mb-2
                            col-12 col-md-3 col-lg-2 mr-1">
                            <i class="fas fa-plus"></i>Tambah
                        </button>

                        <button type="button" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1"
                            data-toggle="modal" data-target="#modal-default">
                            <i class="fas fa-download"></i>Import Excel
                        </button>

                        <a href="{{ route('kelas-siswa-export') }}"
                            class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
                            <i class="fas fa-file-export"></i>Export Excel</a>
                        <select class="btn btn-outline-success mb-2">
                            <option selected>Open this select menu</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>


                    {{--TABEL DATA--}}
                    <div class="container">
                        <div class="table-responsive p-1">
                            <table class="table table-hover tabel-striped" id="example">
                                <thead>
                                    <tr>
                                        {{-- <th><input type="checkbox" name="selectAll" id="selectAll"></th> --}}
                                        <th>No</th>
                                        <th>Aksi</th>
                                        <th>Tahun</th>
                                        <th>NIPD</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pemetaans as $pemetaan)
                                    <tr>
                                        {{-- <td><input type="checkbox" name="pilih{{ $pemetaan->id }}" --}} {{--
                                                id="pilih{{ $pemetaan->id }}" class="checkbox-item"></td> --}}
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <!-- Edit button -->
                                            <button class="btn btn-outline-primary btn-sm" value="{{ $pemetaan->id }}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>

                                            <!-- Delete button -->
                                            <form action="{{ route('siswa_kelas.destroy', $pemetaan->id) }}"
                                                method="POST" id="delete-form-{{ $pemetaan->id }}"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" type="button"
                                                    onclick="confirmDelete({{ $pemetaan->id }})">
                                                    {{-- <i class="fas fa-trash-alt"></i> --}}
                                                    <i class="fas fa-trash-restore"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $pemetaan->tahun->tahun}}-{{ $pemetaan->tahun->semester}}</td>
                                        <td>{{ $pemetaan->siswa->nipd }}</td>
                                        <td>{{ $pemetaan->siswa->nama }}</td>
                                        <td>{{ $pemetaan->kelas->kelas }}</td>
                                        <td>{{ $pemetaan->ket }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- {{ $pemetaans->links('pagination::bootstrap-4') }} --}}
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById('myForm');
        var button = document.getElementById('TombolTambah');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            button.textContent = 'Tutup Form';
            button.style.display = 'block';
        } else {
            form.style.display = 'none';
            button.textContent = 'Tambah Data +';
        }
    }

    function editData(id, kelasId, tahunAjaranId, siswaIds, ket) {
        toggleForm(true);
        document.querySelector('[name="kelas"]').value = kelasId;
        document.querySelector('[name="tahun"]').value = tahunAjaranId;
        document.getElementById('ket').value = ket;

        // Refresh Select2 untuk kelas dan tahun
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        }).trigger('change');

        // Set nilai untuk duallistbox
        const siswaSelect = document.querySelector('.duallistbox');
        for (let option of siswaSelect.options) {
            option.selected = siswaIds.includes(parseInt(option.value));
        }
        // Refresh duallistbox
        $('.duallistbox').bootstrapDualListbox('refresh');
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>

@push('cs')
<!-- Select2 -->
<link rel="stylesheet" href="lte/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<!-- Bootstrap4 Duallistbox -->
<link rel="stylesheet" href="lte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
@endpush

@push('js')
<!-- Bootstrap4 Duallistbox -->
<script src="lte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()
    })
</script>










@endpush

@endsection
{{-- Modal Import Data Siswa ke kelas --}}

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import File Excel Siswa Ke dalam Kelas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('kelas-siswa-import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="file">Import Data Siswa Ke Kelas</label>
                        <input type="file" name="file" id="" class="form-control" accept=".xlsx, .csv, .xls">
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download"></i>Import Excel</button>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>