<!DOCTYPE html>
<html lang="en">


<x-layout.header />

<body>
    <x-layout.toast />




    <x-layout.navbar />
    <x-layout.sidebar />
    <div class="p-4 sm:ml-64 mt-12">

        {{ $slot }}
    </div>


    @livewireScripts
    <x-layout.footer />
    <x-modal.confirm />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
