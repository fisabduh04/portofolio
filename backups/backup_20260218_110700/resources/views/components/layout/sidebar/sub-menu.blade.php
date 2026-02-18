@props(['items'])

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
        @foreach($items as $item)
            <li class="mr-2">
                <a href="{{ $item['href'] }}" 
                   class="inline-block p-4 border-b-2 rounded-t-lg {{ ($item['active'] ?? false) ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}" 
                   @if($item['active'] ?? false) aria-current="page" @endif>
                   {{ $item['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
