@props(['enabled' => true])
<section data-live-recap data-url="{{ request()->fullUrl() }}" data-enabled="{{ $enabled ? '1' : '0' }}">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
        <div>
            <p class="font-medium">Pembaruan otomatis lintas perangkat · setiap 5 detik</p>
            <p data-live-recap-status role="status" aria-live="polite" class="mt-1 text-xs">{{ $enabled ? 'Menyiapkan pembaruan…' : 'Pilih kelas dan tampilkan rekap untuk mengaktifkan pembaruan.' }}</p>
        </div>
        <button data-live-recap-toggle type="button" @disabled(!$enabled) class="rounded-lg border border-blue-300 px-3 py-2 font-medium disabled:opacity-50 dark:border-blue-700">Jeda pembaruan</button>
    </div>
    <div data-live-recap-content class="space-y-6">{{ $slot }}</div>
</section>
