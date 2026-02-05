<div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-xs pointer-events-none"></div>

<script>
    /**
     * Premium Global Notification System (Vanilla JS)
     * Standard Industri: Stacking Support, Progress Bar, Smooth Animations, & Delayed Closing.
     */
    window.notif = function(message, type = 'success', duration = 4500) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const id = 'toast-' + Math.random().toString(36).substr(2, 9);
        const toast = document.createElement('div');
        toast.id = id;
        
        // Base Styling: Glassmorphism Ultra + Premium Shadow + Stacking Animation
        toast.className = `
            relative flex flex-col w-full p-4 rounded-xl shadow-2xl border border-white/20 
            bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl pointer-events-auto 
            transition-all duration-500 ease-[cubic-bezier(0.68,-0.55,0.265,1.55)] 
            transform translate-x-[110%] opacity-0 overflow-hidden group
        `;
        
        // Color Configuration & Standard Icons
        const config = {
            success: {
                accent: 'bg-emerald-500',
                iconColor: 'text-emerald-500',
                icon: `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            },
            error: {
                accent: 'bg-rose-600',
                iconColor: 'text-rose-600',
                icon: `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            },
            warning: {
                accent: 'bg-amber-500',
                iconColor: 'text-amber-500',
                icon: `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`
            },
            info: {
                accent: 'bg-blue-500',
                iconColor: 'text-blue-500',
                icon: `<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
            }
        };

        const style = config[type] || config.info;
        
        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 ${style.iconColor} animate-bounce-short">
                    ${style.icon}
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold capitalize ${style.iconColor} mb-0.5">${type}</h4>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-relaxed">${message}</p>
                </div>
                <button type="button" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200" onclick="window.closeToast('${id}')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Progress Line (Timer) -->
            <div class="absolute bottom-0 left-0 h-[3px] ${style.accent} transition-all ease-linear" style="width: 100%; transition-duration: ${duration}ms;" id="progress-${id}"></div>
        `;

        // Tambahkan ke container
        container.appendChild(toast);

        // Slide In Animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-[110%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Trigger Progress Bar
        setTimeout(() => {
            const bar = document.getElementById(`progress-${id}`);
            if (bar) bar.style.width = '0%';
        }, 50);

        // Auto Close Timer
        const autoCloseTimeout = setTimeout(() => {
            window.closeToast(id);
        }, duration);

        // Simpan timeout ID di elemen untuk referensi
        toast.dataset.timeoutId = autoCloseTimeout;
    };

    /**
     * Profesional Closing: Animasi Keluar dulu baru Hapus Elemen
     */
    window.closeToast = function(id) {
        const toast = document.getElementById(id);
        if (!toast) return;

        // Bersihkan timeout jika ada
        if (toast.dataset.timeoutId) clearTimeout(toast.dataset.timeoutId);

        // Animasi Keluar (Slide Out)
        toast.classList.add('translate-x-[120%]', 'opacity-0', 'scale-90');
        
        // Hapus elemen setelah animasi selesai
        setTimeout(() => {
            toast.remove();
        }, 500);
    };

    /**
     * Bridge: Menangkap Dispatch Livewire
     */
    document.addEventListener('livewire:init', () => {
        Livewire.on('showToast', (event) => {
            const data = Array.isArray(event) ? event[0] : event;
            if (data.message) window.notif(data.message, data.type || 'success');
        });
    });

    /**
     * Bridge: Menangkap Session Laravel
     */
    window.addEventListener('load', () => {
        // Safe PHP-to-JS Injection using json_encode
        
        // 1. Handle standard 'message' key
        @if(session()->has('message'))
            window.notif(
                {!! json_encode(session('message')) !!}, 
                {!! json_encode(session('type', 'success')) !!}
            );
        @endif

        // 2. Handle shorthand keys (success, error, warning, info)
        @foreach(['success', 'error', 'warning', 'info'] as $type)
            @if(session()->has($type) && $type !== 'message')
                window.notif(
                    {!! json_encode(session($type)) !!}, 
                    {!! json_encode($type) !!}
                );
            @endif
        @endforeach
    });
</script>

<style>
    /* Mikro Animasi Tambahan */
    @keyframes bounce-short {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-short {
        animation: bounce-short 1.5s ease-in-out infinite;
    }
</style>
