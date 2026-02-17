<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Tahun Akademik', 'href' => route('tahun.index')],
    ]" />

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm">
        <div class="p-6">
            @livewire('tahun.data')
        </div>
    </div>
</x-layout.layout>
