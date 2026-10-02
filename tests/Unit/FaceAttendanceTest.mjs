import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeFaceAttendance } from '../../resources/js/face-attendance.js';

function scanner(mode = 'piket') {
    const control = () => ({ dataset: {}, handlers: {}, disabled: false, textContent: '', value: '', children: [], classList: { toggle() {}, add() {} },
        addEventListener(name, handler) { this.handlers[name] = handler; }, focus() {},
        prepend(item) { item.remove = () => { this.children = this.children.filter(child => child !== item); }; this.children.unshift(item); },
        get lastElementChild() { return this.children.at(-1); } });
    const fields = new Map();
    const get = name => {
        if (!fields.has(name)) fields.set(name, control());
        return fields.get(name);
    };
    const root = { dataset: { mode, jadwal: '9', type: 'masuk', endpoint: '/presensi-wajah' }, querySelector: selector => get(selector.slice(11, -1)) };
    const document = { ...control(), querySelector: selector => selector === '[data-face-attendance]' ? root : { content: 'token' },
        createElement: tag => tag === 'canvas' ? { getContext: () => ({ drawImage() {} }) } : control() };
    const track = { ...control(), stop() {} };
    const calls = [];
    let elapsed = 0;
    let faceError = null;
    let timerId = 0;
    const timers = new Map();
    const advance = milliseconds => { elapsed += milliseconds; };
    const environment = { ...control(), isSecureContext: true,
        setTimeout(handler, delay) { const id = ++timerId; timers.set(id, { handler, delay }); return id; },
        clearTimeout(id) { timers.delete(id); },
        performance: { now: () => elapsed }, location: { host: 'portofolio.test', hostname: 'portofolio.test' },
        navigator: { mediaDevices: { getUserMedia: async () => ({ getTracks: () => [track], getVideoTracks: () => [track] }) } },
        loadFaceModels: async () => {
            advance(200);
            return Object.assign(async () => {
                advance(400);
                if (faceError) throw Object.assign(new Error(faceError), { code: faceError });
                return Array(128).fill(0.1);
            }, { backend: 'webgl' });
        },
        fetch: async (url, options) => {
            advance(300);
            calls.push(JSON.parse(options.body));
            return { ok: true, json: async () => ({ server_ms: 120, student: '<script>test</script>', kelas: 'X A', status: 'Hadir', message: 'Tercatat', time: '10:00:00', already_recorded: false }) };
        } };
    Object.assign(get('video'), { readyState: 2, videoWidth: 640, videoHeight: 480, play: async () => { advance(100); } });
    initializeFaceAttendance(document, environment);
    return { get, calls, environment, document, advance, timers, setFaceError: code => { faceError = code; },
        async tick() {
            const [id, timer] = timers.entries().next().value;
            timers.delete(id);
            advance(timer.delay);
            await timer.handler();
        }, click: name => get(name).handlers.click() };
}

test('a scan submits the descriptor and renders server identity as plain text', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('capture');

    assert.equal(page.calls[0].mode, 'piket');
    assert.equal(page.calls[0].descriptor.length, 128);
    assert.equal(page.get('count').textContent, '1 presensi baru');
    assert.match(page.get('results').children[0].textContent, /<script>test<\/script>/);
    assert.equal(page.get('capture').disabled, false);
    assert.equal(page.get('feedback').dataset.state, 'success');
    assert.equal(page.get('identity').textContent, '<script>test</script>');
    assert.equal(page.get('result-label').textContent, 'Berhasil dicatat');
});

test('rear camera selection supports scanning and can be changed after stopping', async () => {
    const page = scanner();
    const original = page.environment.navigator.mediaDevices.getUserMedia;
    let requested;
    let mirrored;
    page.environment.navigator.mediaDevices.getUserMedia = async constraints => {
        requested = constraints;
        return original();
    };
    page.get('video').classList.toggle = (name, enabled) => { if (name === '-scale-x-100') mirrored = enabled; };
    page.get('facing').value = 'environment';

    await page.click('start');
    await page.click('capture');

    assert.deepEqual(requested.video.facingMode, { exact: 'environment' });
    assert.equal(mirrored, false);
    assert.equal(page.get('facing').disabled, true);
    assert.equal(page.calls.length, 1);
    await page.click('stop');
    assert.equal(page.get('facing').disabled, false);
});

test('an old camera ending does not interrupt attendance after switching cameras', async () => {
    const page = scanner();
    await page.click('start');
    const oldCameraEnded = page.get('video').srcObject.getVideoTracks()[0].handlers.ended;
    await page.click('stop');
    page.get('facing').value = 'environment';
    await page.click('start');
    const currentCameraEnded = page.get('video').srcObject.getVideoTracks()[0].handlers.ended;

    oldCameraEnded();
    await page.click('capture');

    assert.equal(page.calls.length, 1);
    assert.equal(page.get('capture').disabled, false);
    currentCameraEnded();
    assert.equal(page.get('capture').disabled, true);
    assert.match(page.get('message').textContent, /Kamera terputus/);
});

test('attendance prepares inline muted playback before starting mobile video', async () => {
    const page = scanner();
    const video = page.get('video');
    video.play = async () => {
        if (!video.muted || !video.playsInline) throw new Error('Mobile playback blocked');
    };

    await page.click('start');
    await page.click('capture');

    assert.equal(page.calls.length, 1);
});

test('attendance waits for video height before scanning a mobile camera frame', async () => {
    const page = scanner();
    page.get('video').videoHeight = 0;
    await page.click('start');
    await page.click('capture');
    assert.equal(page.get('capture').disabled, true);
    assert.equal(page.calls.length, 0);

    page.get('video').videoHeight = 480;
    page.get('video').handlers.resize();
    await page.click('capture');

    assert.equal(page.calls.length, 1);
});

test('missing rear camera leaves attendance inactive and allows a different selection', async () => {
    const page = scanner();
    page.get('facing').value = 'environment';
    page.environment.navigator.mediaDevices.getUserMedia = async () => { throw { name: 'NotFoundError' }; };

    await page.click('start');

    assert.match(page.get('message').textContent, /Kamera belakang tidak tersedia/);
    assert.equal(page.get('facing').disabled, false);
    assert.equal(page.get('capture').disabled, true);
    assert.equal(page.calls.length, 0);
});

test('a mapel scan requires lesson material before sending any attendance', async () => {
    const page = scanner('mapel');
    await page.click('start');
    await page.click('capture');

    assert.equal(page.calls.length, 0);
    assert.equal(page.get('message').textContent, 'Isi materi pelajaran terlebih dahulu.');
});

test('a rejected match displays the server error without adding a success result', async () => {
    const page = scanner();
    page.environment.fetch = async () => ({ ok: false, status: 422, json: async () => ({ errors: { descriptor: ['Wajah tidak dikenali'] } }) });
    await page.click('start');
    await page.click('capture');

    assert.equal(page.get('results').children.length, 0);
    assert.equal(page.get('message').textContent, 'Wajah tidak dikenali');
    assert.equal(page.get('feedback').dataset.state, 'error');
    assert.equal(page.get('result-detail').textContent, 'Wajah tidak dikenali');
    assert.equal(page.get('capture').disabled, false);
});

test('repeat clicks during matching only send one attendance request', async () => {
    const page = scanner();
    await page.click('start');
    await Promise.all([page.click('capture'), page.click('capture')]);

    assert.equal(page.calls.length, 1);
});

test('a new scan clears the previous identity while matching and shows an uncertain network result', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('capture');
    let reject;
    page.environment.fetch = () => new Promise((resolve, fail) => { reject = fail; });
    const pending = page.click('capture');
    while (!reject) await Promise.resolve();
    assert.equal(page.get('feedback').dataset.state, 'processing');
    assert.equal(page.get('identity').textContent, 'Mencocokkan siswa…');
    reject(new TypeError('offline'));
    await pending;
    assert.equal(page.get('feedback').dataset.state, 'error');
    assert.equal(page.get('result-label').textContent, 'Periksa hasil di logbook');
    assert.doesNotMatch(page.get('identity').textContent, /<script>/);
    assert.equal(page.get('results').children.length, 1);
});

test('manual scanning without a face shows clear feedback instead of a previous students name', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('capture');
    page.setFaceError('no_face');
    await page.click('capture');
    assert.equal(page.get('identity').textContent, 'Wajah belum terdeteksi');
    assert.equal(page.get('feedback').dataset.state, 'error');
    assert.equal(page.calls.length, 1);
});

test('an already recorded student does not increase the new attendance count', async () => {
    const page = scanner();
    page.environment.fetch = async () => ({ ok: true, json: async () => ({ student: 'Siswa', kelas: 'X', status: 'Izin', message: 'Sudah tercatat', time: '10:00', already_recorded: true }) });
    await page.click('start');
    await page.click('capture');

    assert.equal(page.get('count').textContent, '0 presensi baru');
    assert.match(page.get('results').children[0].textContent, /Izin/);
    assert.equal(page.get('result-label').textContent, 'Sudah tercatat');
    assert.match(page.get('result-detail').textContent, /Izin/);
});

test('performance separates preparation, device detection and server round trip', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('capture');

    assert.equal(page.get('perf-camera').textContent, '100 ms');
    assert.equal(page.get('perf-model').textContent, '200 ms');
    assert.match(page.get('perf-backend').textContent, /WebGL/);
    assert.match(page.get('perf-environment').textContent, /lokal/);
    assert.equal(page.get('perf-host').textContent, 'portofolio.test');
    assert.equal(page.get('perf-detection').textContent, '400 ms');
    assert.equal(page.get('perf-response').textContent, '300 ms');
    assert.equal(page.get('perf-server').textContent, '120 ms');
    assert.equal(page.get('perf-overhead').textContent, '180 ms');
    assert.equal(page.get('perf-total').textContent, '700 ms');
    assert.equal(page.get('perf-average').textContent, '700 ms · 1 pindai berhasil terakhir');
    assert.doesNotMatch(page.get('results').children[0].textContent, /Deteksi|Respons|Total/);
});

test('failed network attempts clear old server metrics and do not enter the success average', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('capture');
    page.environment.fetch = async () => { page.advance(1000); throw new TypeError('offline'); };
    await page.click('capture');

    assert.equal(page.get('perf-server').textContent, '—');
    assert.equal(page.get('perf-overhead').textContent, '—');
    assert.equal(page.get('perf-response').textContent, '1000 ms (tidak selesai)');
    assert.equal(page.get('perf-total').textContent, '1400 ms');
    assert.equal(page.get('perf-status').textContent, 'Gagal / dibatalkan');
    assert.equal(page.get('perf-average').textContent, '700 ms · 1 pindai berhasil terakhir');
});

test('failed detection reports elapsed time without claiming a server request', async () => {
    const page = scanner();
    page.environment.loadFaceModels = async () => Object.assign(async () => {
        page.advance(500);
        throw new Error('Wajah belum terdeteksi');
    }, { backend: 'cpu' });
    await page.click('start');
    await page.click('capture');

    assert.equal(page.get('perf-backend').textContent, 'CPU');
    assert.equal(page.get('perf-detection').textContent, '500 ms (tidak selesai)');
    assert.equal(page.get('perf-response').textContent, '—');
    assert.equal(page.calls.length, 0);
});

test('a rejected match reports its response time without inventing server duration', async () => {
    const page = scanner();
    page.environment.fetch = async () => {
        page.advance(250);
        return { ok: false, status: 422, json: async () => ({ message: 'Wajah tidak dikenali' }) };
    };
    await page.click('start');
    await page.click('capture');

    assert.equal(page.get('perf-response').textContent, '250 ms');
    assert.equal(page.get('perf-server').textContent, '—');
    assert.equal(page.get('perf-total').textContent, '650 ms');
    assert.equal(page.get('perf-status').textContent, 'Gagal / dibatalkan');
});

test('session average and result history retain only the latest twenty successes', async () => {
    const page = scanner();
    await page.click('start');
    const fetch = page.environment.fetch;
    page.environment.fetch = async (...args) => { page.advance(2000); return fetch(...args); };
    await page.click('capture');
    page.environment.fetch = fetch;
    for (let index = 0; index < 20; index++) await page.click('capture');

    assert.equal(page.get('perf-average').textContent, '700 ms · 20 pindai berhasil terakhir');
    assert.equal(page.get('results').children.length, 20);
});

test('cancelling detection does not leave performance marked as scanning', async () => {
    const page = scanner();
    let finish;
    page.environment.loadFaceModels = async () => () => new Promise(resolve => { finish = resolve; });
    await page.click('start');
    const pending = page.click('capture');
    await page.click('stop');
    finish(Array(128).fill(0.1));
    await pending;

    assert.equal(page.get('perf-status').textContent, 'Gagal / dibatalkan');
    assert.equal(page.calls.length, 0);
    assert.equal(page.get('feedback').dataset.state, 'ready');
    assert.equal(page.get('identity').textContent, 'Kamera nonaktif');
});

test('automatic scanning waits for two empty frames before recording the next student', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('auto');
    await page.tick();
    assert.equal(page.calls.length, 1);
    assert.equal(page.get('capture').disabled, true);
    assert.equal(page.get('perf-status').textContent, 'Selesai');
    assert.equal(page.get('result-label').textContent, 'Berhasil dicatat');

    page.setFaceError('no_face');
    await page.tick();
    page.setFaceError(null);
    await page.tick();
    assert.equal(page.calls.length, 1);

    page.setFaceError('no_face');
    await page.tick();
    await page.tick();
    assert.match(page.get('auto-status').textContent, /Siap/);
    assert.equal(page.get('identity').textContent, 'Silakan maju');
    assert.equal(page.get('feedback').dataset.state, 'ready');
    page.setFaceError(null);
    await page.tick();
    assert.equal(page.calls.length, 2);
    assert.equal(page.get('perf-average').textContent, '700 ms · 2 pindai berhasil terakhir');
});

test('multiple faces neither submit attendance nor release the next-student gate', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('auto');
    page.setFaceError('no_face');
    await page.tick();
    page.setFaceError('multiple_faces');
    await page.tick();
    assert.match(page.get('auto-status').textContent, /Lebih dari satu wajah/);
    page.setFaceError('no_face');
    await page.tick();
    page.setFaceError(null);
    await page.tick();

    assert.equal(page.calls.length, 1);
});

test('pausing automatic scanning cancels its timer and manual scanning remains available', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('auto');
    await page.click('auto');

    assert.equal(page.timers.size, 0);
    assert.equal(page.get('capture').disabled, false);
    await page.click('capture');
    assert.equal(page.calls.length, 2);
});

test('pausing while detection runs prevents its later result from being submitted', async () => {
    const page = scanner();
    let finish;
    page.environment.loadFaceModels = async () => () => new Promise(resolve => { finish = resolve; });
    await page.click('start');
    const pending = page.click('auto');
    await page.click('auto');
    finish(Array(128).fill(0.1));
    await pending;

    assert.equal(page.calls.length, 0);
    assert.equal(page.timers.size, 0);
});

test('automatic matching has only one pending request and pause lets that request finish', async () => {
    const page = scanner();
    let finish;
    let requests = 0;
    page.environment.fetch = async () => { requests++; return new Promise(resolve => { finish = resolve; }); };
    await page.click('start');
    const pending = page.click('auto');
    while (!finish) await Promise.resolve();
    await page.click('capture');
    assert.equal(requests, 1);
    assert.equal(page.timers.size, 0);
    await page.click('auto');
    finish({ ok: true, json: async () => ({ student: 'Siswa', kelas: 'X', status: 'Hadir', message: 'Tercatat' }) });
    await pending;

    assert.equal(page.timers.size, 0);
    assert.equal(page.get('result-label').textContent, 'Berhasil dicatat');
});

test('unknown faces wait for departure rather than repeatedly querying the server', async () => {
    const page = scanner();
    let requests = 0;
    page.environment.fetch = async () => {
        requests++;
        return { ok: false, status: 422, json: async () => ({ errors: { descriptor: ['Wajah tidak dikenali'] } }) };
    };
    await page.click('start');
    await page.click('auto');
    await page.tick();

    assert.equal(requests, 1);
    assert.equal(page.get('results').children.length, 0);
    assert.equal(page.get('perf-status').textContent, 'Gagal / dibatalkan');
    assert.equal(page.timers.size, 1);
});

for (const status of [401, 403, 419, 422, 429, 500]) {
    test(`automatic scanning pauses on server failure ${status}`, async () => {
        const page = scanner();
        page.environment.fetch = async () => ({ ok: false, status, json: async () => ({ message: 'Server menolak' }) });
        await page.click('start');
        await page.click('auto');

        assert.equal(page.timers.size, 0);
        assert.equal(page.get('auto').textContent, 'Mulai otomatis');
        if (status === 429) assert.match(page.get('message').textContent, /Tunggu satu menit/);
    });
}

test('network failure pauses automatic scanning without silently retrying a possibly saved record', async () => {
    const page = scanner();
    page.environment.fetch = async () => { throw new TypeError('offline'); };
    await page.click('start');
    await page.click('auto');

    assert.equal(page.timers.size, 0);
    assert.match(page.get('message').textContent, /Periksa logbook/);
});

test('leaving the tab stops automatic scanning and camera', async () => {
    const page = scanner();
    await page.click('start');
    await page.click('auto');
    page.document.hidden = true;
    page.document.handlers.visibilitychange();

    assert.equal(page.timers.size, 0);
    assert.equal(page.get('video').srcObject, null);
    assert.equal(page.get('auto').disabled, true);
});

test('automatic mapel scanning requires material before starting', async () => {
    const page = scanner('mapel');
    await page.click('start');
    await page.click('auto');

    assert.equal(page.calls.length, 0);
    assert.equal(page.timers.size, 0);
    assert.equal(page.get('message').textContent, 'Isi materi pelajaran terlebih dahulu.');
});
