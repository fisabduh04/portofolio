<div data-assignment-row class="repeater-row grid grid-cols-1 md:grid-cols-12 gap-4 items-start bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700 group">
    <div class="min-w-0 md:col-span-4">
        <label for="pegawai-{{ $index }}" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:sr-only">Wali Kelas</label>
        <select id="pegawai-{{ $index }}" name="{{ $isEditing ? 'pegawai_id' : 'penugasans['.$index.'][pegawai_id]' }}" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 py-0 pl-3 pr-9 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            <option value="">Pilih Pegawai</option>
            @foreach ($pegawai as $item)
                <option value="{{ $item->id }}" @selected(($row['pegawai_id'] ?? '') == $item->id)>{{ $item->name }}{{ $item->nuptk ? ' - '.$item->nuptk : '' }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-0 md:col-span-2">
        <label for="kelas-{{ $index }}" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:sr-only">Kelas</label>
        <select id="kelas-{{ $index }}" name="{{ $isEditing ? 'kelas_id' : 'penugasans['.$index.'][kelas_id]' }}" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 py-0 pl-3 pr-9 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            <option value="">Pilih Kelas</option>
            @foreach ($kelas as $item)
                <option value="{{ $item->id }}" @selected(($row['kelas_id'] ?? '') == $item->id)>{{ $item->kelas }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-0 md:col-span-2">
        <label for="status-{{ $index }}" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:sr-only">Status</label>
        <select id="status-{{ $index }}" name="{{ $isEditing ? 'is_active' : 'penugasans['.$index.'][is_active]' }}" required class="block h-11 w-full rounded-lg border border-gray-300 bg-gray-50 py-0 pl-3 pr-9 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            <option value="1" @selected(($row['is_active'] ?? '1') == '1')>Aktif</option>
            <option value="0" @selected(($row['is_active'] ?? '1') == '0')>Nonaktif</option>
        </select>
    </div>
    <div class="min-w-0 md:col-span-3">
        <label for="keterangan-{{ $index }}" class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300 md:sr-only">Keterangan</label>
        <textarea id="keterangan-{{ $index }}" name="{{ $isEditing ? 'keterangan' : 'penugasans['.$index.'][keterangan]' }}" maxlength="2000" rows="1" placeholder="Catatan (opsional)" class="block h-11 min-h-11 w-full resize-y rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">{{ $row['keterangan'] ?? '' }}</textarea>
    </div>
    <button type="button" data-remove-row @if ($isEditing) style="display: none" @endif class="md:col-span-1 flex h-11 w-10 items-center justify-center justify-self-end rounded-lg text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 focus-visible:outline-2 focus-visible:outline-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400" aria-label="Hapus baris penugasan" title="Hapus baris">
        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3" />
        </svg>
    </button>
</div>
