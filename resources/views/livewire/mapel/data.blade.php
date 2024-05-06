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
        @if ($mapel_selected_id)
        <button class="btn btn-outline-danger mb-2 col-12 col-md-3 col-lg-2 mr-1"
            wire:confirm="Apakah Anda yakin data akan dihapus?" wire:click.prevent="del()">
            <i class="fas fa-trash-alt"></i>Hapus {{ count($mapel_selected_id) }} Data
        </button>
        @endif

        <button type="button" wire:click="add" class="btn btn-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
            <i class="fas fa-plus"></i>Tambah
        </button>

        @if($tombol_simpan)
        <button type="submit" class="btn btn-outline-primary mb-2 col-12 col-md-3 col-lg-2 mr-1"
            wire:click.prevent="store()">
            <i class="fas fa-cloud-download-alt"></i>Simpan Data
        </button>
        @endif


        <button type="button" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1" data-toggle="modal"
            data-target="#modal-default">
            <i class="fas fa-download"></i>Import Excel
        </button>

        <a href="{{ route('exportportmapel') }}" class="btn btn-outline-success mb-2 col-12 col-md-3 col-lg-2 mr-1">
            <i class="fas fa-file-export"></i>Export Excel</a>
    </div>

    <div class="row mt-2">
        <div class="col-md-6 col-sm-12">
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
        <div class="col-md-6 col-sm-12">
            <!-- Input search -->
            <div class="input-group mb-3">
                <input type="text" class="form-control" wire:model.live="search" placeholder="Search">
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
                    <th style="width: 2%;">
                        <input type="checkbox" wire:model.live="SelectAll">
                    </th>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 10%;">Aksi</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 30%;">Mata Pelajaran</th>
                    <th style="width: 20%;">Jurusan</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mapel as $index => $val)
                <tr>
                    <td>#</td>
                    <td>{{ $index }}</td>
                    <td>
                        <button type="button" wire:click="remove({{ $index }})" class="btn btn-danger btn-sm"><i
                                class="fas fa-trash-alt"></i> </button>
                    </td>

                    {{-- <td>{{ $index }}</td> --}}
                    <td>
                        <input type="text" wire:model="kode.{{ $index }}" class="form-control">
                    </td>
                    <td>
                        <input type="text" wire:model="mapel.{{ $index }}" class="form-control">
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

                @endforeach
                @foreach ($mapellist as $index => $m)
                <tr>
                    <td>
                        <input type="checkbox" wire:key="{{ $m->id }}" wire:model.live="mapel_selected_id"
                            value="{{ $m->id }}">
                    </td>

                    <td>{{ $m->id }}</td>
                    <td>
                        <form class="d-inline" onclick="return confirm('Apakah anda yakin akan menghapus data ini')"
                            action="mapel/{{ $m->id }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @if ($editmapelindex === $m->id)
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
                        @if ($editmapelindex===$m->id)
                        <input type="text" wire:model="editkode" class="form-control" value="{{ $m->kode }}">
                        @else
                        {{ $m->kode }}
                        @endif
                    </td>
                    <td>

                        @if ($editmapelindex===$m->id)
                        <input type="text" wire:model="editmapel" class="form-control">
                        @else
                        {{ $m->mapel }}
                        @endif
                    </td>
                    <td>
                        @if ($editmapelindex===$m->id)
                        <select class="custom-select col-md-8" wire:model="editjurusan">
                            @foreach ($jurusanlist as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $m->jurusan_id ? 'selected' : '' }}>{{
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
                        @if ($editmapelindex===$m->id)
                        <input type="text" wire:model="editket" class="form-control" value="{{ $m->ket }}">
                        @else
                        {{ $m->ket }}
                        @endif
                    </td>


                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $mapellist->links() }}
    </div>



    {{-- Modal --}}
    <div class="modal fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import File Data Mapel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('importmapel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="file">Import Mapel</label>
                            <input type="file" name="file" id="" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-download"></i>Import
                            Excel</button>
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



</div>