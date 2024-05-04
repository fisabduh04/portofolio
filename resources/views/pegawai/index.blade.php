@extends('tampilan.main')
@section('content')

<div class="col-12">
  @if (session('success'))
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>

  @endif

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Data Pegawai SMK AL-MIFTAH Pamekasan</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-start mb-3">
            <a type="button" href="{{ route('tambahpegawai') }}" class="btn btn-outline-info mb-2 col-12 col-md-3 col-lg-2 mr-1">
                <i class="fas fa-database"></i>Tambah
            </a>

            <button type="button" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1" data-toggle="modal" data-target="#modal-default">
                <i class="fas fa-download"></i>Import Excel
            </button>

            <a href="{{ route('exportpegawai') }}" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
                <i class="fas fa-file-export"></i>Export Excel</a>
        </div>

      <hr>

      <table id="example" class="table table-striped table-responsive display" style="width:100%">
        <thead>
          <tr>
            <th></th>
            <th>No</th>
            {{-- <th>ID</th> --}}
            <th>Aksi</th>
            <th>Nama</th>
            <th>Nuptk</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Kota Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Jenis PTK</th>
            <th>Agama</th>
            <th>Jenis Kelamin</th>
            <th>RT</th>
            <th>RW</th>
            <th>SK Pengangkatan</th>
            <th>Lembaga Pengangkatan</th>
            <th>Pangkat Golongan</th>
            <th>Sumber Gaji</th>
            <th>Ibu Kandung</th>
            <th>Status Perkawinan</th>
            <th>Suami/Istri</th>
            <th>Pekerjaan Suami/Istri</th>
            <th>NPWP</th>
            <th>No NIK</th>
            <th>No KK</th>
            <th>foto</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pegawai as $peg)
          <tr>
            <td></td>
            <td>{{ $loop->iteration }}</td>
            {{-- <td>{{ $peg->id }}</td> --}}
            <td>
              <a href="pegawai/{{$peg->id }}/edit" class="btn btn-outline-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
              <form action="pegawai/{{ $peg->id }}" method="POST" class="d-inline" class="d-inline">
                @csrf @method('delete')
                <button type="submit" onclick="return confirm('Apakah anda akan menghapus data ini?')"
                  class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
              </form>
            </td>
            <td>{{ $peg->name}}</td>
            <td>{{ $peg->nuptk}}</td>
            <td>{{ $peg->email}}</td>
            <td>{{ $peg->hp}}</td>
            <td>{{ $peg->kotalahir}}</td>
            <td>{{ $peg->tanggallahir}}</td>
            <td>{{ $peg->alamat}}</td>
            <td>{{ $peg->jenisptk}}</td>
            <td>{{ $peg->agama}}</td>
            <td>{{ $peg->jk}}</td>
            <td>{{ $peg->rt}}</td>
            <td>{{ $peg->rw}}</td>
            <td>{{ $peg->skpengangkatan}}</td>
            <td>{{ $peg->lembagapengangkatan}}</td>
            <td>{{ $peg->PangkatGolongan}}</td>
            <td>{{ $peg->sumbergaji}}</td>
            <td>{{ $peg->ibukandung}}</td>
            <td>{{ $peg->statusPerkawinan}}</td>
            <td>{{ $peg->suamiistri}}</td>
            <td>{{ $peg->pekerjaansuamiIstri}}</td>
            <td>{{ $peg->npwp}}</td>
            <td>{{ $peg->nonik}}</td>
            <td>{{ $peg->nokk}}</td>
            <td>{{ $peg->foto}}</td>
          </tr>
          @endforeach

        </tbody>

      </table>
    </div>
    <!-- /.card-body -->
  </div>
</div>



@endsection

@push('js')

<script>
    new DataTable('#example', {
        columnDefs: [
        {
            render: DataTable.render.select(),
            targets: 0
        }
    ],
    select: {
        style: 'os',
        selector: 'td:first-child'
    },
    order: [[1, 'asc']],
// });
    layout: {
        topStart: {
            buttons: [
                'pageLength','pdf','excel',
                {
                    extend: 'colvis',
                    collectionLayout: 'fixed columns',
                    popoverTitle: 'Column visibility control'
                }
            ]
        }
    },
    stateSave: true
});
</script>
@endpush

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Import File Excel Data Pegawai</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('importpegawai') }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="form-group mb-3">
                <label for="file">Import Data Pegawai</label>
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
