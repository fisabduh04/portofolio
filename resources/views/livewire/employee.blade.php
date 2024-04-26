<div class="container">
    @if ($errors->any())
    <div>
        <div class="pt-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if (session()->has('success'))
    <div class="pt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
    @endif
    <!-- START FORM -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <form>
            <div class="mb-3 row">
                <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" wire:model="nama">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" wire:model="email">
                </div>
            </div>
            <div class="mb-3 row">
                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" wire:model="alamat">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    @if ($updateData==true)
                    <button type="button" class="btn btn-primary" name="submit" wire:click="update()">Update</button>
                    @else
                    <button type="button" class="btn btn-primary" name="submit" wire:click="store()">Simpan</button>
                    @endif
                    <button type="button" id="hapus" class="btn btn-secondary" name="submit"
                        wire:click="clear()">Clear</button>
                </div>
            </div>
        </form>
    </div>
    <!-- AKHIR FORM -->

    <!-- START DATA -->
    <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h1>Data Pegawai</h1>

        <div class="p-3">
            <input type="text" class="form-control mb-1 w-25" wire:model.live="katakunci" placeholder="Search ...">
        </div>

        @if ($employee_selected_id)
        <a wire:click="delete_confirmation('')" class="btn btn-danger btn-sm" data-bs-toggle="modal"
            data-bs-target="#exampleModal" class="mb-3">Del {{ count($employee_selected_id) }} data</a>
        @endif


        <table class="table table-striped table-sortable">
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th class="col-md-1">No</th>
                    <th class="col-md-4 sort @if ($sortColumn=='nama'){{ $sortDirection }}                        
                    @endif" wire:click="sort('nama')">Nama</th>
                    <th class="col-md-3 sort @if ($sortColumn=='email'){{ $sortDirection }}                        
                    @endif" wire:click="sort('email')">Email</th>
                    <th class="col-md-2 sort @if ($sortColumn=='alamat'){{ $sortDirection }}                        
                    @endif" wire:click="sort('alamat')">Alamat</th>
                    <th class="col-md-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key=>$value)
                <tr>
                    <td><input wire:key="{{ $value->id }}" type="checkbox" value="{{ $value->id }}"
                            wire:model.live="employee_selected_id"></td>
                    <td id="data">{{ $data->firstItem()+$key }}</td>
                    <td>{{ $value->nama }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->alamat }}</td>
                    <td>
                        <a wire:click="edit({{ $value->id }})" class="btn btn-warning btn-sm">Edit</a>
                        <a wire:click="delete_confirmation({{$value->id}})" class="btn btn-danger btn-sm"
                            data-bs-toggle="modal" data-bs-target="#exampleModal">Del</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $data->links() }}

    </div>
    <!-- AKHIR DATA -->



    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakaha anda Yakin Data dengan nama : dengan nomer ID akan dihapus?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="delete()"
                        data-bs-dismiss="modal">Ya</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const p = document.querySelector('#hapus')
    p.textContent='Hapus'
</script>