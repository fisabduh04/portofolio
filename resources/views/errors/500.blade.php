@extends('errors::layout')

@section('title', __('Server Error'))

@section('content')
    {{-- Ikon Ilustrasi --}}
    <div class="mb-8 flex justify-center">
        <div class="relative w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
    </div>

    {{-- Error Code --}}
    <h1 class="text-7xl lg:text-9xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 drop-shadow-sm">
        500
    </h1>
    
    {{-- Pesan Manusia --}}
    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-gray-900 dark:text-white">
        Waduh! Server Sedang Pusing
    </h2>
    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto">
        Maaf, sistem kami mengalami kendala teknis saat memproses permintaan Anda. Kami sudah mencatat masalah ini dan sedang berusaha memperbaikinya secepat mungkin.
    </p>
    
    {{-- Call to Action --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-medium text-white transition-all duration-200 bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-600/30 w-full sm:w-auto">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
        <button onclick="window.location.reload()" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-medium text-red-700 transition-all duration-200 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:text-red-800 focus:outline-none focus:ring-4 focus:ring-red-100 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 dark:hover:bg-gray-700 dark:hover:text-red-300 dark:focus:ring-gray-700 w-full sm:w-auto">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Coba Muat Ulang
        </button>
    </div>
@endsection
