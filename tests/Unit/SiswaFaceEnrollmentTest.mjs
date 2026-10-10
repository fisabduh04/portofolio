import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeFaceEnrollment, requestFaceCamera } from '../../resources/js/siswa-face-enrollment.js';

test('an unanswered camera request times out and releases a stream granted afterward', async () => {
    let grant;
    let stopped = false;
    const request = requestFaceCamera({ getUserMedia: () => new Promise(resolve => { grant = resolve; }) }, 5);

    await assert.rejects(request, { name: 'FaceStartupTimeout' });
    grant({ getTracks: () => [{ stop() { stopped = true; } }] });
    await new Promise(resolve => setImmediate(resolve));

    assert.equal(stopped, true);
});

function setup(maximumSampleDistance = '0.6') {
    const element = () => {
        const classes = new Set();
        return { dataset: {}, handlers: {}, disabled: false, textContent: '', classList: {
            add: value => classes.add(value), remove: value => classes.delete(value),
            toggle(value, enabled) { enabled ? classes.add(value) : classes.delete(value); },
            contains: value => classes.has(value),
        }, addEventListener(name, handler) { this.handlers[name] = handler; }, focus() {}, removeAttribute(name) { delete this[name]; } };
    };
    const elements = new Map();
    const get = name => {
        if (!elements.has(name)) elements.set(name, element());
        return elements.get(name);
    };
    const previews = Array.from({ length: 3 }, element);
    const placeholders = Array.from({ length: 3 }, element);
    const retakes = Array.from({ length: 3 }, element);
    const root = { dataset: { saveUrl: '/siswa/7/wajah', maximumSampleDistance }, querySelector: selector => get(selector.slice(6, -1)),
        querySelectorAll: selector => ({ '[data-face-sample]': previews, '[data-sample-placeholder]': placeholders, '[data-retake]': retakes })[selector] };
    const document = { ...element(), hidden: false, querySelector: selector => selector === '[data-face-enrollment]' ? root : { content: 'csrf-token' },
        querySelectorAll: () => [], createElement: () => ({ getContext: () => ({ drawImage() {} }), toDataURL: () => 'data:image/jpeg;base64,preview' }) };
    const track = { ...element(), stopped: false, stop() { this.stopped = true; } };
    const stream = { getTracks: () => [track], getVideoTracks: () => [track] };
    const environment = { ...element(), isSecureContext: true,
        navigator: { mediaDevices: { getUserMedia: async () => stream } },
        loadFaceModels: async () => async () => Array(128).fill(0.1),
        fetch: async () => ({ ok: true, json: async () => ({ count: 3 }) }) };
    Object.assign(get('face-video'), { readyState: 2, videoWidth: 640, videoHeight: 480, play: async () => {} });
    initializeFaceEnrollment(document, environment);
    const click = name => get(name).handlers.click();
    return { get, click, environment, track, document, retakes, previews };
}

test('enrollment switches detectors after stopping the camera and preserves captured samples', async () => {
    const page = setup();
    const loaded = [];
    const captured = [];
    page.environment.loadFaceModels = async (progress, detector) => {
        loaded.push(detector);
        return async () => {
            captured.push(detector);
            return Array(128).fill(0.1);
        };
    };

    assert.equal(page.get('face-detector').disabled, false);
    await page.click('camera-start');
    assert.equal(page.get('face-detector').disabled, true);
    await page.click('face-capture');
    await page.click('camera-stop');
    assert.equal(page.get('face-detector').disabled, false);
    page.get('face-detector').value = 'tiny';
    await page.click('camera-start');
    await page.click('face-capture');
    await page.click('camera-stop');
    page.get('face-detector').value = 'ssd';
    await page.click('camera-start');
    await page.click('face-capture');

    assert.deepEqual(loaded, ['ssd', 'tiny', 'ssd']);
    assert.deepEqual(captured, ['ssd', 'tiny', 'ssd']);
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, false);
});

test('detector choice stays locked during startup and unlocks after model loading fails', async t => {
    const page = setup();
    t.mock.method(console, 'error', () => {});
    let rejectModel;
    page.environment.loadFaceModels = () => new Promise((resolve, reject) => { rejectModel = reject; });

    const starting = page.click('camera-start');
    assert.equal(page.get('face-detector').disabled, true);
    await new Promise(resolve => setImmediate(resolve));
    const error = new Error('Model gagal dimuat.');
    rejectModel(error);
    await starting;

    assert.equal(page.get('face-detector').disabled, false);
    assert.equal(page.get('face-capture').disabled, true);
    assert.equal(page.get('face-message').textContent, 'Model gagal dimuat.');
    assert.equal(page.track.stopped, true);
    page.get('face-detector').value = 'tiny';
    page.environment.loadFaceModels = async (progress, detector) => {
        assert.equal(detector, 'tiny');
        return async () => Array(128).fill(0.1);
    };
    await page.click('camera-start');
    await page.click('face-capture');
    assert.equal(page.get('sample-count').textContent, '1 dari 3 sampel');
});

test('detector choice stays locked while saving with the camera stopped', async () => {
    const page = setup();
    await page.click('camera-start');
    for (let index = 0; index < 3; index++) await page.click('face-capture');
    await page.click('camera-stop');
    let finish;
    page.environment.fetch = () => new Promise(resolve => { finish = resolve; });

    const saving = page.click('face-save');
    assert.equal(page.get('face-detector').disabled, true);
    finish({ ok: true, json: async () => ({ count: 3 }) });
    await saving;

    assert.equal(page.get('face-detector').disabled, false);
});

test('three captures enable saving and successful saving stops the camera', async () => {
    const page = setup();
    assert.equal(page.get('face-save').disabled, true);
    await page.click('camera-start');
    for (let index = 0; index < 3; index++) await page.click('face-capture');
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, false);
    await page.click('face-save');
    assert.equal(page.get('enrollment-status').textContent, '3 sampel terdaftar');
    assert.equal(page.track.stopped, true);
    assert.equal(page.get('face-save').disabled, true);
});

test('deleting stored faces confirms student identity and cancellation keeps the camera and samples', async () => {
    const page = setup();
    page.get('face-delete-form').dataset.identity = 'Zeinal — X TKJ';
    await page.click('camera-start');
    await page.click('face-capture');
    let prompt;
    let prevented = false;
    page.environment.confirm = text => { prompt = text; return false; };
    page.get('face-delete-form').handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, true);
    assert.match(prompt, /Zeinal — X TKJ/);
    assert.equal(page.track.stopped, false);
    assert.equal(page.get('sample-count').textContent, '1 dari 3 sampel');

    page.environment.confirm = () => true;
    prevented = false;
    page.get('face-delete-form').handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, false);
    assert.equal(page.track.stopped, true);
    assert.equal(page.get('face-delete').disabled, true);
    assert.equal(page.get('face-save').disabled, true);
    page.environment.handlers.beforeunload({ preventDefault() { prevented = true; } });
    assert.equal(prevented, false);
});

test('face deletion cannot be submitted while enrollment is being saved', async () => {
    const page = setup();
    await page.click('camera-start');
    for (let i = 0; i < 3; i++) await page.click('face-capture');
    let finish;
    page.environment.fetch = () => new Promise(resolve => { finish = resolve; });
    const saving = page.click('face-save');
    let prevented = false;
    page.environment.confirm = () => { throw new Error('Should not confirm while saving'); };
    page.get('face-delete-form').handlers.submit({ preventDefault() { prevented = true; } });
    assert.equal(prevented, true);
    assert.equal(page.get('face-delete').disabled, true);
    finish({ ok: true, json: async () => ({ count: 3 }) });
    await saving;
    assert.equal(page.get('face-delete').disabled, false);
});

test('enrollment can stop the front camera and capture with the rear camera without losing samples', async () => {
    const page = setup();
    const requests = [];
    const original = page.environment.navigator.mediaDevices.getUserMedia;
    page.environment.navigator.mediaDevices.getUserMedia = async constraints => {
        requests.push(constraints);
        return original();
    };
    await page.click('camera-start');
    await page.click('face-capture');
    assert.equal(page.get('face-video').classList.contains('-scale-x-100'), true);
    assert.equal(page.get('camera-facing').disabled, true);

    await page.click('camera-stop');
    assert.equal(page.track.stopped, true);
    assert.equal(page.get('camera-facing').disabled, false);
    page.get('camera-facing').value = 'environment';
    await page.click('camera-start');
    await page.click('face-capture');

    assert.equal(requests[0].video.facingMode, 'user');
    assert.deepEqual(requests[1].video.facingMode, { exact: 'environment' });
    assert.equal(page.get('face-video').classList.contains('-scale-x-100'), false);
    assert.equal(page.get('sample-count').textContent, '2 dari 3 sampel');
});

test('an old camera ending does not interrupt enrollment after switching cameras', async () => {
    const page = setup();
    await page.click('camera-start');
    const oldCameraEnded = page.track.handlers.ended;
    await page.click('camera-stop');
    page.get('camera-facing').value = 'environment';
    await page.click('camera-start');

    oldCameraEnded();
    await page.click('face-capture');

    assert.equal(page.get('sample-count').textContent, '1 dari 3 sampel');
    assert.equal(page.get('camera-status').textContent, 'Kamera aktif');
    page.track.handlers.ended();
    assert.equal(page.get('face-capture').disabled, true);
    assert.match(page.get('face-message').textContent, /Kamera terputus/);
});

test('enrollment prepares inline muted playback before starting mobile video', async () => {
    const page = setup();
    const video = page.get('face-video');
    video.play = async () => {
        if (!video.muted || !video.playsInline) throw new Error('Mobile playback blocked');
    };

    await page.click('camera-start');
    await page.click('face-capture');

    assert.equal(page.get('sample-count').textContent, '1 dari 3 sampel');
});

test('unavailable rear camera explains how to recover and unlocks camera selection', async () => {
    const page = setup();
    page.get('camera-facing').value = 'environment';
    page.environment.navigator.mediaDevices.getUserMedia = async () => { throw { name: 'OverconstrainedError' }; };

    await page.click('camera-start');

    assert.match(page.get('face-message').textContent, /Kamera belakang tidak tersedia/);
    assert.equal(page.get('camera-facing').disabled, false);
    assert.equal(page.get('face-capture').disabled, true);
});

test('rear camera retries flexible constraints when a mobile browser rejects exact selection', async () => {
    const requests = [];
    const stream = { getVideoTracks: () => [{ getSettings: () => ({ facingMode: 'environment' }) }] };
    const devices = { getUserMedia: async constraints => {
        requests.push(constraints);
        if (requests.length === 1) throw { name: 'OverconstrainedError' };
        return stream;
    } };

    const acquired = await requestFaceCamera(devices, 30000, 'environment');

    assert.equal(acquired, stream);
    assert.deepEqual(requests[1], { video: { facingMode: { ideal: 'environment' } }, audio: false });
});

test('rear camera fallback releases a front camera instead of silently using it', async () => {
    let requests = 0;
    let stopped = false;
    const track = { getSettings: () => ({ facingMode: 'user' }), stop() { stopped = true; } };
    const devices = { getUserMedia: async () => {
        if (++requests === 1) throw { name: 'OverconstrainedError' };
        return { getVideoTracks: () => [track], getTracks: () => [track] };
    } };

    await assert.rejects(requestFaceCamera(devices, 30000, 'environment'), /Kamera belakang tidak tersedia/);

    assert.equal(stopped, true);
    assert.equal(requests, 2);
});

test('a rear camera fallback granted after timeout releases the camera', async () => {
    let grant;
    let requests = 0;
    let stopped = false;
    const devices = { getUserMedia: async () => {
        if (++requests === 1) throw { name: 'OverconstrainedError' };
        return new Promise(resolve => { grant = resolve; });
    } };

    await assert.rejects(requestFaceCamera(devices, 5, 'environment'), { name: 'FaceStartupTimeout' });
    grant({ getVideoTracks: () => [], getTracks: () => [{ stop() { stopped = true; } }] });
    await new Promise(resolve => setImmediate(resolve));

    assert.equal(stopped, true);
});

test('a rear camera rejection after timeout does not open another camera request', async () => {
    let rejectCamera;
    let requests = 0;
    const devices = { getUserMedia: () => {
        requests++;
        return new Promise((resolve, reject) => { rejectCamera = reject; });
    } };

    await assert.rejects(requestFaceCamera(devices, 5, 'environment'), { name: 'FaceStartupTimeout' });
    rejectCamera({ name: 'OverconstrainedError' });
    await new Promise(resolve => setImmediate(resolve));

    assert.equal(requests, 1);
});

test('rear camera permission errors are not retried with another camera', async () => {
    let requests = 0;
    const devices = { getUserMedia: async () => { requests++; throw { name: 'NotAllowedError' }; } };

    await assert.rejects(requestFaceCamera(devices, 30000, 'environment'), { name: 'NotAllowedError' });

    assert.equal(requests, 1);
});

test('capture becomes enabled when delayed video frames become playable', async () => {
    const page = setup();
    page.get('face-video').readyState = 1;
    await page.click('camera-start');
    assert.equal(page.get('face-capture').disabled, true);

    page.get('face-video').readyState = 2;
    page.get('face-video').handlers.canplay();

    assert.equal(page.get('face-capture').disabled, false);
});

test('cancelled model preparation cannot reenable capture when it resolves later', async () => {
    const page = setup();
    let finishModels;
    let modelStarted;
    const reachedModels = new Promise(resolve => { modelStarted = resolve; });
    page.environment.loadFaceModels = () => {
        modelStarted();
        return new Promise(resolve => { finishModels = resolve; });
    };
    const starting = page.click('camera-start');
    await reachedModels;
    assert.equal(page.get('face-capture').disabled, true);
    await page.click('camera-stop');
    finishModels(async () => Array(128).fill(0.1));
    await starting;

    assert.equal(page.get('face-capture').disabled, true);
    assert.equal(page.get('camera-start').disabled, false);
    assert.equal(page.track.stopped, true);
});

test('failed saving preserves samples and permits retry', async () => {
    const page = setup();
    page.environment.fetch = async () => ({ ok: false, status: 422, json: async () => ({ message: 'Sampel ditolak' }) });
    await page.click('camera-start');
    for (let index = 0; index < 3; index++) await page.click('face-capture');
    await page.click('face-save');
    assert.equal(page.get('save-message').textContent, 'Sampel ditolak');
    assert.equal(page.get('face-save').disabled, false);
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
});

test('permission denial shows recovery guidance without enabling capture', async () => {
    const page = setup();
    page.environment.navigator.mediaDevices.getUserMedia = async () => { throw { name: 'NotAllowedError' }; };
    await page.click('camera-start');
    assert.match(page.get('face-message').textContent, /Akses kamera ditolak/);
    assert.equal(page.get('face-capture').disabled, true);
    assert.equal(page.get('camera-start').disabled, false);
});

test('camera permission resolving after cancellation releases its stream', async () => {
    const page = setup();
    let release;
    page.environment.navigator.mediaDevices.getUserMedia = () => new Promise(resolve => { release = resolve; });
    const pending = page.click('camera-start');
    await page.click('camera-stop');
    release({ getTracks: () => [page.track] });
    await pending;
    assert.equal(page.track.stopped, true);
    assert.equal(page.get('face-video').srcObject, null);
});

test('invalid face detection keeps the current slot empty', async () => {
    const page = setup();
    page.environment.loadFaceModels = async () => async () => { throw new Error('Ada lebih dari satu wajah.'); };
    await page.click('camera-start');
    await page.click('face-capture');
    assert.match(page.get('face-message').textContent, /lebih dari satu wajah/);
    assert.equal(page.get('sample-count').textContent, '0 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, true);
});

test('quality rejection cannot save or replace a sample and a successful retake recovers', async () => {
    const page = setup();
    let rejectQuality = true;
    let saves = 0;
    page.environment.loadFaceModels = async () => async (canvas, options) => {
        assert.equal(options.checkQuality, true);
        if (rejectQuality) throw new Error('Gambar wajah kurang tajam. Diam sebentar dan periksa fokus kamera.');
        return Array(128).fill(0.1);
    };
    page.environment.fetch = async () => { saves++; return { ok: true, json: async () => ({ count: 3 }) }; };
    await page.click('camera-start');
    await page.click('face-capture');
    await page.click('face-save');
    assert.equal(saves, 0);
    assert.equal(page.get('sample-count').textContent, '0 dari 3 sampel');
    assert.equal(page.previews[0].src, undefined);
    assert.match(page.get('face-message').textContent, /kurang tajam/);

    rejectQuality = false;
    for (let i = 0; i < 3; i++) await page.click('face-capture');
    const previous = page.previews[1].src;
    page.retakes[1].handlers.click();
    rejectQuality = true;
    await page.click('face-capture');
    await page.click('face-save');
    assert.equal(saves, 0);
    assert.equal(page.previews[1].src, previous);
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, true);

    rejectQuality = false;
    await page.click('face-capture');
    await page.click('face-save');
    assert.equal(saves, 1);
});

test('retaking replaces one slot and clearing previews leaves registered status intact', async () => {
    const page = setup();
    await page.click('camera-start');
    for (let index = 0; index < 3; index++) await page.click('face-capture');
    page.retakes[1].handlers.click();
    assert.equal(page.get('face-save').disabled, true);
    await page.click('face-capture');
    await page.click('face-save');
    await page.click('face-reset');
    assert.equal(page.get('sample-count').textContent, '0 dari 3 sampel');
    assert.equal(page.get('enrollment-status').textContent, '3 sampel terdaftar');
    assert.ok(page.previews.every(preview => !preview.src));
});

test('third sample must match both earlier positions and can recover after retaking', async () => {
    const page = setup();
    const coordinates = [0, -0.4, 0.4, -0.2];
    page.environment.loadFaceModels = async () => async () => {
        const descriptor = Array(128).fill(0.125);
        descriptor[0] = coordinates.shift();
        return descriptor;
    };
    await page.click('camera-start');
    for (let i = 0; i < 3; i++) await page.click('face-capture');

    assert.equal(page.get('sample-count').textContent, '2 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, true);
    assert.equal(page.previews[2].src, undefined);
    assert.match(page.get('face-message').textContent, /Sedikit ke kanan.*Sedikit ke kiri.*0.8000/);

    await page.click('face-capture');
    assert.equal(page.get('face-save').disabled, false);
});

test('retaking an earlier position checks every other slot and preserves its previous preview on rejection', async () => {
    const page = setup();
    const coordinates = [0, -0.2, 0.3, -0.4];
    page.environment.loadFaceModels = async () => async () => {
        const descriptor = Array(128).fill(0.125);
        descriptor[0] = coordinates.shift();
        return descriptor;
    };
    await page.click('camera-start');
    for (let i = 0; i < 3; i++) await page.click('face-capture');
    const previous = page.previews[0].src;
    page.retakes[0].handlers.click();
    await page.click('face-capture');

    assert.equal(page.previews[0].src, previous);
    assert.equal(page.get('face-save').disabled, true);
    assert.match(page.get('face-message').textContent, /Menghadap depan.*Sedikit ke kanan.*0.7000/);
});

test('browser uses the configured threshold and accepts equality', async () => {
    const page = setup('0.25');
    const coordinates = [0, 0.25, 0.3];
    page.environment.loadFaceModels = async () => async () => {
        const descriptor = Array(128).fill(0.125);
        descriptor[0] = coordinates.shift();
        return descriptor;
    };
    await page.click('camera-start');
    await page.click('face-capture');
    await page.click('face-capture');
    assert.equal(page.get('sample-count').textContent, '2 dari 3 sampel');
    await page.click('face-capture');
    assert.equal(page.get('sample-count').textContent, '2 dari 3 sampel');
    assert.match(page.get('face-message').textContent, /Maksimum 0.2500/);
});

test('server consistency errors show all conflicting pairs without losing captured samples', async () => {
    const page = setup();
    page.environment.fetch = async () => ({ ok: false, status: 422, json: async () => ({ errors: {
        'samples.0': ['Depan dan kiri belum konsisten.', 'Depan dan kanan belum konsisten.'],
        'samples.1': ['Depan dan kiri belum konsisten.'],
    } }) });
    await page.click('camera-start');
    for (let i = 0; i < 3; i++) await page.click('face-capture');
    await page.click('face-save');

    assert.equal(page.get('save-message').textContent, 'Depan dan kiri belum konsisten. Depan dan kanan belum konsisten.');
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
    assert.ok(page.previews.every(preview => preview.src));
});

for (const confirmed of [false, true]) {
    test(`similarity warning ${confirmed ? 'saves only after confirmation' : 'preserves samples when cancelled'}`, async () => {
        const page = setup();
        const requests = [];
        let prompt;
        page.environment.confirm = text => { prompt = text; return confirmed; };
        page.environment.fetch = async (url, options) => {
            requests.push(JSON.parse(options.body));
            return requests.length === 1
                ? { ok: false, status: 409, json: async () => ({ code: 'face_similarity_review', message: 'Periksa identitas.',
                    candidates: [{ siswa_id: 8, student: '<b>Budi</b>', distance: 0.3 }], threshold: 0.45, confirmation_token: 'review-token' }) }
                : { ok: true, json: async () => ({ count: 3 }) };
        };
        await page.click('camera-start');
        for (let i = 0; i < 3; i++) await page.click('face-capture');
        await page.click('face-save');

        assert.match(prompt, /<b>Budi<\/b>.*0.3000/);
        assert.equal(requests.length, confirmed ? 2 : 1);
        assert.equal(requests[0].similarity_confirmation, undefined);
        if (confirmed) {
            assert.equal(requests[1].similarity_confirmation, 'review-token');
            assert.deepEqual(requests[1].samples, requests[0].samples);
            assert.equal(page.track.stopped, true);
        } else {
            assert.match(page.get('save-message').textContent, /Belum disimpan/);
            assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
            assert.equal(page.track.stopped, false);
        }
    });
}

test('changed similarity review does not silently retry confirmation', async () => {
    const page = setup();
    let requests = 0;
    let prompts = 0;
    page.environment.confirm = () => { prompts++; return true; };
    page.environment.fetch = async () => {
        requests++;
        return { ok: false, status: 409, json: async () => ({ code: 'face_similarity_review', message: 'Periksa kembali kandidat.',
            candidates: [{ siswa_id: 8, student: 'Budi', distance: 0.3 }], threshold: 0.45, confirmation_token: 'token' }) };
    };
    await page.click('camera-start');
    for (let i = 0; i < 3; i++) await page.click('face-capture');
    await page.click('face-save');

    assert.equal(requests, 2);
    assert.equal(prompts, 1);
    assert.match(page.get('save-message').textContent, /Periksa kembali/);
    assert.equal(page.get('sample-count').textContent, '3 dari 3 sampel');
    assert.equal(page.get('face-save').disabled, false);
});
