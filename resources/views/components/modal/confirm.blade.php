<div id="confirm-modal" tabindex="-1" class="fixed inset-0 z-[9999] hidden flex-col items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeConfirmModal(false)">
    <div class="relative w-full max-w-md max-h-full p-4" onclick="event.stopPropagation()">
        <div class="relative bg-white rounded-2xl shadow-xl dark:bg-gray-800 border border-gray-150 dark:border-gray-700">
            <!-- Close Button -->
            <button type="button" onclick="closeConfirmModal(false)" class="absolute top-3.5 end-3 text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white cursor-pointer transition-colors" data-modal-hide="confirm-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-5 text-center">
                <!-- Icon -->
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto mb-4 border border-red-100 dark:border-red-900/50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 id="confirm-modal-message" class="mb-5 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Apakah Anda yakin?
                </h3>
                <div class="flex justify-center gap-3">
                    <button id="confirm-modal-ok" type="button" class="inline-flex items-center text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-bold rounded-xl text-xs px-5 py-2.5 text-center cursor-pointer transition-colors shadow-sm">
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeConfirmModal(false)" class="inline-flex py-2.5 px-5 text-xs font-medium text-gray-900 focus:outline-none bg-white rounded-xl border border-gray-200 hover:bg-gray-50 hover:text-blue-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 cursor-pointer transition-colors shadow-sm">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentConfirmCallback = null;

    window.showConfirmModal = function(message, onConfirm) {
        const modal = document.getElementById('confirm-modal');
        const msgElem = document.getElementById('confirm-modal-message');
        const okBtn = document.getElementById('confirm-modal-ok');
        
        if (modal && msgElem && okBtn) {
            msgElem.innerText = message;
            currentConfirmCallback = onConfirm;
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
            }, 10);
            
            // Setup click handler
            okBtn.onclick = function() {
                if (currentConfirmCallback) {
                    currentConfirmCallback();
                }
                closeConfirmModal(true);
            };
        }
    };

    window.closeConfirmModal = function(isConfirmed) {
        const modal = document.getElementById('confirm-modal');
        if (modal) {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
        currentConfirmCallback = null;
    };
    
    // Support Escape key for confirm modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmModal(false);
        }
    });
</script>
