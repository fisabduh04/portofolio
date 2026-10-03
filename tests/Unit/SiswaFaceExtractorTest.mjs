import test from 'node:test';
import assert from 'node:assert/strict';
import { createFaceExtractor } from '../../resources/js/siswa-face-extractor.js';

function fixture({ brightness = 128, textured = true, box = { x: 100, y: 100, width: 160, height: 160 }, count = 1 } = {}) {
    const descriptor = Array(128).fill(0.1);
    const image = { width: 64, height: 64, data: new Uint8ClampedArray(64 * 64 * 4) };
    for (let i = 0; i < 64 * 64; i++) {
        const value = brightness + (textured ? ((i + Math.floor(i / 64)) % 2 ? 10 : -10) : 0);
        image.data.set([value, value, value, 255], i * 4);
    }
    const draws = [];
    const context = { drawImage: (...args) => draws.push(args), getImageData: () => image };
    const canvas = { width: 640, height: 480, ownerDocument: { createElement: () => ({ getContext: () => context }) } };
    const api = { TinyFaceDetectorOptions: class {}, detectAllFaces: () => ({ withFaceLandmarks: () => ({
        withFaceDescriptors: async () => Array.from({ length: count }, () => ({ detection: { box }, descriptor })),
    }) }) };
    return { canvas, descriptor, draws, extract: createFaceExtractor(api) };
}

for (const [name, options, message] of [
    ['dark', { brightness: 30 }, /terlalu gelap/],
    ['overexposed', { brightness: 235 }, /terlalu terang/],
    ['blurred', { textured: false }, /kurang tajam/],
    ['small', { box: { x: 100, y: 100, width: 99, height: 160 } }, /terlalu kecil/],
    ['cropped', { box: { x: 550, y: 100, width: 160, height: 160 } }, /tepi gambar/],
    ['invalid position', { box: { x: NaN, y: 100, width: 160, height: 160 } }, /tidak valid/],
    ['no face', { count: 0 }, /belum terdeteksi/],
    ['multiple faces', { count: 2 }, /lebih dari satu wajah/],
]) {
    test(`enrollment rejects ${name} capture with recovery guidance`, async () => {
        const { canvas, extract } = fixture(options);
        await assert.rejects(extract(canvas, { checkQuality: true }), message);
    });
}

test('quality checks the face crop and returns the unchanged descriptor for an acceptable image', async () => {
    const { canvas, extract, descriptor, draws } = fixture();
    assert.deepEqual(await extract(canvas, { checkQuality: true }), descriptor);
    assert.deepEqual(draws, [[canvas, 100, 100, 160, 160, 0, 0, 64, 64]]);
});

test('attendance extraction does not enable enrollment quality rejection', async () => {
    const { canvas, extract, descriptor, draws } = fixture({ brightness: 20 });
    assert.deepEqual(await extract(canvas), descriptor);
    assert.equal(draws.length, 0);
});

test('unreadable quality pixels fail closed', async () => {
    const { canvas, extract } = fixture();
    canvas.ownerDocument.createElement = () => ({ getContext: () => null });
    await assert.rejects(extract(canvas, { checkQuality: true }), /belum dapat diperiksa/);
});
