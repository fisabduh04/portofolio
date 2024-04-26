<div>
    <div class="card-body table-responsive p-0 mt-1">
        <table class="table table-hover tabel-striped">
            <thead >
                <tr>
                    <th>No</th>
                    <th>Aksi</th>
                    <th>Kode</th>
                    <th>Mata Pelajaran</th>
                    <th>Jurusan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mapel as $index => $m)
                    <tr>
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
                                <button wire:click="store()" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-save"></i>
                                </button>
                            @else
                                <button wire:click="ubah({{ $m->id }})" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            @endif


                        </td>
                        <td>
                            {{ $m->kode }}
                        </td>
                        <td>{{ $m->mapel }}</td>
                        <td>{{ $m->jurusan->jurusan }}</td>
                        <td>
                            <div contenteditable="true">
                                {{ $m->ket }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
