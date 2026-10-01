import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeFaceEnrollment, requestFaceCamera } from '../../resources/js/siswa-face-enrollment.js';

test('an unanswered camera request times out and releases a stream granted afterward', async () => {
    let grant;
    let stopped = false;
    const request = requestFaceCamera({ getUserMedia: () => new Promise(resolve => { grant = resolve; }) }, 5);

    await assert.rejects(request, { name: 'FaceStartupTimeout' });
    grant({ getTracks: () => [{ stop() { stopped = true; } }] });
    await Promise.resolve();

    assert.equal(stopped, true);
});

function setup() {
    const element = () => {
        const classes = new Set();
        return { dataset: {}, handlers: {}, disabled: false, textContent: '', classList: {
            add: value => classes.add(value), remove: value => classes.delete(value),
            toggle(value, enabled) { enabled ? classes.add(value) : classes.delete(value); },
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
    const root = { dataset: { saveUrl: '/siswa/7/wajah' }, querySelector: selector => get(selector.slice(6, -1)),
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
