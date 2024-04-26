<div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aksi</th>
                    <th>Kode Jurusan</th>
                    <th>Jurusan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jurusan as $index => $k)
                <tr>
                    <td wire:key="{{ $k->id }}">{{ $k->id }}</td>
                    <td>
                        <button wire:click.prevent="del({{ $k->id }})"
                            wire:confirm.prompt="Apakah Anda yakin akan menghapus data ini?\n\nType DELETE to confirm|DELETE"
                            class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>del
                        </button>
                        @if ($editStudentIndex === $k->id)
                        <button class="btn btn-success btn-sm" wire:click.prevent="update({{ $k->id}})">
                            <i class="fas fa-save"></i>Save</button>
                        @else
                        <button class="btn btn-primary btn-sm" wire:click.prevent="editStudent({{ $k->id }})">
                            <i class="fas fa-edit"></i>Edit</button>
                        @endif
                    </td>
                    <td>
                        @if ($editStudentIndex === $k->id)
                        <input type="text" wire:model="kode" class="form-control col-md-6">
                        @else
                        {{ $k->kode }}
                        @endif
                    </td>
                    <td>
                        @if ($editStudentIndex === $k->id)
                        <input type="text" wire:model="jurusan1" class="form-control col-sm-10">
                        @else
                        {{ $k->jurusan }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
