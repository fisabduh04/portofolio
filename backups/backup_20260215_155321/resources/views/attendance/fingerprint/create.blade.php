<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Mesin Fingerprint', 'href' => route('attendance.fingerprint.index')],
        ['name' => 'Tambah Mesin', 'href' => '']
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm mt-4">
        <div class="p-4 sm:p-6">
            <form action="{{ route('attendance.fingerprint.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <x-form.input name="name" label="Nama Mesin" placeholder="Contoh: Mesin Lobby, Mesin Pos 1" required />
                
                <div class="grid grid-cols-2 gap-4">
                    <x-form.input name="ip_address" label="IP Address" placeholder="192.168.1.201" required />
                    <x-form.input type="number" name="port" label="Port" value="4370" required />
                </div>
                
                <x-form.input type="number" name="comkey" label="Comkey / Password" value="0" required />
                
                <x-form.input name="location" label="Lokasi" placeholder="Lantai 1, Depan Lift" />

                <div class="flex items-center">
                    <input id="status" type="checkbox" name="status" value="1" checked class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="status" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Status Aktif</label>
                </div>

                <div class="flex items-center justify-end space-x-2">
                    <a href="{{ route('attendance.fingerprint.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Batal</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Simpan Mesin</button>
                </div>
            </form>
        </div>
    </div>
</x-layout.layout>
