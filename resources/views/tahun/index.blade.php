<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Tahun Ajaran', 'href' => route('tahun.index')],
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        @livewire('tahun.data')
    </div>
</x-layout.layout>
