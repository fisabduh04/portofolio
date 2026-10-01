export function initializeAttendanceLiveRecap(documentRoot = document, environment = window) {
    const root = documentRoot.querySelector('[data-live-recap]');
    if (!root || root.dataset.initialized || root.dataset.enabled !== '1') return;
    root.dataset.initialized = '1';
    const content = root.querySelector('[data-live-recap-content]');
    const status = root.querySelector('[data-live-recap-status]');
    const toggle = root.querySelector('[data-live-recap-toggle]');
    const url = root.dataset.url;
    let timer;
    let controller;
    let busy = false;
    let paused = false;
    let ended = false;
    let retryDelay = 5000;
    let lastHtml = content.innerHTML;
    let lastSuccess = '';
    const say = text => { status.textContent = text + (lastSuccess ? ` Terakhir tersinkron: ${lastSuccess}.` : ''); };
    const blocked = () => content.contains(documentRoot.activeElement)
        || !!documentRoot.querySelector('[role="dialog"][aria-modal="true"]:not(.hidden), #globalLightbox:not(.hidden), #sessionDetailModal:not(.hidden)');
    function schedule(delay = retryDelay) {
        environment.clearTimeout(timer);
        if (!paused && !ended && !documentRoot.hidden && environment.navigator.onLine !== false) {
            timer = environment.setTimeout(refresh, delay);
        }
    }
    function suspend() {
        environment.clearTimeout(timer);
        controller?.abort();
    }
    async function refresh() {
        if (busy || paused || ended || documentRoot.hidden || environment.navigator.onLine === false) return;
        if (blocked()) { schedule(); return; }
        busy = true;
        controller = new AbortController();
        const timeout = environment.setTimeout(() => controller?.abort(), 20000);
        try {
            const response = await environment.fetch(url, {
                credentials: 'same-origin', cache: 'no-store', redirect: 'error', signal: controller.signal,
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if ([401, 403, 419].includes(response.status)) {
                ended = true;
                toggle.disabled = true;
                say('Pembaruan berhenti. Muat ulang halaman untuk memeriksa login dan hak akses.');
                return;
            }
            if (!response.ok) throw new Error('response');
            const page = new environment.DOMParser().parseFromString(await response.text(), 'text/html');
            const updated = page.querySelector('[data-live-recap-content]');
            if (!updated) throw new Error('invalid-page');
            if (controller.signal.aborted || paused || ended || documentRoot.hidden || blocked()) return;
            const html = updated.innerHTML;
            if (html !== lastHtml) {
                const scrollPositions = [...content.querySelectorAll('.overflow-x-auto')].map(node => node.scrollLeft);
                const details = [...content.querySelectorAll('details')].map(node => node.open);
                updated.querySelectorAll('script').forEach(node => node.remove());
                root.dispatchEvent(new environment.CustomEvent('attendance:recap-updating', { bubbles: true }));
                content.replaceChildren(...updated.childNodes);
                content.querySelectorAll('.overflow-x-auto').forEach((node, index) => { node.scrollLeft = scrollPositions[index] || 0; });
                content.querySelectorAll('details').forEach((node, index) => { if (details[index] !== undefined) node.open = details[index]; });
                lastHtml = html;
                root.dispatchEvent(new environment.CustomEvent('attendance:recap-updated', { bubbles: true }));
            }
            lastSuccess = new Date().toLocaleTimeString('id-ID');
            retryDelay = 5000;
            say('Aktif · data terbaru dari semua perangkat.');
        } catch {
            if (!paused && !ended && !documentRoot.hidden) {
                retryDelay = Math.min(retryDelay * 2, 60000);
                say(`Belum tersinkron. Data lama tetap tampil; mencoba lagi dalam ${retryDelay / 1000} detik. Jika berlanjut, periksa koneksi atau login.`);
            }
        } finally {
            environment.clearTimeout(timeout);
            controller = null;
            busy = false;
            schedule();
        }
    }
    toggle.addEventListener('click', () => {
        paused = !paused;
        toggle.textContent = paused ? 'Lanjutkan pembaruan' : 'Jeda pembaruan';
        if (paused) { suspend(); say('Pembaruan dijeda.'); }
        else { say('Menghubungkan kembali…'); schedule(0); }
    });
    documentRoot.addEventListener('visibilitychange', () => {
        if (documentRoot.hidden) { suspend(); say('Pembaruan dijeda saat tab tidak aktif.'); }
        else schedule(0);
    });
    environment.addEventListener('offline', () => { suspend(); say('Offline. Data terakhir tetap tampil.'); });
    environment.addEventListener('online', () => schedule(0));
    environment.addEventListener('pagehide', () => { ended = true; suspend(); });
    environment.addEventListener('pageshow', event => { if (event.persisted) { ended = false; schedule(0); } });
    say('Aktif · menunggu pemeriksaan pertama.');
    schedule();
}
