<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Jadwal Khusus', 'href' => route('attendance.overrides.index')],
        ['name' => 'Tambah', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white mb-6">Tambah Jadwal Khusus</h1>
            
            <form action="{{ route('attendance.overrides.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-form.select name="pegawai_id" label="Pegawai" required>
                            <option value="">Pilih Pegawai</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <div>
                        <x-form.input type="date" name="date" label="Tanggal" required />
                    </div>
                    <div>
                        <x-form.select name="attendance_rule_id" label="Rule / Aturan yang Berlaku" required>
                            <option value="">Pilih Aturan</option>
                            @foreach($rules as $rule)
                                <option value="{{ $rule->id }}">{{ $rule->name }} ({{ $rule->jam_masuk }} - {{ $rule->jam_pulang }})</option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <div>
                        <x-form.input name="reason" label="Alasan / Catatan" />
                    </div>
                </div>
        
                <div class="mt-6 flex justify-end space-x-2">
                    <a href="{{ route('attendance.overrides.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layout.layout>
