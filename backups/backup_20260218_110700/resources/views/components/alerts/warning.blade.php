@props(['title'])

<div {{ $attributes->merge(['class' => 'p-4 my-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300']) }}
    role="alert">
    <span class="font-medium">{{ $title }}</span>
    {{ $slot }}
</div>
