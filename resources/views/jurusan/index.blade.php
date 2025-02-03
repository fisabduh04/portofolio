<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jurusan', 'href' => '']
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Jurusan</h1>
        @livewire('jurusan.data')
    </div>

</x-layout.layout>