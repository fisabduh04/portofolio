<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SMK Al-MIFTAH' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/almiftah.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- @vite('resources/css/app.css') --}}
    @livewireStyles
</head>
