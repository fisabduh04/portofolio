<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Akademik', 'href' => '#'],
        ['name' => 'Kelas', 'href' => route('kelas.index')]
    ]" />

    <div class="mt-2">
        @livewire('kelas.data')
    </div>
</x-layout.layout>
