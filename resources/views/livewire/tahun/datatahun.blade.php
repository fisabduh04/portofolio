<div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">TAHUN AJARAN SMK AL-MIFTAH</h3>
                </div>
                <div class="card-body">
                    @if($form)
                    <form wire:submit.prevent="store()">
                        <div class="form-row">
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tahun">Tahun Ajaran:</label>
                                <input type="text" wire:model="tahun" class="form-control" id="tahun" name="tahun" placeholder="Tahun Ajaran" required>
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="semester">Semester:</label>
                                <select class="form-control" id="semester" name="semester" wire:model="semester" >
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tanggalmulai">Tanggal Mulai:</label>
                                <input type="date" class="form-control" id="tanggalmulai" wire:model="tanggalmulai" name="tanggalmulai" placeholder="Tanggal Mulai">
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tanggalakhir">Tanggal Akhir:</label>
                                <input type="date" class="form-control" id="tanggalakhir" wire:model="tanggalakhir"  name="tanggalakhir" placeholder="Tanggal Akhir">
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="isActive">Status:</label>
                                <select class="form-control" id="isActive" name="isActive">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-10 col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success mt-2 "><i class="fas fa-save"></i>{{$tombol_simpan}}</button>
                                
                                <button type="button" class="btn btn-secondary mt-2 ml-2" wire:click="close()"><i class="fas fa-close"></i>Close</button>
                            
                            </div>
                        </div>
                    </form>
                    @endif
                @if(!$form)
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <button type="button" wire:click="tambah()" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Tambah</button>                    
                    </div>
                </div>
                @endif

                </div>
                
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tahunlist as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>                                    
                                    <a class="btn btn-outline-info btn-sm border-0" wire:click="edit({{ $t->id }})">
                                        
                                        <i class="fas fa-pencil-alt"></i></a>

                                    <form action="{{ route('tahun.destroy', $t->id) }}" method="POST" class="d-inline">                                      
                                        @csrf
                                        @method('delete')
                                        <button type="submit" onclick="return confirm('Apakah anda akan menghapus data ini?')" class="btn btn-outline-danger btn-sm rounded-circle border-0"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $t->tahun }}</td>
                            <td>{{ $t->semester }}</td>
                            <td>{{ $t->tanggalmulai }}</td>
                            <td>{{ $t->tanggalakhir }}</td>
                            <td>
                                @if($t->isActive)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">Non Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
