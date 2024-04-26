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
      <a type="button" class="btn btn-outline-primary" href="/Addpegawai"><i class="far fa-thin fa-user"></i>
        Tambah</a>

      <a type="button" class="btn btn-success" href="#"><i class="far fa-thin fa-user"></i>
        Import Excel</a>
      <button class="btn btn-outline-danger"><i class="fas fa-trash-alt"></i>Hapus Data </button>
      <button type="button" class="btn btn-success toastrDefaultSuccess">
        Launch Success Toast
      </button>
      <hr>

      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            {{-- <th>ID</th> --}}
            <th>Aksi</th>
            <th>Nama</th>
            <th>Nuptk</th>
            <th>Kota Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Jenis PTK</th>
            <th>Agama</th>
            <th>Alamat</th>
            <th>RT</th>
            <th>RW</th>
            <th>No. HP</th>
            <th>Email</th>
            <th>Jenis Kelamin</th>
            <th>No NIK</th>
            <th>No KK</th>
            <th>SK Pengangkatan</th>
            <th>Lembaga Pengangkatan</th>
            <th>Pangkat Golongan</th>
            <th>Sumber Gaji</th>
            <th>Ibu Kandung</th>
            <th>Status Perkawinan</th>
            <th>Suami/Istri</th>
            <th>Pekerjaan Suami/Istri</th>
            <th>NPWP</th>
            <th>foto</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pegawai as $peg)
          <tr>
            <td>{{ $loop->iteration }}</td>
            {{-- <td>{{ $peg->id }}</td> --}}
            <td>
              <a href="pegawai/{{ $peg->id }}" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>
              <form action="pegawai/{{ $peg->id }}" method="POST" class="d-inline" class="d-inline">
                @csrf @method('delete')
                <button type="submit" onclick="return confirm('Apakah anda akan menghapus data ini?')"
                  class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
              </form>
            </td>
            <td>{{ $peg->name }}</td>
            <td>{{ $peg->nuptk }}</td>
            <td>{{ $peg->alamat }}</td>
            <td>{{ $peg->kotalahir }}</td>
            <td>{{ $peg->tanggallahir }}</td>
            <td>{{ $peg->jenisptk }}</td>
            <td>{{ $peg->alamat }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
            <td>{{ $peg->hp }}</td>
          </tr>
          @endforeach

        </tbody>
        <tfoot>
          <tr>
            <th>No</th>
            {{-- <th>ID</th> --}}
            <th>Aksi</th>
            <th>Nama</th>
            <th>Nuptk</th>
            <th>Kota Lahir</th>
            <th>Tanggal Lahir</th>
            <th>Jenis PTK</th>
            <th>Agama</th>
            <th>Alamat</th>
            <th>RT</th>
            <th>RW</th>
            <th>No. HP</th>
            <th>Email</th>
            <th>Jenis Kelamin</th>
            <th>No NIK</th>
            <th>No KK</th>
            <th>SK Pengangkatan</th>
            <th>Lembaga Pengangkatan</th>
            <th>Pangkat Golongan</th>
            <th>Sumber Gaji</th>
            <th>Ibu Kandung</th>
            <th>Status Perkawinan</th>
            <th>Suami/Istri</th>
            <th>Pekerjaan Suami/Istri</th>
            <th>NPWP</th>
            <th>foto</th>
          </tr>
        </tfoot>
      </table>
    </div>
    <!-- /.card-body -->
  </div>
</div>
@endsection
