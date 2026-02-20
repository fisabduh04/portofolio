<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Perizinan', 'href' => route('attendance.izin.index')],
        ['name' => 'Tambah', 'href' => '#']
    ]" />

    <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">Input Izin / Cuti Pegawai</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Masukkan data ketidakhadiran pegawai.</p>
        </div>
        
        <form action="{{ route('attendance.izin.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                     <label for="pegawai_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Pegawai</label>
                     <select name="pegawai_id" id="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawais as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                     </select>
                </div>
                
                <div>
                     <label for="jenis_izin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Izin</label>
                     <select name="jenis_izin" id="jenis_izin" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        <option value="Sakit">Sakit (Tetap Gaji, Tanpa Uang Makan)</option>
                        <option value="Izin">Izin (Potong Gaji & Uang Makan)</option>
                        <option value="Cuti">Cuti (Tetap Gaji, Tanpa Uang Makan)</option>
                        <option value="Dinas Luar">Dinas Luar (Full Payment)</option>
                     </select>
                </div>

                <div>
                    <label for="bukti_dokumen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload Bukti (Opsional)</label>
                    <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="bukti_dokumen" name="bukti_dokumen" type="file">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">JPG, PNG, PDF (Max. 2MB)</p>
                </div>

                <div>
                    <x-form.input type="date" label="Tanggal Mulai" name="tanggal_mulai" id="tanggal_mulai" required />
                </div>
                <div>
                     <x-form.input type="date" label="Tanggal Akhir" name="tanggal_akhir" id="tanggal_akhir" required />
                </div>
                
                <div class="sm:col-span-2">
                     <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan / Alasan</label>
                     <textarea id="keterangan" name="keterangan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Jelaskan alasan izin/sakit/cuti..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-btn type="submit" color="blue" icon="save">Simpan Data</x-btn>
                <x-btn type="a" href="{{ route('attendance.izin.index') }}" color="light">Batal</x-btn>
            </div>
        </form>
    </div>
    
    <script>
        // Simple script to auto-fill end date same as start date
        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            const start = this.value;
            const endInput = document.getElementById('tanggal_akhir');
            if(!endInput.value) {
                endInput.value = start;
            }
        });
    </script>
</x-layout.layout>
