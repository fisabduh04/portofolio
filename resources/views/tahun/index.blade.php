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
        <h3 class="card-title">Tahun Ajaran SMK AL-MIFTAH</h3>

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
        <form action="tahun" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="tahun">Tahun Ajaran</label>
                <input type="text" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun"
                  placeholder="Tahun Ajaran" required>
                @error('name')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror()
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Semester</label>
                <select class="form-control select2 @error('semester') is-invalid @enderror" style="width: 100%;"
                  name="semester">
                  <option selected="selected" value="Ganjil">Ganjil</option>
                  <option value="Genap">Genap</option>
                </select>
                @error('semester')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror()
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Tanggal Mulai</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                  </div>
                  <input type="date" class="form-control" name="tanggalmulai" placeholder="Tanggal Mulai">
                </div>
                <!-- /.input group -->
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Tanggal Akhir</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                  </div>
                  <input type="date" class="form-control" name="tanggalakhir" placeholder="Tanggal Akhir">
                </div>
                <!-- /.input group -->
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Aktif</label>
                <select class="form-control select2 @error('Aktif') is-invalid @enderror" style="width: 100%;"
                  name="Aktif">
                  <option selected="selected" value="Aktif">Aktif</option>
                  <option value="nonAktif">Non Aktif</option>
                </select>
                @error('Aktif')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror()
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary mt-1">Tambah</button>
            </div>
          </div>
        </form>
      </div>
      <!-- /.card-body -->

    </div>
    <!-- /.card -->

    <div class="card">

      <!-- /.card-header -->
      <div class="card-body">

        <table id="example1" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>No</th>
              <th>Aksi</th>
              <th>Tahun Ajaran</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Akhir</th>
              <th>Aktif</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($tahun as $t)
            <tr>
              <td>{{ $loop->iteration}}</td>
              <td>
                <a href="tahun/{{ $t->id }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="tahun/{{ $t->id }}" method="POST" class="d-inline">
                  @csrf @method('delete')
                  <button type="submit" onclick="return confirm('Apakah anda akan menghapus data ini?')"
                    class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>Del</button>
                </form>
              </td>
              <td>{{ $t->tahun }}</td>
              <td>{{ $t->tanggalmulai }}</td>
              <td>{{ $t->tanggalakhir }}</td>
              <td>{{ $t->Aktif }}</td>
            </tr>
            @endforeach

          </tbody>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Aksi</th>
              <th>Tahun Ajaran</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Akhir</th>
              <th>Aktif</th>
          </tfoot>
        </table>

      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>
@endsection

{{-- DataTAble --}}
{{-- <script src="{{ asset('DataTables/jQuery-3.7.0/jquery-3.7.0.js')}}"></script> --}}
{{-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> --}}
{{-- <scrip src="{{ asset('DataTables/DataTables-2.0.1/js/dataTables.js')}}">
  </script>
  <script src="{{ asset('DataTables/Bootstrap-4-4.6.0/js/bootstrap.min.js')}}"></script>
  <script src="{{ asset('DataTables/Bootstrap-4-4.6.0/js/bootstrap.js')}}"></script>
  <script src="{{ asset('DataTables/DataTables-2.0.1/js/dataTables.js')}}"></script>
  <script src="{{ asset('DataTables/DataTables-2.0.1/js/dataTables.bootstrap4.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/dataTables.buttons.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/buttons.bootstrap4.js')}}"></script>
  <script src="{{ asset('DataTables/JSZip-3.10.1/jszip.min.js')}}"></script>
  <script src="{{ asset('DataTables/pdfmake-0.2.7/pdfmake.min.js')}}"></script>
  <script src="{{ asset('DataTables/pdfmake-0.2.7/vfs_fonts.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/buttons.html5.min.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/buttons.colVis.min.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/buttons.print.min.js')}}"></script>
  <script src="{{ asset('DataTables/Buttons-3.0.0/js/buttons.html5.min.js')}}"></script> --}}

  {{-- <script>
    new DataTable('#example');
    new DataTable('#example'
       , {       
     layout: {
         topStart: {
             buttons: ['pageLength','copy', 'excel', 'pdf', 'colvis'],
            
         }
     }
 }
 );  
  {{-- 
  </script> --}}