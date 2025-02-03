<x-layout>
    <x-page-title title="Daftar Hadir Siswa" subtitle="" :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => '#'],
        ['label' => 'Daftar Hadir Siswa']
    ]" />

    <section class="section">
        <x-card title="Daftar Hadir Siswa">

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
                <div class="col-md-6 mb-4">
                    <h6>Hari</h6>
                    <select class="choices form-select" id="hari" name="hari">
                        <option>Hari</option>
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                        <option value="ahad">Ahad</option>
                    </select>
                </div>

                <div class="col-md-6 mb-4">
                    <h6>Kelas</h6>
                    <select class="choices form-select" id="kelas" name="kelas" datalist>
                        @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->kelas }}</option>

                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-4">
                    <h6>Jam</h6>
                    <select class="form-select" id="jam" name="jam" datalist>
                        @for ($i = 1; $i <= 4; $i++) { <option value="{{ $i}}">{{ $i }}</option>
                            }
                            @endfor
                    </select>
                </div>
                <div class="col-md-6 mb-4 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                            checked>
                        <label class="form-check-label" for="flexSwitchCheckChecked">Masuk Semua</label>
                    </div>
                </div>
            </div>
            <hr>


            <div class="table-responsive mx-1">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama & NRP</th>
                            <th class="d-none d-sm-table-cell">M</th>
                            <th class="d-none d-sm-table-cell">S</th>
                            <th class="d-none d-sm-table-cell">I</th>
                            <th class="d-none d-sm-table-cell">P</th>
                            <th class="d-none d-sm-table-cell">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswa as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $s->nama }}</strong><br>
                                <small>{{ $s->nipd }}</small>

                                <!-- Radio Buttons for Small Screens -->
                                <div class="d-block d-sm-none mt-2">
                                    <div class="d-flex justify-content-start">
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                                value="masuk" id="masuk_{{ $s->nipd }}">
                                            <label class="form-check-label" for="masuk_{{ $s->nipd }}">M</label>
                                        </div>
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                                value="sakit" id="sakit_{{ $s->nipd }}">
                                            <label class="form-check-label" for="sakit_{{ $s->nipd }}">S</label>
                                        </div>
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                                value="izin" id="izin_{{ $s->nipd }}">
                                            <label class="form-check-label" for="izin_{{ $s->nipd }}">I</label>
                                        </div>
                                        <div class="form-check mr-3">
                                            <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                                value="pulang" id="pulang_{{ $s->nipd }}">
                                            <label class="form-check-label" for="pulang_{{ $s->nipd }}">P</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                                value="alpha" id="alpha_{{ $s->nipd }}">
                                            <label class="form-check-label" for="alpha_{{ $s->nipd }}">A</label>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Radio Buttons for Larger Screens -->
                            <td class="d-none d-sm-table-cell">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                        value="masuk" id="masuk_{{ $s->nipd }}">
                                    <label class="form-check-label" for="masuk_{{ $s->nipd }}"></label>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                        value="sakit" id="sakit_{{ $s->nipd }}">
                                    <label class="form-check-label" for="sakit_{{ $s->nipd }}"></label>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                        value="izin" id="izin_{{ $s->nipd }}">
                                    <label class="form-check-label" for="izin_{{ $s->nipd }}"></label>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                        value="pulang" id="pulang_{{ $s->nipd }}">
                                    <label class="form-check-label" for="pulang_{{ $s->nipd }}"></label>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_{{ $s->nipd }}"
                                        value="alpha" id="alpha_{{ $s->nipd }}">
                                    <label class="form-check-label" for="alpha_{{ $s->nipd }}"></label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $siswa->links() }} --}}
            </div>




        </x-card>
    </section>
</x-layout>
<script src="assets/static/js/initTheme.js"></script>

<style>
    /* Styling radio buttons */
    .form-check-input {
        position: relative;
        appearance: none;
        -webkit-appearance: none;
        border: 2px solid #ddd;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        outline: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-input:checked::before {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .form-check-input[value="masuk"]:checked {
        border-color: blue;
        box-shadow: 0 0 0 4px rgba(0, 0, 255, 0.5);
    }

    .form-check-input[value="masuk"]:checked::before {
        background-color: blue;
    }

    .form-check-input[value="sakit"]:checked {
        border-color: yellow;
        box-shadow: 0 0 0 4px rgba(255, 255, 0, 0.5);
    }

    .form-check-input[value="sakit"]:checked::before {
        background-color: yellow;
    }

    .form-check-input[value="pulang"]:checked {
        border-color: orange;
        box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.5);
    }

    .form-check-input[value="pulang"]:checked::before {
        background-color: orange;
    }

    .form-check-input[value="izin"]:checked {
        border-color: green;
        box-shadow: 0 0 0 4px rgba(0, 255, 0, 0.5);
    }

    .form-check-input[value="izin"]:checked::before {
        background-color: green;
    }

    .form-check-input[value="alpha"]:checked {
        border-color: red;
        box-shadow: 0 0 0 4px rgba(255, 0, 0, 0.5);
    }

    .form-check-input[value="alpha"]:checked::before {
        background-color: red;
    }
</style>

@push('css')
<link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable.css') }}">
<link rel="stylesheet" href="{{ asset('assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/extensions/simple-datatables/style.css') }}">
<link rel="stylesheet" href="{{ asset('assets/compiled/css/table-datatable.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/static/js/pages/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/static/js/pages/simple-datatables.js') }}"></script>

@endpush