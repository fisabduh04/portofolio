@props([
    'text' => null,           // Teks tombol
    'type' => 'button',       // Tipe tombol (submit, button, dll)
    'color' => 'blue',        // Warna dasar (blue, green, red, dll)
    'icon' => null,           // Nama ikon dari array $svgIcons
    'wireClick' => null,      // Livewire click action
    'href' => null,           // Link jika tombol berfungsi sebagai jangkar (anchor)
    'for' => null,            // ID input jika tombol berfungsi sebagai label
    'size' => 'md',           // Ukuran: xs, sm, md, lg, xl
    'iconPosition' => 'left', // Posisi ikon: left atau right
    'disabled' => false,      // Status non-aktif
    'outline' => false,       // Varian outline (garis tepi)
    'pill' => false,          // Varian bulat sempurna (pill)
    'rounded' => 'rounded-base', // Sudut tumpul default sesuai standar Flowbite
])

@php
    // Menentukan apakah ini tombol 'Hanya Ikon'
    $isIconOnly = empty($text) && $icon;

    // Mapping ukuran sesuai standar Flowbite (padding biasa vs padding ikon)
    $sizeSpecs = [
        'xs' => [
            'text' => 'text-xs',
            'padding' => 'px-3 py-2',
            'iconPadding' => 'p-2',
            'svg' => 'w-3 h-3'
        ],
        'sm' => [
            'text' => 'text-sm',
            'padding' => 'px-3 py-2',
            'iconPadding' => 'p-2',
            'svg' => 'w-3.5 h-3.5'
        ],
        'md' => [
            'text' => 'text-sm',
            'padding' => 'px-5 py-2.5',
            'iconPadding' => 'p-2.5',
            'svg' => 'w-4 h-4'
        ],
        'lg' => [
            'text' => 'text-base',
            'padding' => 'px-5 py-3',
            'iconPadding' => 'p-3',
            'svg' => 'w-5 h-5'
        ],
        'xl' => [
            'text' => 'text-base',
            'padding' => 'px-6 py-3.5',
            'iconPadding' => 'p-3.5',
            'svg' => 'w-6 h-6'
        ],
    ];

    $spec = $sizeSpecs[$size] ?? $sizeSpecs['md'];
    $paddingClass = $isIconOnly ? $spec['iconPadding'] : $spec['padding'];
    $textSizeClass = $spec['text'];
    $svgSize = $spec['svg'];

    // Definisi warna (Solid vs Outline vs Ghost)
    $colorPalette = [
        'blue' => [
            'solid' => 'text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-blue-300',
            'outline' => 'text-blue-600 border border-blue-600 hover:bg-blue-600 hover:text-white focus:ring-blue-300 bg-white dark:bg-transparent',
            'ghost' => 'text-blue-600 bg-blue-50 hover:bg-blue-100 focus:ring-blue-300 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40',
        ],
        'green' => [
            'solid' => 'text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-green-300',
            'outline' => 'text-green-700 border border-green-700 hover:bg-green-800 hover:text-white focus:ring-green-300',
            'ghost' => 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:ring-emerald-300',
        ],
        'red' => [
            'solid' => 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-red-300',
            'outline' => 'text-red-700 border border-red-700 hover:bg-red-800 hover:text-white focus:ring-red-300',
            'ghost' => 'text-red-700 bg-red-50 hover:bg-red-100 focus:ring-red-300 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40',
        ],
        'yellow' => [
            'solid' => 'text-white bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:bg-gradient-to-br focus:ring-yellow-300',
            'outline' => 'text-yellow-400 border border-yellow-400 hover:bg-yellow-500 hover:text-white focus:ring-yellow-300',
            'ghost' => 'text-yellow-700 bg-yellow-50 hover:bg-yellow-100 focus:ring-yellow-300',
        ],
        'purple' => [
            'solid' => 'text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-purple-300',
            'outline' => 'text-purple-700 border border-purple-700 hover:bg-purple-800 hover:text-white focus:ring-purple-300',
            'ghost' => 'text-purple-700 bg-purple-50 hover:bg-purple-100 focus:ring-purple-300',
        ],
        'dark' => [
            'solid' => 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-gray-300',
            'outline' => 'text-gray-900 border border-gray-800 hover:bg-gray-900 hover:text-white focus:ring-gray-300',
            'ghost' => 'text-gray-700 bg-gray-100 hover:bg-gray-200 focus:ring-gray-300 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600',
        ],
        'light' => [
            'solid' => 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:ring-gray-200',
            'outline' => 'text-gray-500 border border-gray-200 hover:bg-gray-100 focus:ring-gray-200',
            'ghost' => 'text-gray-500 bg-gray-50 hover:bg-gray-100 focus:ring-gray-200',
        ],
    ];

    $selectedColor = $colorPalette[$color] ?? $colorPalette['blue'];
    
    // Logic pilihan varian
    $variant = $attributes->get('variant') ?? ($outline ? 'outline' : 'solid');
    $colorClass = $selectedColor[$variant] ?? $selectedColor['solid'];

    // Menentukan radius sudut
    $radiusClass = $pill ? 'rounded-full' : $rounded;

    // Base class gabungan
    $baseClass = trim(
        "inline-flex items-center justify-center font-medium leading-5 transition-all duration-200 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed {$textSizeClass} {$paddingClass} {$colorClass} {$radiusClass}"
    );

    // Margin ikon
    $svgMarginClass = $isIconOnly ? '' : ($iconPosition === 'right' ? 'ms-2' : 'me-2');

    // Daftar Ikon (SVG Paths)
    $svgIcons = [
        'pencil-square' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.988 19.012 5.41-5.41m2.366-6.424 4.058 4.058-2.03 5.41L5.3 20 4 18.701l3.355-9.494 5.41-2.029Zm4.626 4.625L12.197 6.61 14.807 4 20 9.194l-2.61 2.61Z" />',
        'save' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v11a2 2 0 002 2h10a2 2 0 002-2V8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3H9v5h6V3z" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'x' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'trash' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />',
        'export' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5-5 5 5M12 5v14" />',
        'import' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 21H3" />',
        'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />',
        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />',
        'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />',
        'file-text' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
        'table' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7-4h14M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z" />',
        'printer' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />',
    ];
@endphp

@php $tag = $href ? 'a' : ($for ? 'label' : 'button'); @endphp

<{{ $tag }} 
    @if($href) href="{{ $href }}" @endif
    @if($for) for="{{ $for }}" @endif
    @if(!$href && !$for) type="{{ $type }}" @endif
    @if($wireClick) wire:click="{{ $wireClick }}" @endif
    {{ $attributes->merge(['class' => $baseClass]) }}
>
    {{-- Ikon posisi kiri atau Ikon saja --}}
    @if ($icon && isset($svgIcons[$icon]) && ($iconPosition === 'left' || $isIconOnly))
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            {!! $svgIcons[$icon] !!}
        </svg>
    @endif

    {{-- Teks (hanya jika ada) --}}
    @if($text)
        <span>{{ $text }}</span>
    @else
        {{ $slot }}
    @endif

    {{-- Ikon posisi kanan --}}
    @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'right' && !$isIconOnly)
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            {!! $svgIcons[$icon] !!}
        </svg>
    @endif
</{{ $tag }}>
