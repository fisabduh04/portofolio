<div class="mb-4 mt-1 col-span-full">
    <nav class="flex mb-3" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            @foreach ($breadcrumbs as $breadcrumb)
            <li class="inline-flex items-center">
                {{-- separator --}}
                @if (!$loop->first)
                <div class="flex items-center">
                    <svg class="w-3.5 h-3.5 rtl:rotate-180 text-body" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                </div>
                @endif

                @if (!$loop->last)
                <a href="{{ $breadcrumb['href'] }}" class="inline-flex items-center text-sm font-medium text-body hover:text-fg-brand {{ !$loop->first ? 'ms-1 md:ms-2' : '' }}">
                    @if ($loop->first)
                    <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/></svg>
                    @endif
                    {{ $breadcrumb['name'] }}
                </a>
                @else
                <span class="ms-1 text-sm font-medium text-body-subtle md:ms-2">
                    {{ $breadcrumb['name'] }}
                </span>
                @endif
            </li>
            @endforeach
        </ol>
    </nav>
    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white mt-1">
        {{ end($breadcrumbs)['name'] }}
    </h1>
</div>