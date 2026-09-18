<div class="repeater-row grid grid-cols-1 md:grid-cols-12 gap-4 items-start bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700 group">
    <!-- Siswa -->
    <div class="md:col-span-5">
        <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Siswa</label>
        <x-form.searchable-select
            :restore-old-input="false"
            id="siswa-{{ $rowKey }}"
            name="siswa_id[]"
            aria-label="Nama siswa"
            placeholder="Pilih Siswa"
            search-placeholder="Cari nama atau NIPD siswa..."
            required
        >
            @foreach ($siswa as $s)
                <option value="{{ $s->id }}">{{ $s->nama }} - {{ $s->nipd }}</option>
            @endforeach
        </x-form.searchable-select>
    </div>
    
    <!-- Kelas -->
    <div class="md:col-span-4">
        <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Kelas</label>
        <x-form.searchable-select
            :restore-old-input="false"
            id="kelas-{{ $rowKey }}"
            name="kelas_id[]"
            aria-label="Kelas"
            placeholder="Pilih Kelas"
            search-placeholder="Cari kelas..."
            required
        >
            @foreach ($kelas as $k)
                <option value="{{ $k->id }}">{{ $k->kelas }}</option>
            @endforeach
        </x-form.searchable-select>
    </div>

    <!-- Keterangan -->
    <div class="md:col-span-2">
        <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:hidden">Status</label>
        <select name="ket[]" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="aktif">Aktif</option>
            <option value="do">Berhenti</option>
            <option value="naik">Naik Kelas</option>
            <option value="tinggal">Tidak Naik Kelas</option>
        </select>
    </div>

    <!-- Delete Button -->
    <div class="md:col-span-1 flex justify-center items-center h-full pt-1">
        <button type="button" onclick="removeRow(this)" class="text-gray-400 hover:text-red-500 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all opacity-100 md:opacity-0 md:group-hover:opacity-100" title="Hapus Baris">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        </button>
    </div>
</div>
