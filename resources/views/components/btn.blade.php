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
    'rounded' => 'rounded-base', // Sudut tumpul default
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

    // Definisi warna (Solid vs Outline)
    // Format: solid => 'bg classes', outline => 'border/text classes'
    $colorPalette = [
        'blue' => [
            'solid' => 'text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-blue-300',
            'outline' => 'text-blue-700 border border-blue-700 hover:bg-blue-800 hover:text-white focus:ring-blue-300',
        ],
        'green' => [
            'solid' => 'text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-green-300',
            'outline' => 'text-green-700 border border-green-700 hover:bg-green-800 hover:text-white focus:ring-green-300',
        ],
        'red' => [
            'solid' => 'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-red-300',
            'outline' => 'text-red-700 border border-red-700 hover:bg-red-800 hover:text-white focus:ring-red-300',
        ],
        'yellow' => [
            'solid' => 'text-white bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:bg-gradient-to-br focus:ring-yellow-300',
            'outline' => 'text-yellow-400 border border-yellow-400 hover:bg-yellow-500 hover:text-white focus:ring-yellow-300',
        ],
        'purple' => [
            'solid' => 'text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-purple-300',
            'outline' => 'text-purple-700 border border-purple-700 hover:bg-purple-800 hover:text-white focus:ring-purple-300',
        ],
        'cyan' => [
            'solid' => 'text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-cyan-300',
            'outline' => 'text-cyan-700 border border-cyan-700 hover:bg-cyan-800 hover:text-white focus:ring-cyan-300',
        ],
        'teal' => [
            'solid' => 'text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-teal-300',
            'outline' => 'text-teal-700 border border-teal-700 hover:bg-teal-800 hover:text-white focus:ring-teal-300',
        ],
        'lime' => [
            'solid' => 'text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-lime-300',
            'outline' => 'text-lime-700 border border-lime-700 hover:bg-lime-800 hover:text-white focus:ring-lime-300',
        ],
        'dark' => [
            'solid' => 'text-white bg-gray-800 hover:bg-gray-900 focus:ring-gray-300',
            'outline' => 'text-gray-900 border border-gray-800 hover:bg-gray-900 hover:text-white focus:ring-gray-300',
        ],
        'light' => [
            'solid' => 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:ring-gray-200',
            'outline' => 'text-gray-500 border border-gray-200 hover:bg-gray-100 focus:ring-gray-200',
        ],
    ];

    $selectedColor = $colorPalette[$color] ?? $colorPalette['blue'];
    $colorClass = $outline ? $selectedColor['outline'] : $selectedColor['solid'];

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
        'save' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v11a2 2 0 002 2h10a2 2 0 002-2V8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3H9v5h6V3z" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'x' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'trash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4h6v3" />',
        'export' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5-5 5 5M12 5v14" />',
        'import' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 21H3" />',
        'pencil' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6m2 2v10a2 2 0 01-2 2H7l-4 4V7a2 2 0 012-2h4" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />',
        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />',
        'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />',
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
    @endif

    {{-- Ikon posisi kanan --}}
    @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'right' && !$isIconOnly)
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            {!! $svgIcons[$icon] !!}
        </svg>
    @endif
</{{ $tag }}>
