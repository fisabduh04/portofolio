@props([
    'text' => 'Button',
    'type' => 'button',
    'color' => 'primary',
    'icon' => null,
    'wireClick' => null,
    'href' => null,
    'for' => null,
    'size' => 'md', // xs, sm, md, lg, xl
    'iconPosition' => 'left', // left or right
    'disabled' => false,
    'ariaLabel' => null,
    'rounded' => 'rounded-lg',
])

@php
    // Size classes
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'xl' => 'px-6 py-3 text-base',
    ];

    // Flowbite palette: full class strings to match examples (gradient, hover, focus ring, text color)
    $colors = [
        'blue' =>
            'text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-blue-300 dark:focus:ring-blue-800',
        'green' =>
            'text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-green-300 dark:focus:ring-green-800',
        'cyan' =>
            'text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-cyan-300 dark:focus:ring-cyan-800',
        'teal' =>
            'text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-teal-300 dark:focus:ring-teal-800',
        'lime' =>
            'text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-lime-300 dark:focus:ring-lime-800',
        'red' =>
            'text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-red-300 dark:focus:ring-red-800',
        'pink' =>
            'text-white bg-gradient-to-r from-pink-400 via-pink-500 to-pink-600 hover:bg-gradient-to-br focus:ring-pink-300 dark:focus:ring-pink-800',
        'purple' =>
            'text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-purple-300 dark:focus:ring-purple-800',
        'yellow' =>
            'text-white bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:bg-gradient-to-br focus:ring-yellow-300 dark:focus:ring-yellow-800',
        'dark' =>
            'text-white bg-gradient-to-r from-gray-800 via-gray-900 to-black hover:bg-gradient-to-br focus:ring-gray-700 dark:focus:ring-gray-700',
        'light' => 'text-gray-900 bg-white border hover:bg-gray-50 focus:ring-gray-300 dark:focus:ring-gray-800',
        'primary' =>
            'text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-blue-300 dark:focus:ring-blue-800',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $colorClass = $colors[$color] ?? $colors['primary'];

    // Base class aligned with Flowbite examples; allow rounded override via prop
    // Make buttons full width on small screens and auto on larger (matching layout expectations)
    $baseClass = trim(
        "inline-flex items-center justify-center w-full sm:w-auto {$rounded} font-sans font-medium leading-5 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed {$sizeClass} {$colorClass}",
    );

    // icon spacing class depends on icon position
    $svgMarginClass = $iconPosition === 'right' ? 'ms-2' : 'me-2';

    // NOTE: keep a single $baseClass (defined above) to avoid duplication; visual classes unchanged

    // Icon set (approx 20 common icons)
    $svgIcons = [
        'save' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8v11a2 2 0 002 2h10a2 2 0 002-2V8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3H9v5h6V3z" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
        'x' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
        'trash' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5 4v6m4-6v6M9 7V4h6v3" />',
        'export' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5-5 5 5M12 5v14" />',
        'import' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 21H3" />',
        'pencil' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6m2 2v10a2 2 0 01-2 2H7l-4 4V7a2 2 0 012-2h4" />',
        'search' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z" />',
        'download' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M21 21H3" />',
        'upload' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21V7m0 0l4 4m-4-4l-4 4M3 21h18" />',
        'eye' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
        'eye-off' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.985 9.985 0 012.19-3.236M3 3l18 18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.584 10.584A3 3 0 0113.416 13.416" />',
        'refresh' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7a9 9 0 10-9 9" />',
        'calendar' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
        'bell' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a2 2 0 10-4 0v1.083A6 6 0 004 11v3.159c0 .538-.214 1.055-.595 1.436L2 17h13z" />',
        'clock' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />',
        'user' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />',
        'chevron-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />',
        'minus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14" />',
        'plus-circle' =>
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8" /><circle cx="12" cy="12" r="9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />',
    ];

    $svgSize = 'w-5 h-5';

@endphp

@if ($for)
    <label for="{{ $for }}" {{ $attributes->merge(['class' => $baseClass . ' cursor-pointer']) }}>
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'left')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
        {{ $text }}
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'right')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
    </label>
@elseif($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass]) }}>
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'left')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
        {{ $text }}
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'right')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
    </a>
@else
    <button type="{{ $type }}" @if ($wireClick) wire:click="{{ $wireClick }}" @endif
        {{ $attributes->merge(['class' => $baseClass]) }}>
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'left')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
        {{ $text }}
        @if ($icon && isset($svgIcons[$icon]) && $iconPosition === 'right')
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $svgSize }} {{ $svgMarginClass }}" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                {!! $svgIcons[$icon] !!}
            </svg>
        @endif
    </button>
@endif
