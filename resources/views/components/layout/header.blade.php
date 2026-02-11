<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? ($sekolah->nama_sekolah ?? 'Sistem Informasi Sekolah') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $sekolah->logo ?? asset('img/logo.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    @stack('styles')
</head>
