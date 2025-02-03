<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jurusan', 'href' => '']
    ]" />

    <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-5">
        {{--
        <livewire:jurusan.data1 /> --}}
        @livewire('tahun.data')
    </div>

</x-layout.layout>
