@extends('tampilan.main')
@section('content')

<style>
/* CSS untuk membuat tabel terlihat formal dan elegan seperti pada Core UI */

.table {
    font-family: 'Poppins', sans-serif; /* Ganti font family */
    border-collapse: collapse;
    width: 100%;
    margin: 0 auto;
    background: linear-gradient(to bottom, #f8f9fa, #e9ecef); /* Warna latar belakang abu-abu terdegradasi */
    color: #495057; /* Warna teks */
}
.table thead th {
    background-color: #f8f9fa; /* Warna latar header */
    color: #495057; /* Warna teks header */
    border-bottom: 2px solid #dee2e6; /* Garis bawah header */
    text-align: center;
    font-weight: bold; /* Teks header tebal */
}

.table tbody tr {
    background-color: #fff; /* Warna latar baris */
}

.table tbody tr:hover {
    background-color: #f1f1f1; /* Warna saat hover */
}

.table td, .table th {
    border: 1px solid #dee2e6; /* Garis tepi */
    padding: 12px; /* Padding */
    font-weight: normal; /* Teks sel normal */
}

.table th:first-child,
.table td:first-child {
    border-left: 1px solid #dee2e6; /* Garis kiri */
}

.table th:last-child,
.table td:last-child {
    border-right: 1px solid #dee2e6; /* Garis kanan */
}

</style>

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
      <h3 class="card-title">Data Siswa SMK AL-MIFTAH Pamekasan</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-start mb-3">
            <a type="button" href="" class="btn btn-outline-info mb-2 col-12 col-md-3 col-lg-2 mr-1">
                <i class="fas fa-database"></i>Tambah
            </a>

            <button type="button" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1" data-toggle="modal" data-target="#modal-default">
                <i class="fas fa-download"></i>Import Excel
            </button>

            <a href="{{ route('exportsiswa') }}" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
                <i class="fas fa-file-export"></i>Export Excel</a>
        </div>

      <hr>

      <table id="example" class="table table-striped table-responsive" style="width:100%">
        <thead>
          <tr>
            <th>No</th>
            <th>Aksi</th>
            <th>Nama</th>
            <th>NIPD</th>
            <th>Jenis Kelamin</th>
            <th>Aktif</th>
            <th>NISN</th>
            <th>Tempat Lahir</th>
            <th>Tanggal Lahir</th>
            <th>NIK</th>
            <th>Agama</th>
            <th>Alamat</th>
            <th>RT</th>
            <th>RW</th>
            <th>Dusun</th>
            <th>Kelurahan</th>
            <th>Kecamatan</th>
            <th>Kode Pos</th>
            <th>Jenis Tinggal</th>
            <th>Alat Transportasi</th>
            <th>Telepon</th>
            <th>HP</th>
            <th>E-Mail</th>
            <th>SKHUN</th>
            <th>Penerima KPS</th>
            <th>No. KPS</th>
            <th>Nama ayah</th>
            <th>Tahun Lahir</th>
            <th>Jenjang Pendidikan</th>
            <th>Pekerjaan</th>
            <th>Penghasilan</th>
            <th>NIK</th>
            <th>Nama wali</th>
            <th>Tahun Lahir</th>
            <th>Jenjang <br> Pendidikan</th>
            <th>Pekerjaan</th>
            <th>Penghasilan</th>
            <th>NIK</th>
            <th>Nama Wali</th>
            <th>Tahun Lahir</th>
            <th>Jenjang Pendidikan</th>
            <th>Pekerjaan</th>
            <th>Penghasilan</th>
            <th>NIK</th>
            <th>Rombel Saat Ini</th>
            <th>No Ujian Nasional</th>
            <th>No Seri Ijazah</th>
            <th>Penerima KIP</th>
            <th>Nomor KIP</th>
            <th>Nama di KIP</th>
            <th>Nomor KKS</th>
            <th>NoAkta Lahir</th>
            <th>Bank</th>
            <th>Nomor <br> Rekening Bank</th>
            <th>Rekening Atas Nama</th>
            <th>Layak PIP</th>
            <th>Alasan Layak PIP</th>
            <th>Kebutuhan Khusus</th>
            <th>Sekolah Asal</th>
            <th>Anak ke-berapa</th>
            <th>Lintang</th>
            <th>Bujur</th>
            <th>No KK</th>
            <th>Berat Badan</th>
            <th>Tinggi Badan</th>
            <th>Lingkar Kepala</th>
            <th>Jml. Saudara Kandung</th>
            <th>Jarak Rumah </th>
        </thead>
        <tbody>
          @foreach ($siswa as $sis)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
              <a href="siswa/{{$sis->id }}/edit" class="btn btn-outline-info btn-sm"><i class="fas fa-pencil-alt"></i></a>
              <form action="siswa/{{ $sis->id }}" method="POST" class="d-inline" class="d-inline">
                @csrf @method('delete')
                <button type="submit" onclick="return confirm('Apakah anda akan menghapus data ini?')"
                  class="btn btn-outline-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
              </form>
            </td>
            <td>{{$sis->nama}}</td>
            <td>{{$sis->nipd}}</td>
            <td>{{$sis->jk}}</td>
            <td>{{$sis->aktif}}</td>
            <td>{{$sis->nisn}}</td>
            <td>{{$sis->tempatlahir}}</td>
            <td>{{$sis->tanggallahir}}</td>
            <td>{{$sis->nik}}</td>
            <td>{{$sis->agama}}</td>
            <td>{{$sis->alamat}}</td>
            <td>{{$sis->rt}}</td>
            <td>{{$sis->rw}}</td>
            <td>{{$sis->dusun}}</td>
            <td>{{$sis->kelurahan}}</td>
            <td>{{$sis->kecamatan}}</td>
            <td>{{$sis->kode_pos}}</td>
            <td>{{$sis->jenis_tinggal}}</td>
            <td>{{$sis->alat_transportasi}}</td>
            <td>{{$sis->telepon}}</td>
            <td>{{$sis->hp}}</td>
            <td>{{$sis->email}}</td>
            <td>{{$sis->skhun}}</td>
            <td>{{$sis->penerima_kps}}</td>
            <td>{{$sis->nokps}}</td>
            <td>{{$sis->ayah}}</td>
            <td>{{$sis->tahunlahirayah}}</td>
            <td>{{$sis->pendidikanayah}}</td>
            <td>{{$sis->pekerjaanayah}}</td>
            <td>{{$sis->penghasilanayah}}</td>
            <td>{{$sis->nikayah}}</td>
            <td>{{$sis->namaibu}}</td>
            <td>{{$sis->tahunlahiribu}}</td>
            <td>{{$sis->pendidikanibu}}</td>
            <td>{{$sis->pekerjaanibu}}</td>
            <td>{{$sis->penghasilanibu}}</td>
            <td>{{$sis->nikibu}}</td>
            <td>{{$sis->namawali}}</td>
            <td>{{$sis->tahunlahirwali}}</td>
            <td>{{$sis->pendidikanwali}}</td>
            <td>{{$sis->pekerjaanwali}}</td>
            <td>{{$sis->penghasilanwali}}</td>
            <td>{{$sis->nikwali}}</td>
            <td>{{$sis->rombelsaatini}}</td>
            <td>{{$sis->nopesertaunas}}</td>
            <td>{{$sis->noijazah}}</td>
            <td>{{$sis->penerimakip}}</td>
            <td>{{$sis->nomorkip}}</td>
            <td>{{$sis->namadikip}}</td>
            <td>{{$sis->nomorkks}}</td>
            <td>{{$sis->noaktalahir}}</td>
            <td>{{$sis->bank}}</td>
            <td>{{$sis->nomor_rekening_bank}}</td>
            <td>{{$sis->rekening_atas_nama}}</td>
            <td>{{$sis->layakpip}}</td>
            <td>{{$sis->alasanlayakpip}}</td>
            <td>{{$sis->kebutuhankhusus}}</td>
            <td>{{$sis->sekolahasal}}</td>
            <td>{{$sis->anakke}}</td>
            <td>{{$sis->lintang}}</td>
            <td>{{$sis->bujur}}</td>
            <td>{{$sis->nokk}}</td>
        <td>{{$sis->beratbadan}}</td>
        <td>{{$sis->tinggibadan}}</td>
        <td>{{$sis->lingkarkepala}}</td>
        <td>{{$sis->jmlsaudara}}</td>  
        <td>{{$sis->jarakrumah}}</td>           
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
        autoWidth: true,
    //     columnDefs: [
    //     {
    //         render: DataTable.render.select(),
    //         targets: 0
    //     }
    // ],
    // select: {
    //     style: 'os',
    //     selector: 'td:first-child'
    // },
    // order: [[1, 'asc']],
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
          <h4 class="modal-title">Import File Excel Data siswa</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('importsiswa') }}" method="POST" enctype="multipart/form-data" >
            @csrf
            <div class="form-group mb-3">
                <label for="file">Import Data siswa</label>
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
