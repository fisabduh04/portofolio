<x-layout.layout>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Rekapitulasi', 'href' => '#'],
                ['name' => 'Laporan Tahunan', 'href' => route('absensi.rekap-tahunan')],
            ]" />
          
</x-layout.layout>
