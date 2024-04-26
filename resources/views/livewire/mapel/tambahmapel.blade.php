<div>
    <form wire:submit.prevent="store">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                @if ($kepala_table)
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Mapel</th>
                        <th>Jurusan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                @endif
                <tbody>
                    @foreach($mapel as $index => $val)
                    <tr>
                        <td>{{ $index }}</td>
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

                        <td>
                            <button type="button" wire:click="remove({{ $index }})" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash-alt"></i> </button>
                        </td>
                    </tr>

                    @endforeach

                </tbody>
            </table>
            <button type="button" wire:click="add" class="btn btn-success">Tambah</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>

            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-default">
                Import Excel
             </button>
            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#modal-default">
                Export Excel
             </button>
        </div>
    </form>


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
              <form action="{{ route('importmapel') }}" method="POST" enctype="multipart/form-data" >
                @csrf
                <div class="form-group mb-3">
                    <label for="file">Import Mapel</label>
                    <input type="file" name="file" id="" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Import Excel</button>
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
      <hr>


      {{-- @dd($mapel_id) --}}
      <div class="card-body table-responsive p-0 mt-1">
        <table class="table table-hover tabel-striped">
            <thead >
                <tr>
                    <th style="width: 2%;"></th>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 10%;">Aksi</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 30%;">Mapel</th>
                    <th style="width: 25%;">Jurusan</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mapellist as $index => $m)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{{ $m->id }}</td>
                        <td>
                            <form class="d-inline" onclick="return confirm('Apakah anda yakin akan menghapus data ini')" action="mapel/{{ $m->id }}" method="POST">
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
                            <input type="text" wire:model="editmapel" class="form-control" value="{{ $m->mapel }}">
                        @else
                        {{ $m->mapel }}
                        @endif
                        </td>
                        <td>
                            @if ($editmapelindex===$m->id)
                            <select class="custom-select col-md-8" wire:model="editjurusan" >
                                @foreach ($jurusanlist as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $m->jurusan_id ? 'selected' : '' }}>{{ $item->jurusan}}</option>

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
    </div>
</div>
