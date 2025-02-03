<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Mata Pelajaran', 'href' => '']
    ]" />

    <div class="p-4 mt-5 border-2 border-gray-200 rounded-lg dark:border-gray-700">
        <@livewire('Pelajaran.data') </div>

</x-layout.layout>


{{-- <h1>Ayam goreng</h1> --}}