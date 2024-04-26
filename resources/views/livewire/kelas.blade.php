<div>

    <form wire:submit.prevent="store">
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    @if ($kepala_table==true)
                    <tr>
                        <th>ID</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Action</th>
                    </tr>
                    @endif

                </thead>
                <tbody>
                    @foreach($kelas as $key => $val)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <input type="text" wire:model="kelas.{{ $key }}" class="form-control">
                        </td>

                        <td>
                            <select class="custom-select" wire:model="jurusan.{{ $key }}" name="jurusan[{{ $key }}]"
                                id="jurusan_{{ $key }}">
                                <option selected>Pilih Jurusan</option>
                                @foreach ($jurusanlist as $item)
                                <option value="{{ $item->id }}">{{ $item->jurusan }}</option>
                                @endforeach
                            </select>



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

    <table class="table table-hover mt-3">
        <thead>
            <th>No</th>
            <th>Aksi</th>
            <th>Kelas</th>
            <th>Jurusan</th>
        </thead>
        <tbody>
            @foreach ($kelasList as $key=>$k)
            <tr>
                <td>{{ $k->id }}</td>
                <td>
                    <button wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE"
                        wire:click.prevent="del({{ $k->id }})" class="btn btn-danger btn-sm"><i
                            class="fas fa-trash-alt"></i>del</button>
                @if ($editkelasindex===$k->id)
                <button class="btn btn-success btn-sm" wire:click="update({{ $k->id }})"><i class="fas fa-save "></i>Save</button>
                @else
                <button class="btn btn-primary btn-sm" wire:click="edit({{ $k->id }})"><i class="fas fa-pencil-alt "></i>Edit</button>
                @endif
                </td>
                <td>
                    @if ($editkelasindex=== $k->id)
                    <input type="text" wire:model="editkelas" class="form-control col-md-8" value="{{ $k->kelas }}">
                    @else
                    {{ $k->kelas }}
                    @endif
                </td>
                <td>
                    @if ($editkelasindex=== $k->id)
                    <select class="custom-select col-md-8" wire:model="editjurusan" >
                        @foreach ($jurusanlist as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $k->jurusan_id ? 'selected' : '' }}>{{ $item->jurusan }}</option>
                        @endforeach
                    </select>
                    @else
                        @if ($k->jurusan)
                        {{ $k->jurusan->jurusan }}
                        @else
                        Tidak Ada
                        @endif
                    @endif

            </tr>
            @endforeach
        </tbody>

    </table>


</div>
