@extends('tampilan.main')

@section('judul','uji coba')
@section('content')

<div class="col-12">
    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Jurusan SMK AL-MIFTAH Pamekasan</h3>
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
            @livewire('jurusan.data')
        </div>
        <!-- /.card-body -->

    </div>
    <!-- /.card -->
</div>



@endsection