<div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-xs pointer-events-none"></div>

<script>
    /**
     * Global Notif System (Vanilla JS) - 100% Tailwind Utility Classes
     * Mendukung: window.notif(), Livewire dispatch, dan Session Laravel transaksional.
     */
    window.notif = function(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const id = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = id;
        
        // Base Class: Glassmorphism + Transition (Tanpa CSS @keyframes kustom)
        toast.className = `relative flex flex-col w-full p-4 rounded-base shadow-xl border-l-4 transition-all duration-300 transform translate-x-full opacity-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md pointer-events-auto overflow-hidden`;
        
        // Konfigurasi Warna & Ikon berdasarkan standar Flowbite/Tailwind
        const config = {
            success: {
                border: 'border-emerald-500',
                iconColor: 'text-emerald-500 dark:text-emerald-400',
                barBg: 'bg-emerald-500',
                icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
            },
            error: {
                border: 'border-rose-600',
                iconColor: 'text-rose-600 dark:text-rose-500',
                barBg: 'bg-rose-600',
                icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            },
            warning: {
                border: 'border-amber-500',
                iconColor: 'text-amber-500 dark:text-amber-400',
                barBg: 'bg-amber-500',
                icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
            },
            info: {
                border: 'border-blue-500',
                iconColor: 'text-blue-500 dark:text-blue-400',
                barBg: 'bg-blue-500',
                icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            }
        };

        const style = config[type] || config.info;
        toast.classList.add(style.border);
        
        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 ${style.iconColor}">
                    ${style.icon}
                </div>
                <div class="ms-3 text-sm font-medium text-gray-800 dark:text-gray-200">
                    ${message}
                </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 text-gray-400 hover:text-gray-900 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 dark:hover:text-white" onclick="this.closest('[id^=toast-]').remove()">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <!-- Progress Bar (Timer) -->
            <div class="absolute bottom-0 left-0 h-1 ${style.barBg} opacity-30 transition-all ease-linear" style="width: 100%; transition-duration: ${duration}ms;" id="progress-${id}"></div>
        `;

        container.appendChild(toast);

        // Animasi Masuk (Slide-in)
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Trigger Progress Bar
        setTimeout(() => {
            const bar = document.getElementById(`progress-${id}`);
            if (bar) bar.style.width = '0%';
        }, 50);

        // Animasi Keluar & Hapus Otomatis
        setTimeout(() => {
            if (document.getElementById(id)) {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    };

    /**
     * Bridge Otomatis: Menangkap Dispatch dari Livewire
     */
    document.addEventListener('livewire:init', () => {
        Livewire.on('showToast', (event) => {
            // Support parameter objek (event.message) atau array positional (event[0])
            const data = Array.isArray(event) ? event[0] : event;
            const message = data.message;
            const type = data.type || 'success';
            if (message) window.notif(message, type);
        });
    });

    /**
     * Bridge Otomatis: Menangkap Session Laravel (Cara Lama & Baru)
     */
    window.addEventListener('load', () => {
        // Cara Lama: with('message', '...')->with('type', '...')
        @if(session()->has('message'))
            window.notif("{{ session('message') }}", "{{ session('type', 'success') }}");
        @endif

        // Cara Baru (Shorthand): with('success', '...') atau with('error', '...')
        @foreach(['success', 'error', 'warning', 'info'] as $type)
            @if(session()->has($type) && $type !== 'message')
                window.notif("{{ session($type) }}", "{{ $type }}");
            @endif
        @endforeach
    });
</script>
