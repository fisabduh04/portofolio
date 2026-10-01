import test from 'node:test';
import assert from 'node:assert/strict';
import { loadFaceWeights, waitForFaceStep } from '../../resources/js/siswa-face-weights.js';

test('a stalled startup step reports a recoverable error instead of waiting forever', async () => {
    await assert.rejects(waitForFaceStep(new Promise(() => {}), 5, 'Model belum siap'), {
        name: 'FaceStartupTimeout', message: 'Model belum siap',
    });
});

test('a ready startup step returns its result', async () => {
    assert.equal(await waitForFaceStep(Promise.resolve('ready'), 100, 'timeout'), 'ready');
});

test('model shards resolve to the application host instead of the build hostname', async () => {
    let requested;
    let loaded;
    const weightMap = { descriptor: 'decoded tensor' };
    const io = { async loadWeights(manifest, prefix) {
        requested = prefix + (prefix.endsWith('/') ? '' : '/') + manifest[0].paths[0];
        return weightMap;
    } };

    await loadFaceWeights({ loadFromWeightMap: weights => { loaded = weights; } }, io,
        [{ paths: ['original.bin'], weights: [] }], '/build/assets/recognition-123.bin', 'https://portofolio.test/siswa/593');

    assert.equal(requested, 'https://portofolio.test/build/assets/recognition-123.bin');
    assert.equal(loaded, weightMap);
});

test('model shards retain the development server origin and query string', async () => {
    let requested;
    const io = { async loadWeights(manifest, prefix) {
        requested = prefix + manifest[0].paths[0];
        return {};
    } };

    await loadFaceWeights({ loadFromWeightMap() {} }, io,
        [{ paths: ['original.bin'], weights: [] }], 'https://localhost:5173/models/recognition.bin?import', 'https://portofolio.test/siswa/593');

    assert.equal(requested, 'https://localhost:5173/models/recognition.bin?import');
});
