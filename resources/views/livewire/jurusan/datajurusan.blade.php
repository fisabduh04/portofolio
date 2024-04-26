<div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <form wire:submit.prevent="store">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                @if ($kepala_table)
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Jurusan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                @endif
                <tbody>
                    @foreach($kode as $index => $val)
                    <tr>
                        <td>{{ $index }}</td>
                        <td>
                            <input type="text" wire:model="kode.{{ $index }}" class="form-control">
                        </td>
                        <td>
                            <input type="text" wire:model="jurusan.{{ $index }}" class="form-control">
                        </td>

                        <td>
                            <button type="button" wire:click="remove({{ $index }})" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash-alt"></i> Del</button>
                        </td>
                    </tr>

                    @endforeach

                </tbody>
            </table>
            <button type="button" wire:click="add" class="btn btn-success">Tambah</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-hover mt-2">
        <thead>
            <th>No</th>
            <th>Aksi</th>
            <th>Kode</th>
            <th>Jurusan</th>
        </thead>
        <tbody>
            @foreach ($semuajurusan as $key=>$k)
            <tr>
                <td>{{ $k->id }}</td>
                <td>
                    <button wire:click.prevent="del({{ $k->id }})"
                        wire:confirm.prompt="Apakah Anda yakin akan menghapus data ini?\n\nType DELETE to confirm|DELETE"
                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>del
                    </button>
                    @if ($editStudentIndex === $k->id)

                    <button class="btn btn-success btn-sm" wire:click.prevent="update({{ $k->id }})">
                        <i class="fas fa-save"></i>Save</button>
                    @else
                    <button class="btn btn-primary btn-sm" wire:click="editStudent({{ $k->id }})">
                        <i class="fas fa-edit"></i>Edit</button>
                    @endif
                </td>
                <td>
                    @if ($editStudentIndex === $k->id)
                    <input type="text" wire:model="editkode" class="form-control col-md-8 ">
                    @else
                    {{ $k->kode }}
                    @endif
                </td>
                <td>
                    @if ($editStudentIndex === $k->id)
                    <input type="text" wire:model="editjurusan" class="form-control ">
                    @else
                    {{ $k->jurusan }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>

@push('js')
{{-- toastr for livewire --}}


@endpush

</div>


