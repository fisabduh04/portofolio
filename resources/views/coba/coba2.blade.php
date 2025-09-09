<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => ''],
        ['name' => 'Users', 'href' => ''],
        ['name' => 'Jurusan', 'href' => ''],
    ]" />
    <div class="p-4 border-2 border-gray-200 rounded-lg dark:border-gray-700 mt-5">
        <div>
            <input type="text" placeholder="Masukkan Nama" class="border p-2 rounded mt-2">
            <button id="button" class="bg-blue-500 text-white p-2 rounded mt-2">Click Me</button>
        </div>
        <div class="mt-4" id="result">
            <span class="text-gray-700">You typed: </span>
        </div>
        <div>
            <input type="text" placeholder="Type something..." class="border p-2 rounded mt-2">
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const button = document.getElementById('button');
                button.addEventListener('click', function() {
                    alert('Button clicked!');
                });
            });


            document.addEventListener('DOMContentLoaded', function() {
                const input = document.querySelector('input[type="text"]');
                const result = document.getElementById('result').querySelector('span');


                input.addEventListener('input', function() {
                    result.textContent = `You typed: ${input.value}`;
                });
            });
        </script>

</x-layout.layout>
