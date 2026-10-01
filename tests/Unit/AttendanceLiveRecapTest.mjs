import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeAttendanceLiveRecap } from '../../resources/js/attendance-live-recap.js';

function setup() {
    const handlers = () => ({ handlers: {}, addEventListener(name, callback) { this.handlers[name] = callback; } });
    const status = { textContent: '' };
    const toggle = { ...handlers(), textContent: '', disabled: false };
    const scroll = { scrollLeft: 170 };
    const content = { innerHTML: 'old', contains: () => false,
        querySelectorAll: selector => selector === '.overflow-x-auto' ? [scroll] : [],
        replaceChildren(html) { this.innerHTML = html; this.replaced = (this.replaced || 0) + 1; } };
    const root = { dataset: { enabled: '1', url: 'https://school.test/absensi/rekap-harian?kelas_id=3&date=2026-10-01' },
        querySelector: selector => ({ '[data-live-recap-content]': content, '[data-live-recap-status]': status, '[data-live-recap-toggle]': toggle })[selector],
        dispatchEvent(event) { this.event = event; } };
    const document = { ...handlers(), hidden: false, querySelector: selector => selector === '[data-live-recap]' ? root : null };
    const timers = new Map();
    const calls = [];
    let timerId = 0;
    const environment = { ...handlers(), navigator: { onLine: true },
        setTimeout(fn, delay) { const id = ++timerId; timers.set(id, { fn, delay }); return id; },
        clearTimeout(id) { timers.delete(id); },
        CustomEvent: class { constructor(name) { this.type = name; } },
        DOMParser: class { parseFromString(html) { return { querySelector: () => html === 'login' ? null : {
            innerHTML: html, childNodes: [html], querySelectorAll: () => [],
        } }; } },
        fetch: async (url, options) => { calls.push({ url, options }); return { ok: true, text: async () => 'new' }; },
    };
    initializeAttendanceLiveRecap(document, environment);
    return { root, document, environment, content, status, toggle, timers, calls, scroll,
        async tick() { const [id, timer] = timers.entries().next().value; timers.delete(id); return timer.fn(); } };
}

test('polling replaces report data from another device while retaining filter URL and scroll', async () => {
    const page = setup();
    assert.equal([...page.timers.values()][0].delay, 5000);
    await page.tick();

    assert.equal(page.calls[0].url, page.root.dataset.url);
    assert.equal(page.calls[0].options.cache, 'no-store');
    assert.equal(page.content.innerHTML, 'new');
    assert.equal(page.scroll.scrollLeft, 170);
    assert.equal(page.root.event.type, 'attendance:recap-updated');
    assert.match(page.status.textContent, /Terakhir tersinkron/);
    await page.tick();
    assert.equal(page.content.replaced, 1);
});

test('a failed poll preserves existing data and backs off before recovering', async () => {
    const page = setup();
    const fetch = page.environment.fetch;
    page.environment.fetch = async () => { throw new TypeError('offline'); };
    await page.tick();
    assert.equal(page.content.innerHTML, 'old');
    assert.equal([...page.timers.values()][0].delay, 10000);
    await page.tick();
    assert.equal([...page.timers.values()][0].delay, 20000);
    page.environment.fetch = fetch;
    await page.tick();
    assert.equal(page.content.innerHTML, 'new');
    assert.equal([...page.timers.values()][0].delay, 5000);
});

test('pause and hidden tabs stop polling and resuming starts a fresh check', async () => {
    const page = setup();
    page.toggle.handlers.click();
    assert.equal(page.timers.size, 0);
    page.toggle.handlers.click();
    await page.tick();
    page.document.hidden = true;
    page.document.handlers.visibilitychange();
    assert.equal(page.timers.size, 0);
    page.document.hidden = false;
    page.document.handlers.visibilitychange();
    assert.equal([...page.timers.values()][0].delay, 0);
});

test('a slow request cannot overlap another poll and a paused late response cannot change data', async () => {
    const page = setup();
    let finish;
    page.environment.fetch = () => new Promise(resolve => { finish = resolve; });
    const pending = page.tick();
    assert.equal(page.timers.size, 1);
    assert.equal([...page.timers.values()][0].delay, 20000);
    page.toggle.handlers.click();
    finish({ ok: true, text: async () => 'late' });
    await pending;
    assert.equal(page.content.innerHTML, 'old');
    assert.equal(page.timers.size, 0);
});

for (const status of [401, 403, 419]) {
    test(`access failure ${status} stops updates without replacing the report`, async () => {
        const page = setup();
        page.environment.fetch = async () => ({ ok: false, status });
        await page.tick();
        assert.equal(page.content.innerHTML, 'old');
        assert.equal(page.timers.size, 0);
        assert.equal(page.toggle.disabled, true);
        assert.match(page.status.textContent, /login dan hak akses/);
    });
}

test('login or unexpected HTML is never substituted for attendance data', async () => {
    const page = setup();
    page.environment.fetch = async () => ({ ok: true, text: async () => 'login' });
    await page.tick();
    assert.equal(page.content.innerHTML, 'old');
    assert.match(page.status.textContent, /Belum tersinkron/);
});

test('editing or focusing report content defers replacement', async () => {
    const page = setup();
    page.content.contains = () => true;
    await page.tick();
    assert.equal(page.calls.length, 0);
    assert.equal(page.content.innerHTML, 'old');
});

test('offline and navigation stop timers and an online event restarts checks', () => {
    const page = setup();
    page.environment.navigator.onLine = false;
    page.environment.handlers.offline();
    assert.equal(page.timers.size, 0);
    page.environment.navigator.onLine = true;
    page.environment.handlers.online();
    assert.equal(page.timers.size, 1);
    page.environment.handlers.pagehide();
    assert.equal(page.timers.size, 0);
});
