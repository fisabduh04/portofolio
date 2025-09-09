<div>
    @props([
        'text' => 'Button',
        'type' => 'button',
        'color' => 'blue', // blue, green, red, cyan
        'icon' => null,
        'wireClick' => null,
        'href' => null,
        'for' => null,
    ])

    @php
        $colors = [
            'blue' =>
                'from-blue-500 via-blue-600 to-blue-700 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-blue-500/50 dark:shadow-blue-800/80',
            'green' =>
                'from-green-400 via-green-500 to-green-600 focus:ring-green-300 dark:focus:ring-green-800 shadow-green-500/50 dark:shadow-green-800/80',
            'red' =>
                'from-red-400 via-red-500 to-red-600 focus:ring-red-300 dark:focus:ring-red-800 shadow-red-500/50 dark:shadow-red-800/80',
            'cyan' =>
                'from-cyan-400 via-cyan-500 to-cyan-600 focus:ring-cyan-300 dark:focus:ring-cyan-800 shadow-cyan-500/50 dark:shadow-cyan-800/80',
        ];

        $svgIcons = [
            'plus' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
            'save' => '<path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z" />
                       <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z" />',
            'trash' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />',
            'export' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />',
            'import' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 6c0 1.657-3.134 3-7 3S5 7.657 5 6m14 0c0-1.657-3.134-3-7-3S5 4.343 5 6m14 0v6M5 6v6m0 0c0 1.657 3.134 3 7 3s7-1.343 7-3M5 12v6c0 1.657 3.134 3 7 3s7-1.343 7-3v-6" />',
        ];

        $defaultClass = "inline-flex items-center text-white bg-gradient-to-r {$colors[$color]} hover:bg-gradient-to-br focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2";
    @endphp

    @if ($for)
        <label for="{{ $for }}" {{ $attributes->merge(['class' => "$defaultClass cursor-pointer"]) }}>
            @if ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 text-white me-2 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif
            {{ $text }}
        </label>
    @elseif($href)
        <a href="{{ $href }}" {{ $attributes->merge(['class' => $defaultClass]) }}>
            @if ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 text-white me-2 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif
            {{ $text }}
        </a>
    @else
        <button type="{{ $type }}" @if ($wireClick) wire:click="{{ $wireClick }}" @endif
            {{ $attributes->merge(['class' => $defaultClass]) }}>
            @if ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 text-white me-2 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif
            {{ $text }}
        </button>
    @endif
</div>
