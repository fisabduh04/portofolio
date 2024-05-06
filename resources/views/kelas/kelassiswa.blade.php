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
            <div>
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


                <div class="d-flex flex-wrap justify-content-start mb-3">

                    <button class="btn btn-outline-danger mb-2 col-12 col-md-3 col-lg-2 mr-1">
                        <i class="fas fa-trash-alt"></i>Hapus Data</button>


                    <button type="button" wire:click="add" class="btn btn-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
                        <i class="fas fa-plus"></i>Tambah
                    </button>


                    <button type="submit" class="btn btn-outline-primary mb-2 col-12 col-md-3 col-lg-2 mr-1">
                        <i class="fas fa-cloud-download-alt"></i>Simpan Data
                    </button>


                    <button type="button" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1"
                        data-toggle="modal" data-target="#modal-default">
                        <i class="fas fa-download"></i>Import Excel
                    </button>

                    <a href="{{ route('exportkelas') }}"
                        class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
                        <i class="fas fa-file-export"></i>Export Excel</a>
                </div>
                <hr>

                <div class="row mt-2">
                    <div class="col-md-4 col-sm-12">
                        <!-- Dropdown untuk memilih jumlah item per halaman -->
                        <div class="mb-2">
                            <label for="perPage" class="col-form-label">Data</label>
                            <select wire:model.live="perPage" class="custom-select form-control-border col-3 float-left"
                                id="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="-1">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4 col-sm-12">
                        <!-- Input search -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control " placeholder="Search">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                        {{-- {{ $search }} --}}
                    </div>
                </div>

                {{--TABEL DATA--}}
                <div class="table-responsive p-0">
                    <table class="table table-hover tabel-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Aksi</th>
                                <th>Kelas</th>
                                <th>NIPD</th>
                                <th>Nama</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach($kelas as $index => $val)
                            <tr>
                                <td>#</td>
                                <td>{{ $index }}</td>
                                <td>
                                    <button type="button" wire:click="remove({{ $index }})"
                                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> </button>
                                </td>

                                <td>
                                    <input type="text" wire:model="kelas.{{ $index }}" class="form-control">
                                </td>
                                <td>
                                    <select class="custom-select" wire:model="jurusan.{{ $index }}">
                                        <option selected>Pilih Jurusan</option>
                                        @foreach ($jurusanlist as $item)
                                        <option value="{{ $item->id }}">{{ $item->jurusan }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" wire:model="ket.{{ $index }}" class="form-control">
                                </td>
                            </tr>
                            @endforeach --}}


                            {{-- @foreach ($kelaslist as $index => $m)
                            <tr>
                                <td>
                                    <input type="checkbox" wire:key="{{ $m->id }}" wire:model.live="kelas_selected_id"
                                        value="{{ $m->id }}">
                                </td>

                                <td>{{ $m->id }}</td>
                                <td>
                                    <form class="d-inline"
                                        onclick="return confirm('Apakah anda yakin akan menghapus data ini')"
                                        action="kelas/{{ $m->id }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @if ($editkelasindex === $m->id)
                                    <button wire:click="update({{ $m->id }})" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    @else
                                    <button wire:click="edit({{ $m->id }})" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    @endif


                                </td>

                                <td>

                                    <input type="text" wire:model="editkelas" class="form-control">
                                    @else
                                    @endif
                                </td>
                                <td>
                                    @if ($editkelasindex===$m->id)
                                    <select class="custom-select col-md-8" wire:model="editjurusan">
                                        @foreach ($jurusanlist as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $m->jurusan_id ? 'selected' : ''
                                            }}>{{
                                            $item->jurusan}}</option>

                                        @endforeach
                                    </select>
                                    @else
                                    @if ($m->jurusan)
                                    {{ $m->jurusan->jurusan }}
                                    @else
                                    Tidak Ada
                                    @endif
                                    @endif
                                </td>

                                <td>
                                    @if ($editkelasindex===$m->id)
                                    <input type="text" wire:model="editket" class="form-control" value="{{ $m->ket }}">
                                    @else
                                    {{ $m->ket }}
                                    @endif
                                </td>


                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <!-- /.card-body -->

    </div>
    <!-- /.card -->
</div>
@endsection