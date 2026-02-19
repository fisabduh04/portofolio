<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Events', 'href' => route('attendance.events.index')],
        ['name' => 'Tambah', 'href' => '#']
    ]" />

    <div class="p-4 bg-white block border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-4">
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl dark:text-white">Tambah Event Baru</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Buat jadwal kegiatan khusus untuk pegawai.</p>
        </div>
        
        <form action="{{ route('attendance.events.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                     <x-form.input label="Nama Event" name="name" id="name" placeholder="Contoh: Rapat Dewan Guru" required />
                </div>
                <div>
                    <x-form.input type="date" label="Tanggal" name="date" id="date" required />
                </div>
                <div>
                    <x-form.input type="time" label="Jam Mulai" name="start_time" id="start_time" required />
                </div>
                <div>
                     <x-form.input type="time" label="Jam Selesai" name="end_time" id="end_time" required />
                </div>
                <div>
                     <x-form.input type="number" label="Bantuan Transport/Hadir (Rp)" name="bantuan_hadir" id="bantuan_hadir" value="0" />
                </div>
                <div class="flex items-center pt-8">
                    <input id="is_mandatory" name="is_mandatory" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="is_mandatory" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Wajib Hadir (Absen Alpha jika tidak hadir)</label>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Peserta Event</h3>
                    <div class="space-x-2">
                        <button type="button" onclick="selectAll(true)" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Pilih Semua</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" onclick="selectAll(false)" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Hapus Pilihan</button>
                    </div>
                </div>
                
                <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-900/50 dark:border-gray-700 max-h-60 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($pegawais as $pegawai)
                        <div class="flex items-center">
                            <input id="p_{{ $pegawai->id }}" name="participants[]" type="checkbox" value="{{ $pegawai->id }}" class="participant-checkbox w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="p_{{ $pegawai->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 truncate cursor-pointer">{{ $pegawai->nama }}</label> <!-- Changed name to nama -->
                        </div>
                        @endforeach
                    </div>
                </div>
                 @error('participants')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-btn type="submit" color="blue" icon="save">Simpan Event</x-btn>
                <x-btn type="a" href="{{ route('attendance.events.index') }}" color="light">Batal</x-btn>
            </div>
        </form>
    </div>

    <script>
        function selectAll(checked) {
            document.querySelectorAll('.participant-checkbox').forEach(c => c.checked = checked);
        }
    </script>
</x-layout.layout>
