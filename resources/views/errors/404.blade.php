@extends('errors::layout')

@section('title', __('Not Found'))

@section('content')
    {{-- Ikon Ilustrasi --}}
    <div class="mb-8 flex justify-center">
        <div class="relative w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
            </svg>
            <div class="absolute -top-1 -right-1 w-8 h-8 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center">
                <span class="text-blue-600 dark:text-blue-400 font-bold text-lg">?</span>
            </div>
        </div>
    </div>

    {{-- Error Code --}}
    <h1 class="text-7xl lg:text-9xl font-extrabold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 drop-shadow-sm">
        404
    </h1>
    
    {{-- Pesan Manusia --}}
    <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-gray-900 dark:text-white">
        Ups! Anda Tersesat
    </h2>
    <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-lg mx-auto">
        Halaman yang Anda cari sepertinya tidak ada, telah dihapus, atau Anda salah memasukkan alamat URL.
    </p>
    
    {{-- Call to Action --}}
    <div>
        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3.5 text-base font-medium text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-600/30">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
    </div>
@endsection
