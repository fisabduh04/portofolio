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



    <x-layout.footer />

</body>

</html>