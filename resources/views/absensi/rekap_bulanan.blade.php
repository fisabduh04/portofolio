<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Rekapitulasi', 'href' => '#'],
                ['name' => 'Laporan Bulanan', 'href' => route('absensi.rekap-bulanan')],
            ]" />
            
</x-layout.layout>
