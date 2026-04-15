@extends('errors::layout')

@section('title', __('Service Unavailable'))

@section('content')
    {{-- Ikon Ilustrasi --}}
    <div class="mb-8 flex justify-center">
        <div class="relative w-24 h-24 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-purple-600 dark:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
    </div>

    {{-- Error Code --}}
    <h1 class="text-7xl lg:text-9xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-500 drop-shadow-sm">
        503
    </h1>
    
    {{-- Pesan Manusia --}}
    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-gray-900 dark:text-white">
        Sedang Dalam Perbaikan
    </h2>
    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto">
        Tunggu sebentar! Kami sedang melakukan pemeliharaan rutin untuk meningkatkan kualitas layanan. Harap kembali beberapa saat lagi.
    </p>
    
    {{-- Call to Action --}}
    <div>
        <button onclick="window.location.reload()" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-medium text-white transition-all duration-200 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800 shadow-lg shadow-purple-600/30">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Muat Ulang Halaman
        </button>
    </div>
@endsection
