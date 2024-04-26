@extends('tampilan.main')
@section('content')

<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Data Detail Pegawai SMK AL-MIFTAH</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form action="pegawai" method="POST" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
      <div class="form-group row">
        <label for="name" class="col-sm-2 col-form-label">Nama</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name"
            placeholder="Nama">
          @error('name')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="nuptk" class="col-sm-2 col-form-label">NUPTK</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('nuptk') is-invalid @enderror" name="nuptk" id="nuptk"
            placeholder="NUPTK">
          @error('nuptk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            placeholder="Email">
          @error('nuptk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="kotalahir" class="col-sm-2 col-form-label">Tempat Lahir</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('kotalahir') is-invalid @enderror" name="kotalahir"
            id="kotalahir" placeholder="Tempat Lahir">
          @error('kotalahir')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="tanggallahir" class="col-sm-2 col-form-label">Tanggal Lahir</label>
        <div class="col-sm-10">
          <input type="date" class="form-control @error('tanggallahir') is-invalid @enderror" name="tanggallahir"
            id="tanggallahir" placeholder="Tanggal Lahir">
          @error('tanggallahir')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>


      <div class="form-group row">
        <label for="jk" class="col-sm-2 col-form-label">Jenis Kelamin</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('jk') is-invalid @enderror" name="jk" id="jk"
            placeholder="Jenis Kelamin">
          @error('jk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="jenisptk" class="col-sm-2 col-form-label">Jenis PTK</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('jenisptk') is-invalid @enderror" name="jenisptk" id="jenisptk"
            placeholder="Jenis PTK">
          @error('jenisptk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="agama" class="col-sm-2 col-form-label">Agama</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('agama') is-invalid @enderror" name="agama" id="agama"
            placeholder="Agama">
          @error('agama')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="rt" class="col-sm-2 col-form-label">RT</label>
        <div class="col-sm-10">
          <input type="number" class="form-control @error('rt') is-invalid @enderror" name="rt" id="rt"
            placeholder="RT">
          @error('rt')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="rw" class="col-sm-2 col-form-label">RW</label>
        <div class="col-sm-10">
          <input type="number" class="form-control @error('rw') is-invalid @enderror" name="rw" id="rw"
            placeholder="RW">
          @error('rw')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('alamat') is-invalid @enderror" name="alamat" id="alamat"
            placeholder="Alamat">
          @error('alamat')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="hp" class="col-sm-2 col-form-label">No HP</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('hp') is-invalid @enderror" name="hp" id="hp"
            placeholder="No HP">
          @error('hp')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="skpengangkatan" class="col-sm-2 col-form-label"> No. SK Pengangkatan</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('skpengangkatan') is-invalid @enderror" name="skpengangkatan"
            id="skpengangkatan" placeholder="No. SK Pengangkatan">
          @error('skpengangkatan')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="lembagapengangkatan" class="col-sm-2 col-form-label">Lembaga Pengangkatan</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control @error('lembagapengangkatan') is-invalid @enderror"
            name="lembagapengangkatan" id="lembagapengangkatan" placeholder="Lembaga Pengangkatan">
          @error('lembagapengangkatan')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="PangkatGolongan" class="col-sm-2 col-form-label">Pangkat Golongan</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control @error('PangkatGolongan') is-invalid @enderror" name="PangkatGolongan"
            id="PangkatGolongan" placeholder="Pangkat Golongan">
          @error('PangkatGolongan')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="sumbergaji" class="col-sm-2 col-form-label">Sumber Gaji</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('sumbergaji') is-invalid @enderror" name="sumbergaji"
            id="sumbergaji" placeholder="Jenis Kelamin">
          @error('sumbergaji')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>


      <div class="form-group row">
        <label for="ibukandung" class="col-sm-2 col-form-label">Ibu Kandung</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('ibukandung') is-invalid @enderror" name="ibukandung"
            id="ibukandung" placeholder="JNama Ibu Kandung">
          @error('ibukandung')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="statusPerkawinan" class="col-sm-2 col-form-label">Status Perkawinan</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('statusPerkawinan') is-invalid @enderror"
            name="statusPerkawinan" id="statusPerkawinan" placeholder="Status Perkawinan">
          @error('statusPerkawinan')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="suamiistri" class="col-sm-2 col-form-label">Suami / Istri</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('suamiistri') is-invalid @enderror" name="suamiistri"
            id="suamiistri" placeholder="Suami / Istri">
          @error('suamiistri')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="pekerjaansuamiIstri" class="col-sm-2 col-form-label">Pekerjaan Suami/Istri</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('pekerjaansuamiIstri') is-invalid @enderror"
            name="pekerjaansuamiIstri" id="pekerjaansuamiIstri" placeholder="Pekerjaan Suami/Istri">
          @error('pekerjaansuamiIstri')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="npwp" class="col-sm-2 col-form-label">No. NPWP</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('npwp') is-invalid @enderror" name="npwp" id="npwp"
            placeholder="No. NPWP">
          @error('npwp')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="nonik" class="col-sm-2 col-form-label">No. NIK</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('nonik') is-invalid @enderror" name="nonik" id="nonik"
            placeholder="No. NIK">
          @error('nonik')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="nokk" class="col-sm-2 col-form-label">No. KK</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('nokk') is-invalid @enderror" name="nokk" id="nokk"
            placeholder="No. KK">
          @error('nokk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="foto" class="col-sm-2 col-form-label">Foto</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('foto') is-invalid @enderror" name="foto" id="foto"
            placeholder="Foto">
          @error('foto')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="scan_kk" class="col-sm-2 col-form-label">Scan KK</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('scan_kk') is-invalid @enderror" name="scan_kk" id="scan_kk"
            placeholder="Scan KK">
          @error('scan_kk')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>

      <div class="form-group row">
        <label for="scan_ijasah" class="col-sm-2 col-form-label">Scan Ijazah</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('scan_ijasah') is-invalid @enderror" name="scan_ijasah"
            id="scan_ijasah" placeholder="Scan Ijazah">
          @error('scan_ijasah')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="scan_ppg" class="col-sm-2 col-form-label">Scan Sertifikat PPG</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('scan_ppg') is-invalid @enderror" name="scan_ppg" id="scan_ppg"
            placeholder="Scan Sertifikat PPG">
          @error('scan_ppg')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>
      <div class="form-group row">
        <label for="scan_ktp" class="col-sm-2 col-form-label">KTP</label>
        <div class="col-sm-10">
          <input type="text" class="form-control @error('scan_ktp') is-invalid @enderror" name="scan_ktp" id="scan_ktp"
            placeholder="KTP">
          @error('scan_ktp')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
          @enderror
        </div>
      </div>


    </div>


    <!-- /.card-body -->
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
    <!-- /.card-footer -->
  </form>
</div>




@endsection
