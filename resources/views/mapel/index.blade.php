<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Mata Pelajaran', 'href' => route('mapel.index')]
    ]" />

    <div class="mt-5">
        @livewire('mapel.data')        
    </div>

</x-layout.layout>