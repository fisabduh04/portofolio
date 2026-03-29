<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? ($sekolah?->nama_sekolah ?? 'SISTEM INFORMASI SEKOLAH') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $logo ?? $sekolah?->logo_url ?? asset('img/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 antialiased text-gray-900 dark:text-gray-100">
    
    <main>
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>
