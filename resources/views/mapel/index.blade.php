@extends('tampilan.main')

@section('Judul','Mata Pelajaran')
@section('content')

{{-- session berhasil  --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success')}}
    </div>
@endif
{{-- session error atau data gagal --}}

@if ($errors->any())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
@endif


<div class="col-12">
  <!-- Default box -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Daftar Seluruh Mata Pelajaran SMK AL-MIFTAH Pamekasan</h3>
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
        @livewire('Mapel.datamapel')
    </div>
    <!-- /.card-body -->

  </div>
  <!-- /.card -->
</div>


@endsection

{{-- ModalImport Excel --}}

