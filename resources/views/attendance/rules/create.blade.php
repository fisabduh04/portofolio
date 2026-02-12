<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Aturan Kehadiran', 'href' => route('attendance.rules.index')],
        ['name' => 'Tambah Aturan', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <form action="{{ route('attendance.rules.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <x-form.input name="name" label="Nama Aturan" placeholder="Contoh: Staff Kantor, Guru Piket" required />
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-form.input type="time" name="jam_masuk" label="Jam Masuk" required />
                    </div>
                    <div>
                        <x-form.input type="time" name="jam_pulang" label="Jam Pulang" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-form.input type="time" name="scan_masuk_start" label="Awal Scan Masuk" value="06:00" required />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jam paling pagi untuk mulai scan masuk.</p>
                    </div>
                    <div>
                        <x-form.input type="time" name="scan_pulang_end" label="Akhir Scan Pulang" value="18:00" required />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jam paling malam untuk scan pulang.</p>
                    </div>
                </div>

                <div>
                    <x-form.input type="number" name="toleransi_telat" label="Toleransi Keterlambatan (Menit)" value="15" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-form.input type="number" name="gaji_harian" label="Gaji Pokok Harian (Rp)" required />
                    </div>
                    <div>
                        <x-form.input type="number" name="bantuan_makan" label="Uang Makan Harian (Rp)" required />
                    </div>
                </div>

                <div>
                    <x-form.input type="number" name="denda_telat" label="Denda Keterlambatan (Rp)" value="0" required />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Potongan gaji jika scan masuk lewat dari Toleransi.</p>
                </div>

                <div>
                    <span class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hari Kerja</span>
                    <div class="flex flex-wrap gap-4">
                        @foreach(['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'] as $key => $label)
                        <div class="flex items-center">
                            <input id="day-{{ $key }}" type="checkbox" value="{{ $key }}" name="hari_kerja[]" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="day-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('attendance.rules.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout.layout>
