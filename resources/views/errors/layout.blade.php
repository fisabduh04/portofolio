<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white font-sans antialiased selection:bg-blue-500 selection:text-white">
    <main class="min-h-screen flex items-center justify-center p-6 lg:p-12 relative overflow-hidden">
        {{-- Decorative background elements (Glow effects) --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500/20 dark:bg-blue-600/10 rounded-full mix-blend-multiply filter blur-[100px] opacity-70"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-cyan-400/20 dark:bg-cyan-500/10 rounded-full mix-blend-multiply filter blur-[100px] opacity-70"></div>
            <div class="absolute top-[20%] right-[10%] w-[30%] h-[30%] bg-purple-500/20 dark:bg-purple-600/10 rounded-full mix-blend-multiply filter blur-[100px] opacity-70"></div>
        </div>

        <div class="relative z-10 w-full max-w-2xl text-center flex flex-col items-center">
            @yield('content')
            
            <footer class="mt-16 text-sm text-gray-500 dark:text-gray-400 font-medium tracking-wide">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Hak Cipta Dilindungi.
            </footer>
        </div>
    </main>
</body>
</html>
