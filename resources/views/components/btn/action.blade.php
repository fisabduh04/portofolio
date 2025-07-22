<div>
    @props([
        'text' => 'Button',
        'type' => 'button',
        'color' => 'blue', // blue, green, red, cyan
        'icon' => null, // plus, save, trash, export, import, etc.
        'wireClick' => null,
        'href' => null,
        'for' => null, // untuk label input file
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
            'save' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
            'trash' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14M10 11v6m4-6v6M9 3h6a1 1 0 0 1 1 1v1H8V4a1 1 0 0 1 1-1Z" />',
            'export' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5m0 0l-5-5m5 5H3m13 5v4m0 0h4m-4 0H9" />',
            'import' =>
                '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 12l-5 5m0 0l5-5m-5 5V3m10 10v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4" />',
        ];

        $baseClass = "inline-flex items-center text-white bg-gradient-to-r {$colors[$color]} hover:bg-gradient-to-br focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2";
    @endphp

    {{-- Label (for input) --}}
    @if ($for)
        <label for="{{ $for }}" class="{{ $baseClass }} cursor-pointer">
            @if ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif
            {{ $text }}
        </label>

        {{-- Link --}}
    @elseif($href)
        <a href="{{ $href }}" class="{{ $baseClass }}">
            @if ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif
            {{ $text }}
        </a>

        {{-- Button --}}
    @else
        <button type="{{ $type }}"
            @if ($wireClick) wire:click="{{ $wireClick }}"
            wire:loading.attr="disabled"
            wire:target="{{ $wireClick }}" @endif
            class="{{ $baseClass }}">
            {{-- Spinner saat loading --}}
            @if ($wireClick)
                <svg wire:loading wire:target="{{ $wireClick }}" class="w-5 h-5 me-2 animate-spin text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 000 16z"></path>
                </svg>
            @elseif ($icon && isset($svgIcons[$icon]))
                <svg class="w-5 h-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    {!! $svgIcons[$icon] !!}
                </svg>
            @endif

            {{ $text }}
        </button>
    @endif
</div>
