<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Events', 'href' => route('attendance.events.index')],
        ['name' => 'Buat Event', 'href' => '']
    ]" />

    <x-layout.sidebar.sub-menu :items="[
        ['name' => 'Dashboard', 'href' => route('attendance.index')],
        ['name' => 'Laporan Bulanan', 'href' => route('attendance.report')],
        ['name' => 'Laporan Individu', 'href' => route('attendance.report.employee')],
        ['name' => 'Aturan & Jam Kerja', 'href' => route('attendance.rules.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
        ['name' => 'Events', 'href' => route('attendance.events.index'), 'active' => true],
        ['name' => 'Konfigurasi', 'href' => route('attendance.setting')],
        ['name' => 'Payroll', 'href' => route('attendance.payroll.index')]
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Event Khusus</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftarkan kegiatan sekolah dan otomatisasi absensi peserta.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <form action="{{ route('attendance.events.store') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <x-form.input label="Nama Kegiatan" name="name" placeholder="Contoh: Rapat Kerja Awal Semester" required />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form.input label="Tanggal" name="date" type="date" required />
                                <div class="grid grid-cols-2 gap-2">
                                    <x-form.input label="Waktu Mulai" name="start_time" type="time" value="07:00" required />
                                    <x-form.input label="Waktu Selesai" name="end_time" type="time" value="14:00" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form.input label="Uang Saku / Bantuan Hadir" name="bantuan_hadir" type="number" placeholder="0" value="0" required />
                                <div class="flex items-center pt-7">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_mandatory" value="1" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Wajib Hadir</span>
                                    </label>
                                </div>
                            </div>

                            <x-form.textarea label="Keterangan Tambahan" name="description" placeholder="Informasi detail mengenai agenda kegiatan..." />

                            {{-- Participants --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Pilih Peserta</h3>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-xs text-gray-500 uppercase font-semibold">Daftar Pegawai Aktif</span>
                                        <div class="flex gap-3">
                                            <button type="button" onclick="selectAll()" class="text-xs text-blue-600 hover:underline font-medium">Pilih Semua</button>
                                            <button type="button" onclick="deselectAll()" class="text-xs text-gray-500 hover:underline font-medium">Reset</button>
                                        </div>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($pegawais as $p)
                                        <label class="flex items-center p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input type="checkbox" name="participants[]" value="{{ $p->id }}" class="participant-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                            <span class="ml-2.5 text-sm text-gray-700 dark:text-gray-300">{{ $p->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('attendance.events.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:underline">Batal</a>
                                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 rounded-base focus:ring-4 focus:ring-blue-300">
                                    Simpan Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-5 rounded-lg">
                        <h4 class="text-blue-700 dark:text-blue-400 font-semibold mb-3 text-sm">Informasi Sistem</h4>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <p>Sistem akan secara otomatis menghitung absensi peserta berdasarkan log fingerprint pada tanggal kegiatan.</p>
                            <p>Jika peserta <span class="font-semibold text-blue-600">Wajib Hadir</span> namun tidak melakukan scan, sistem akan menandai sebagai <span class="text-red-600 font-semibold">ALPHA</span>.</p>
                            <p>Bantuan hadir akan ditambahkan ke total pendapatan harian pegawai di laporan penggajian.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function selectAll() {
        document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = true);
    }
    function deselectAll() {
        document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = false);
    }
</script>
</x-layout.layout>
