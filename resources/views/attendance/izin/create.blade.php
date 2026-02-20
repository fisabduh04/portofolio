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
        
        <form action="{{ route('attendance.izin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                     <x-form.select name="pegawai_id" label="Nama Pegawai" :options="$pegawais" placeholder="-- Pilih Pegawai --" required />
                </div>
                
                <div>
                     <x-form.select name="jenis_izin" label="Jenis Izin" :options="[
                        'Sakit' => 'Sakit (Tetap Gaji, Tanpa Uang Makan)',
                        'Izin' => 'Izin (Potong Gaji & Uang Makan)',
                        'Cuti' => 'Cuti (Tetap Gaji, Tanpa Uang Makan)',
                        'Dinas Luar' => 'Dinas Luar (Full Payment)'
                     ]" placeholder="-- Pilih Jenis Izin --" required />
                </div>

                <div>
                    <x-form.input type="file" name="bukti_dokumen" label="Upload Bukti (Opsional)" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">JPG, PNG, PDF (Max. 2MB)</p>
                </div>

                <div>
                    <x-form.input type="date" label="Tanggal Mulai" name="tanggal_mulai" id="tanggal_mulai" required />
                </div>
                <div>
                     <x-form.input type="date" label="Tanggal Akhir" name="tanggal_akhir" id="tanggal_akhir" required />
                </div>
                
                <div class="sm:col-span-2">
                     <x-form.textarea name="keterangan" label="Keterangan / Alasan" rows="3" placeholder="Jelaskan alasan izin/sakit/cuti..." />
                </div>
            </div>

            <div class="flex items-center gap-3">
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
