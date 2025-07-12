<!DOCTYPE html>
<html lang="en">


<x-layout.header />

<body>
    @livewire('toast')




    <x-layout.navbar />
    <x-layout.sidebar />
    <div class="p-4 sm:ml-64">

        {{ $slot }}
    </div>


    @livewireScripts
    <x-layout.footer />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
</body>

</html>
