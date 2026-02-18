@props(['active' => 'rules'])

<div class="border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
        <li class="mr-2">
            <a href="{{ route('attendance.rules.index') }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $active === 'rules' ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}" 
               aria-current="{{ $active === 'rules' ? 'page' : 'false' }}">
                Aturan Reguler
            </a>
        </li>
        <li class="mr-2">
            <a href="{{ route('attendance.mandatory.index') }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $active === 'mandatory' ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
               aria-current="{{ $active === 'mandatory' ? 'page' : 'false' }}">
                Jadwal Wajib
            </a>
        </li>
        <li class="mr-2">
            <a href="{{ route('attendance.events.index') }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $active === 'events' ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
               aria-current="{{ $active === 'events' ? 'page' : 'false' }}">
                Event & Kegiatan
            </a>
        </li>
        <li class="mr-2">
            <a href="{{ route('attendance.rule-allocations.index') }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg {{ $active === 'allocations' ? 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}"
               aria-current="{{ $active === 'allocations' ? 'page' : 'false' }}">
                Alokasi Aturan (Tahunan)
            </a>
        </li>
    </ul>
</div>
