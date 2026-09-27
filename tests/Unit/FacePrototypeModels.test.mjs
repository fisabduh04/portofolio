import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { after, before, test } from 'node:test';
import { spawn, execFileSync } from 'node:child_process';
import { createServer } from 'node:net';
import { fileURLToPath } from 'node:url';
import { setTimeout as delay } from 'node:timers/promises';
import { motionSignals } from '../../resources/js/face-prototype-challenge.js';

let api;
const modelDirectory = new URL('../../node_modules/@vladmandic/face-api/model/', import.meta.url);

before(async () => {
    // Exercise the shipped browser bundle on CPU, not the separate native Node build.
    const runtimeProcess = globalThis.process;
    try {
        globalThis.process = undefined;
        api = await import('@vladmandic/face-api/dist/face-api.esm.js');
    } finally {
        globalThis.process = runtimeProcess;
    }
    api.tf.env().setPlatform('synthetic-test', {
        now: () => performance.now(),
        fetch: globalThis.fetch,
        encode: (value) => new TextEncoder().encode(value),
        decode: (value) => new TextDecoder().decode(value),
        isTypedArray: (value) => ArrayBuffer.isView(value) && !(value instanceof DataView),
    });
    const noDom = () => { throw new Error('This test accepts tensors only, not browser elements.'); };
    api.env.setEnv({
        Canvas: class {}, Image: class {}, Video: class {}, ImageData: class {},
        CanvasRenderingContext2D: class {},
        createCanvasElement: noDom, createImageElement: noDom, createVideoElement: noDom,
        fetch: globalThis.fetch,
    });
    await api.tf.setBackend('cpu');
    await api.tf.ready();
    for (const [name, net] of [
        ['tiny_face_detector', api.nets.tinyFaceDetector],
        ['face_landmark_68_tiny', api.nets.faceLandmark68TinyNet],
        ['face_recognition', api.nets.faceRecognitionNet],
    ]) {
        const manifest = JSON.parse(await readFile(new URL(`${name}_model-weights_manifest.json`, modelDirectory), 'utf8'));
        const weightMap = {};
        for (const group of manifest) {
            const chunks = await Promise.all(group.paths.map((path) => readFile(new URL(path, modelDirectory))));
            const bytes = Buffer.concat(chunks);
            Object.assign(weightMap, api.tf.io.decodeWeights(
                bytes.buffer.slice(bytes.byteOffset, bytes.byteOffset + bytes.byteLength), group.weights,
            ));
        }
        net.loadFromWeightMap(weightMap);
    }
});

after(() => {
    api?.nets.tinyFaceDetector.dispose();
    api?.nets.faceLandmark68TinyNet.dispose();
    api?.nets.faceRecognitionNet.dispose();
});

test('installed browser detector rejects a blank synthetic image', async () => {
    const frame = api.tf.zeros([160, 160, 3]);
    try {
        for (const inputSize of [320, 224, 416]) {
            const faces = await api.detectAllFaces(frame,
                new api.TinyFaceDetectorOptions({ inputSize, scoreThreshold: 0.65 }));
            assert.equal(faces.length, 0);
        }
    } finally { frame.dispose(); }
});

test('installed landmark and recognition models produce the expected finite vector', async () => {
    const frame = api.tf.zeros([160, 160, 3]);
    try {
        const landmarks = await api.nets.faceLandmark68TinyNet.detectLandmarks(frame);
        assert.equal(landmarks.positions.length, 68);
        const detection = new api.FaceDetection(0.9, new api.Rect(0, 0, 1, 1), { width: 160, height: 160 });
        const extracted = await new api.DetectAllFaceLandmarksTask(
            Promise.resolve([{ detection }]), frame, true,
        ).withFaceDescriptors();
        const descriptor = extracted[0].descriptor;
        assert.equal(descriptor.length, 128);
        assert.ok(Array.from(descriptor).every(Number.isFinite));
        assert.ok(Object.values(motionSignals(extracted[0].landmarks)).every(Number.isFinite));
        assert.equal(typeof api.DetectAllFaceLandmarksTask, 'function');
        assert.equal(api.version, '1.7.15');
    } finally { frame.dispose(); }
});

test('isolated launcher serves local assets and accepts its displayed operator code', { timeout: 30000 }, async () => {
    const reservation = createServer();
    await new Promise((resolve) => reservation.listen(0, '127.0.0.1', resolve));
    const port = reservation.address().port;
    await new Promise((resolve) => reservation.close(resolve));
    const child = spawn('php', ['scripts/face-prototype.php', 'serve'], {
        cwd: fileURLToPath(new URL('../../', import.meta.url)),
        env: { ...process.env, FACE_PROTOTYPE_PORT: String(port) },
        windowsHide: true, stdio: ['ignore', 'pipe', 'pipe'],
    });
    let output = '';
    child.stdout.on('data', (data) => { output += data.toString(); });
    child.stderr.on('data', (data) => { output += data.toString(); });
    try {
        const deadline = Date.now() + 20000;
        while (!output.includes('Kode operator sementara (salin ke halaman):') && Date.now() < deadline && child.exitCode === null) await delay(100);
        const token = output.match(/Kode operator sementara \(salin ke halaman\): ([a-f0-9]{48})/)?.[1];
        assert.ok(token, 'Launcher must supply a temporary operator code.');
        assert.ok(output.includes('Development Server'), 'Isolated server must start.');
        assert.ok(output.includes('Server dan kode operator sudah terverifikasi.'));
        assert.ok(output.includes(`#code=${token}`));
        const base = `http://127.0.0.1:${port}`;
        const page = await fetch(`${base}/uji/absensi-wajah`);
        assert.equal(page.status, 200);
        const pageHtml = await page.text();
        assert.match(pageHtml, /Hasil uji tidak mencatat absensi/);
        const guideOption = pageHtml.match(/<input[^>]+id="show-position-guide"[^>]*>/)?.[0];
        assert.ok(guideOption, 'The alignment guide must be optional.');
        assert.doesNotMatch(guideOption, /\bchecked\b/);
        assert.match(pageHtml, /value="detailed"/);
        assert.match(pageHtml, /id="challenge" disabled/);
        assert.match(pageHtml, /id="motion-status"/);
        assert.match(pageHtml, /Tidak ada bukti liveness terverifikasi server/);
        const versionedScript = pageHtml.match(/src="(\/prototype-assets\/app\.js\?v=[a-f0-9]{64})"/)?.[1];
        assert.ok(versionedScript, 'The camera script must be versioned so updates replace stale browser assets.');
        const scriptResponse = await fetch(`${base}${versionedScript}`);
        assert.equal(scriptResponse.status, 200);
        assert.equal(scriptResponse.headers.get('cache-control'), 'no-store, private');
        for (const asset of ['app.js', 'app.css', 'face-api.js']) {
            const response = await fetch(`${base}/prototype-assets/${asset}`);
            assert.equal(response.status, 200);
            if (asset !== 'face-api.js') assert.equal(response.headers.get('cache-control'), 'no-store, private');
            assert.ok((await response.arrayBuffer()).byteLength > 0);
        }
        for (const name of ['tiny_face_detector', 'face_landmark_68_tiny', 'face_recognition']) {
            const response = await fetch(`${base}/prototype-assets/models/${name}_model-weights_manifest.json`);
            assert.equal(response.status, 200);
            for (const group of await response.json()) {
                for (const path of group.paths) {
                    const weights = await fetch(`${base}/prototype-assets/models/${path}`);
                    assert.equal(weights.status, 200);
                    assert.ok((await weights.arrayBuffer()).byteLength > 0);
                }
            }
        }
        const descriptor = [1, ...Array(127).fill(0)];
        const body = JSON.stringify({ direction: 'masuk', consent: true,
            model: 'face-api-1.7.15-tiny-landmark68-recognition128', descriptor,
            references: [{ alias: 'UJI-001', descriptor }],
        });
        const headers = { 'Content-Type': 'application/json', Accept: 'application/json', Authorization: `Bearer ${token}` };
        const access = await fetch(`${base}/uji/absensi-wajah/access`, { method: 'POST', headers, body: '{}' });
        assert.equal(access.status, 200);
        assert.equal((await access.json()).authorized, true);
        const response = await fetch(`${base}/uji/absensi-wajah/match`, { method: 'POST', headers, body });
        assert.equal(response.status, 200, 'The code displayed by the launcher must authorize matching.');
        const result = await response.json();
        assert.equal(result.status, 'candidate');
        assert.equal(result.attendance_saved, false);
        assert.equal(typeof result.opcode_cache_enabled, 'boolean');
        for (const stage of ['bootstrap_ms', 'guard_ms', 'dispatch_validation_ms']) {
            assert.ok(Number.isFinite(result.timings[stage]) && result.timings[stage] >= 0);
        }
        assert.equal(result.candidates[0].alias, 'UJI-001');
        const refused = await fetch(`${base}/uji/absensi-wajah/match`, {
            method: 'POST', headers: { ...headers, Authorization: 'Bearer wrong' }, body,
        });
        assert.equal(refused.status, 401);
        assert.equal((await fetch(`${base}/absensi/harian`)).status, 404);
    } finally {
        if (child.pid && child.exitCode === null) {
            if (process.platform === 'win32') execFileSync('taskkill', ['/pid', String(child.pid), '/T', '/F'], { windowsHide: true, stdio: 'ignore' });
            else child.kill('SIGTERM');
        }
    }
});

test('launcher rejects an occupied port without displaying a new operator code', { timeout: 20000 }, async () => {
    const reservation = createServer((socket) => socket.end());
    await new Promise((resolve) => reservation.listen(0, '127.0.0.1', resolve));
    try {
        const child = spawn('php', ['scripts/face-prototype.php', 'serve'], {
            cwd: fileURLToPath(new URL('../../', import.meta.url)),
            env: { ...process.env, FACE_PROTOTYPE_PORT: String(reservation.address().port) },
            windowsHide: true, stdio: ['ignore', 'pipe', 'pipe'],
        });
        let output = '';
        child.stdout.on('data', (data) => { output += data; });
        child.stderr.on('data', (data) => { output += data; });
        const code = await new Promise((resolve, reject) => { child.once('error', reject); child.once('close', resolve); });
        assert.equal(code, 1);
        assert.match(output, /masih dipakai server lain/);
        assert.doesNotMatch(output, /Kode operator sementara|#code=/);
    } finally { await new Promise((resolve) => reservation.close(resolve)); }
});
