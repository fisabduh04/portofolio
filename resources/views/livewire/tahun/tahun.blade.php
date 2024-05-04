<div>
<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">TAHUN AJARAN SMK AL-MIFTAH</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('tahun.store') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tahun">Tahun Ajaran:</label>
                                <input type="text" class="form-control" id="tahun" name="tahun" placeholder="Tahun Ajaran" required>
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="semester">Semester:</label>
                                <select class="form-control" id="semester" name="semester">
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tanggalmulai">Tanggal Mulai:</label>
                                <input type="date" class="form-control" id="tanggalmulai" name="tanggalmulai" placeholder="Tanggal Mulai">
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="tanggalakhir">Tanggal Akhir:</label>
                                <input type="date" class="form-control" id="tanggalakhir" name="tanggalakhir" placeholder="Tanggal Akhir">
                            </div>
                            <div class="form-group col-sm-10 col-md-3">
                                <label for="isActive">Status:</label>
                                <select class="form-control" id="isActive" name="isActive">
                                    <option value="1">Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-10 col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                            </div>
                        </div>
                    </form>
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
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    
                                    <a class="btn btn-outline-info btn-sm border-0 edit-btn" data-id="{{ $t->id }}">
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
