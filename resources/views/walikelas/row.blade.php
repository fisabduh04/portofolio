<div data-assignment-row class="grid grid-cols-1 items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 lg:grid-cols-12 dark:border-gray-600 dark:bg-gray-800">
    <div class="lg:col-span-3">
        <label for="kelas-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
        <select id="kelas-{{ $index }}" name="penugasans[{{ $index }}][kelas_id]" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="">Pilih Kelas</option>
            @foreach ($kelas as $item)
                <option value="{{ $item->id }}" @selected(($row['kelas_id'] ?? '') == $item->id)>{{ $item->kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="lg:col-span-3">
        <label for="pegawai-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Wali Kelas</label>
        <select id="pegawai-{{ $index }}" name="penugasans[{{ $index }}][pegawai_id]" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="">Pilih Pegawai</option>
            @foreach ($pegawai as $item)
                <option value="{{ $item->id }}" @selected(($row['pegawai_id'] ?? '') == $item->id)>{{ $item->name }}{{ $item->nuptk ? ' — '.$item->nuptk : '' }}</option>
            @endforeach
        </select>
    </div>
    <div class="lg:col-span-2">
        <label for="status-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status</label>
        <select id="status-{{ $index }}" name="penugasans[{{ $index }}][is_active]" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="1" @selected(($row['is_active'] ?? '1') == '1')>Aktif</option>
            <option value="0" @selected(($row['is_active'] ?? '1') == '0')>Nonaktif</option>
        </select>
    </div>
    <div class="lg:col-span-3">
        <label for="keterangan-{{ $index }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
        <textarea id="keterangan-{{ $index }}" name="penugasans[{{ $index }}][keterangan]" maxlength="2000" rows="2" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ $row['keterangan'] ?? '' }}</textarea>
    </div>
    <button type="button" data-remove-row class="rounded-lg px-2 py-2 text-sm font-medium text-red-600 hover:bg-red-50 lg:mt-8 dark:text-red-400 dark:hover:bg-gray-700" aria-label="Hapus baris penugasan">Hapus</button>
</div>
