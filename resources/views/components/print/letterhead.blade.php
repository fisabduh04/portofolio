@props([
    'sekolah' => null,
    'logo' => null,
    'size' => 'md', // md, lg
])

@php
    $titleSize = $size === 'lg' ? 'text-2xl' : 'text-xl';
    $logoSize = $size === 'lg' ? 'w-20 h-20' : 'w-16 h-16';
@endphp

<div class="flex items-center gap-4 mb-4 border-b-4 border-double border-black pb-2">
    <img
        src="{{ $logo ?? asset('img/logo.png') }}"
        class="{{ $logoSize }} object-contain"
        alt="Logo"
    >
    <div class="flex-1 text-center">
        <h1 class="{{ $titleSize }} font-bold uppercase tracking-wider">
            {{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}
        </h1>
        <p class="text-sm font-semibold">
            {{ $sekolah->alamat ?? $sekolah->alamat_sekolah ?? 'Alamat Sekolah' }}
        </p>
        <p class="text-sm">
            Email: {{ $sekolah->email ?? $sekolah->email_sekolah ?? '-' }}
            | Telp: {{ $sekolah->no_telp ?? $sekolah->telp_sekolah ?? '-' }}
        </p>
    </div>
</div>

