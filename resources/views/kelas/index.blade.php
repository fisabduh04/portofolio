<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Kelas', 'href' => '']
    ]" />

    <div class="mt-2">
        @livewire('kelas.data')
    </div>
</x-layout.layout>
