@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'restoreOldInput' => true,
    'placeholder' => 'Pilih opsi',
    'searchPlaceholder' => 'Ketik untuk mencari…',
    'maxResults' => 50,
    'portal' => false,
    'size' => 'md',
])

@php
    $inputId = $id ?? 'searchable-select-'.Illuminate\Support\Str::uuid();
    $controlClass = $size === 'sm' ? 'table-form-control' : 'searchable-select-control';
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $selectedValue = $restoreOldInput ? old($errorKey, $selected) : $selected;
    $hasError = $errors->has($errorKey);
    $descriptionIds = trim(($attributes->get('aria-describedby') ?? '').' '.($hasError ? $inputId.'-error' : ''));
@endphp

<div data-searchable-select data-max-results="{{ max(1, min(200, (int) $maxResults)) }}" @if ($portal) data-select-portal @endif class="relative">
    @if ($label)
        <label id="{{ $inputId }}-label" for="{{ $inputId }}" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        data-select-native
        {{ $attributes->except(['aria-invalid', 'aria-describedby'])->merge([
            'class' => $controlClass,
            'aria-invalid' => $hasError ? 'true' : $attributes->get('aria-invalid', 'false'),
            'aria-describedby' => $descriptionIds ?: null,
        ]) }}
    >
        <option value="" @selected($selectedValue === null || (string) $selectedValue === '')>{{ $placeholder }}</option>
        @foreach ($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected($selectedValue !== null && (string) $value === (string) $selectedValue)>{{ $optionLabel }}</option>
        @endforeach
        {{ $slot }}
    </select>

    <button
        id="{{ $inputId }}-trigger"
        type="button"
        data-select-trigger
        class="{{ $controlClass }} flex items-center justify-between gap-2 text-start"
        aria-haspopup="listbox"
        aria-controls="{{ $inputId }}-listbox"
        aria-expanded="false"
        @if ($label) aria-labelledby="{{ $inputId }}-label {{ $inputId }}-value" @endif
        hidden
    >
        <span id="{{ $inputId }}-value" data-select-value class="min-w-0 truncate">{{ $placeholder }}</span>
        <svg class="size-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div data-select-popup class="absolute inset-x-0 z-50 rounded-lg border border-gray-200 bg-white p-2 shadow-lg dark:border-gray-600 dark:bg-gray-800" hidden>
        <input
            id="{{ $inputId }}-search"
            type="text"
            data-select-search
            class="{{ $controlClass }} mb-2"
            role="combobox"
            aria-label="{{ $label ? 'Cari '.$label : $searchPlaceholder }}"
            aria-controls="{{ $inputId }}-listbox"
            aria-expanded="false"
            aria-autocomplete="list"
            autocomplete="off"
            placeholder="{{ $searchPlaceholder }}"
        >
        <div id="{{ $inputId }}-listbox" data-select-list role="listbox" aria-label="{{ $label ?? $placeholder }}" class="max-h-60 overflow-y-auto overscroll-contain"></div>
        <p data-select-status role="status" aria-live="polite" class="px-2 py-1 text-xs text-gray-500 dark:text-gray-400"></p>
    </div>

    @error($errorKey)
        <p id="{{ $inputId }}-error" class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
