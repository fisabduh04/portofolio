<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Tahun Akademik', 'href' => '']
    ]" />

    <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-5">
        {{-- <div class="flex items-center justify-center h-48 mb-4 rounded bg-gray-50 dark:bg-gray-800"> --}}
            @livewire('tahun.data')
            {{-- </div> --}}
    </div>

</x-layout.layout>
