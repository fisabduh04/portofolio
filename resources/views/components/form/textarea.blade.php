@props(['disabled' => false, 'label' => false, 'name', 'id' => null, 'value' => null])

@php
    $value = old($name, $value ?? '');
@endphp

<div>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endif
    
    <textarea {{ $attributes }} 
        id="{{ $id ?? $name }}" 
        name="{{ $name }}" 
        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 @error($name) border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:border-red-500 dark:text-red-500 dark:placeholder-red-500 @enderror"
    >{{ $value }}</textarea>

    @error($name)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
