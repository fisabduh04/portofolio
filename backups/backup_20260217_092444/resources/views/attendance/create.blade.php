<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Input Manual', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <form action="{{ route('attendance.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    @php
                        $pegawaiOptions = $pegawais->pluck('name', 'id')->toArray();
                    @endphp
                    <x-form.select name="pegawai_id" label="Pilih Pegawai" :options="$pegawaiOptions" placeholder="-- Pilih Pegawai --" required />
                </div>
                
                <div>
                    <x-form.input type="datetime-local" name="scan_time" label="Waktu Scan" :value="now()->format('Y-m-d\TH:i')" required />
                </div>

                <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('attendance.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Kembali</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Log</button>
                </div>
            </form>
            
            <div class="mt-8 bg-blue-50 text-blue-800 p-4 rounded-lg text-sm dark:bg-gray-700 dark:text-blue-300">
                <h4 class="font-bold mb-2">Informasi</h4>
                <p>Input manual ini akan langsung mentrigger perhitungan ulang absensi harian untuk pegawai tersebut pada tanggal yang dipilih.</p>
            </div>
        </div>
    </div>
</x-layout.layout>
