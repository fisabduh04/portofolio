<div>

    @php
    $value = old($name, $value ?? '');

    @endphp

    @if ($label)
    <label for="{{ $id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
    @endif
    
    <div class="relative">
        <input {{ $attributes }} 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name ?? ''}}" 
            value="{{ $value}}"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-base focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 {{ $type === 'password' ? 'pr-10' : '' }} @error($name) bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-base focus:ring-red-500 dark:bg-gray-700 focus:border-red-500 block w-full p-2.5 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500 @enderror"
            placeholder="{{ $placeholder }}" 
        />

        @if($type === 'password')
        <button type="button" onclick="togglePasswordVisibility('{{ $id }}')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
            {{-- Icon Mata Tertutup (Default: Password Hidden) --}}
            <svg id="eyeIconClosed-{{ $id }}" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
            
            {{-- Icon Mata Terbuka (Password Visible) --}}
            <svg id="eyeIconOpen-{{ $id }}" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        </button>
        @endif
    </div>

    @error( $name )
    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror

    @if($type === 'password')
    <script>
        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            const eyeOpen = document.getElementById('eyeIconOpen-' + id);
            const eyeClosed = document.getElementById('eyeIconClosed-' + id);
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
    @endif
</div>
