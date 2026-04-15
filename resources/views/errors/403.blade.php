@extends('errors::layout')

@section('title', __('Forbidden'))

@section('content')
    {{-- Ikon Ilustrasi --}}
    <div class="mb-8 flex justify-center">
        <div class="relative w-24 h-24 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-yellow-600 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
    </div>

    {{-- Error Code --}}
    <h1 class="text-7xl lg:text-9xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-amber-600 drop-shadow-sm">
        403
    </h1>
    
    {{-- Pesan Manusia --}}
    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-gray-900 dark:text-white">
        Akses Ditolak
    </h2>
    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto">
        Maaf, Anda tidak memiliki izin untuk melihat halaman atau melakukan tindakan ini. Silakan hubungi Administrator jika ini adalah sebuah kesalahan.
    </p>
    
    {{-- Call to Action --}}
    <div>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-medium text-white transition-all duration-200 bg-yellow-500 border border-transparent rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-800 shadow-lg shadow-yellow-500/30">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
@endsection
