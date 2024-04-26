<div>
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
                    @foreach($kode as $key => $val)
                    <tr>
                        <td>{{ $key}}</td>
                        <td>
                            <input type="text" wire:model="kode.{{ $key }}" class="form-control">
                            {{ $val }}
                        </td>
                        <td>
                            <input type="text" wire:model="jurusan.{{ $key }}" class="form-control">
                        </td>

                        <td>
                            <button type="button" wire:click="remove({{ $key }})" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash-alt"></i>Del</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" wire:click="add" class="btn btn-success">Tambah</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>

    <table class="table table-hover mt-2">
        <thead>
            <th>No</th>
            <th>Aksi</th>
            <th>Kode</th>
            <th>Jurusan</th>
        </thead>
        <tbody>
            @foreach ($Listjurusan as $key=>$k)
            <tr>
                <td>{{ $k->id }}</td>
                <td>
                    <button wire:click.prevent="del({{ $k->id }})"
                        wire:confirm.prompt="Apakah Anda yakin akan menghapus data ini?\n\nType DELETE to confirm|DELETE"
                        class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i>del
                    </button>
                    @if ($editStudentIndex === $key)

                    <button class="btn btn-success btn-sm" wire:click.prevent="editStudent({{ $key }})">
                        <i class="fas fa-save"></i>Save</button>
                    @else
                    <button class="btn btn-primary btn-sm" wire:click="editStudent({{ $key }})">
                        <i class="fas fa-edit"></i>Edit</button>
                    @endif
                </td>
                <td>
                    @if ($editStudentIndex === $key)
                    <input type="text" wire:model="Listjurusan.{{ $key }}.kode" class="form-control">
                    @else
                    {{ $k->kode }}
                    @endif
                </td>
                <td>{{ $k->jurusan }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>


</div>
