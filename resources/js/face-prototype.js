import '../css/face-prototype.css';
import { metricsCsv } from './face-prototype-export.js';
import { trialScenario, trialError, failureOutcome, trialWarning, summarizeTrials } from './face-prototype-trials.js';
import { detectorInputSize, graphicsDiagnostic, initializeFaceBackend } from './face-prototype-performance.js';
import { facePositionAssessment, faceImageQuality, createPassageGate } from './face-prototype-quality.js';
import { cameraPrerequisiteMessage, cameraActivationIssue, pauseHiddenCamera, faceRequestError, scanFailureMessage, operatorCodeFromLink, verifyOperatorCode } from './face-prototype-camera.js';

const root = document.getElementById('prototype');
const element = (id) => document.getElementById(id);
const video = element('video');
const status = (message) => { element('status').textContent = message; };
const timing = (key, value) => { element(`timing-${key}`).textContent = `${value.toFixed(1)} ms`; };
const references = [];
const samples = [];
const passage = createPassageGate();
let faceapi;
let stream;
let busy = false;
let repeating = false;
let timer;
let generation = 0;
let pendingRequest;
let graphics = { category: 'unknown', label: '' };

function controls() {
    const permitted = element('consent').checked && element('token').value.trim().length >= 32;
    element('camera-help').textContent = cameraPrerequisiteMessage(element('token').value, element('consent').checked);
    element('camera-help').hidden = busy || !!stream;
    element('start').disabled = busy || !!stream;
    element('start').textContent = stream ? 'Kamera sudah aktif' : busy ? 'Sedang memproses…' : 'Aktifkan kamera';
    element('stop').disabled = !stream && !busy;
    element('enroll').disabled = !stream || busy || repeating || !permitted;
    element('scan').disabled = !stream || busy || repeating || !references.length || !permitted;
    element('continuous').disabled = !stream || (!repeating && (busy || !references.length || !permitted));
    element('continuous').textContent = repeating ? 'Hentikan uji berulang' : 'Mulai uji berulang';
    element('alias').disabled = busy || repeating;
    element('camera').disabled = busy || !!stream;
    element('performance-profile').disabled = busy || repeating;
    element('trial-scenario').disabled = busy || repeating;
    if (['photo', 'replay', 'empty'].includes(element('trial-scenario').value)) element('enroll').disabled = true;
    element('download').disabled = !samples.length;
}

function stop() {
    generation++;
    repeating = false;
    clearTimeout(timer);
    pendingRequest?.abort();
    stream?.getTracks().forEach((track) => track.stop());
    stream = null;
    video.srcObject = null;
    element('position-guide').hidden = true;
    element('position-help').textContent = 'Kamera berhenti. Aktifkan kamera untuk melihat panduan posisi.';
    controls();
}

function clearResults() {
    stop();
    references.length = 0;
    samples.length = 0;
    passage.reset();
    element('csv-preview').value = '';
    element('csv-fallback').hidden = true;
    element('download-status').textContent = '';
    element('references').textContent = 'Belum ada referensi.';
    element('result').textContent = 'Belum ada hasil';
    element('candidate').textContent = 'Kandidat akan ditampilkan dengan alias peserta.';
    element('received').textContent = '';
    element('payload').textContent = '';
    element('statistics').textContent = 'Belum ada sampel.';
    element('trial-warning').textContent = '';
    root.querySelectorAll('[id^="timing-"]').forEach((cell) => { cell.textContent = '—'; });
    status('Referensi dan hasil di memori tab sudah dihapus.');
    controls();
}

async function start() {
    if (busy || stream) return;
    const issue = cameraActivationIssue(element('token').value, element('consent').checked);
    if (issue) {
        status(issue.message);
        element('operator-status').textContent = issue.message;
        element(issue.field).focus();
        return;
    }
    const run = ++generation;
    let stage = 'access';
    busy = true;
    controls();
    try {
        status('Memeriksa kode akses. Tunggu sebentar…');
        element('operator-status').textContent = 'Memeriksa kode operator ke server…';
        pendingRequest = new AbortController();
        const accessRequest = pendingRequest;
        const timeout = setTimeout(() => accessRequest.abort(), 10000);
        try {
            await verifyOperatorCode(root.dataset.accessEndpoint, element('token').value, accessRequest.signal);
        } finally { clearTimeout(timeout); pendingRequest = null; }
        if (run !== generation) return;
        element('operator-status').textContent = 'Kode operator diterima server.';
        stage = 'camera';
        if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
            throw new Error('Kamera memerlukan HTTPS tepercaya atau localhost melalui USB port forwarding.');
        }
        if (!faceapi) {
            status('Memuat model lokal. Pemuatan pertama dapat memerlukan waktu.');
            const started = performance.now();
            const moduleUrl = '/prototype-assets/face-api.js';
            const api = await import(/* @vite-ignore */ moduleUrl);
            const backend = await initializeFaceBackend(api.tf);
            element('performance-info').textContent = backend === 'cpu'
                ? 'Mode CPU aktif karena WebGL tidak tersedia. Memuat model; pemindaian mungkin lebih lambat.'
                : 'Backend WebGL aktif. Memuat model wajah…';
            await Promise.all([
                api.nets.tinyFaceDetector.loadFromUri('/prototype-assets/models'),
                api.nets.faceLandmark68TinyNet.loadFromUri('/prototype-assets/models'),
                api.nets.faceRecognitionNet.loadFromUri('/prototype-assets/models'),
            ]);
            faceapi = api;
            graphics = graphicsDiagnostic(document);
            timing('model', performance.now() - started);
        }
        if (run !== generation) return;
        const camera = await navigator.mediaDevices.getUserMedia({ audio: false,
            video: { facingMode: { ideal: element('camera').value }, width: { ideal: 640 }, height: { ideal: 480 } },
        });
        if (run !== generation) { camera.getTracks().forEach((track) => track.stop()); return; }
        stream = camera;
        video.srcObject = stream;
        await video.play();
        if (run !== generation) return;
        element('position-guide').hidden = !element('show-position-guide').checked;
        element('position-help').textContent = 'Pastikan seluruh wajah terlihat jelas di gambar kamera. Ambil referensi atau klik Pindai sekali untuk memeriksa posisi.';
        element('performance-info').textContent = `Backend: ${faceapi.tf.getBackend()}. Video: ${video.videoWidth} × ${video.videoHeight}. ${faceapi.tf.getBackend() === 'cpu'
            ? 'Mode CPU cadangan; kamera tetap dapat diuji, pemindaian mungkin lebih lambat.' : graphics.label}`;
        status('Kamera aktif. Ambil referensi satu peserta terlebih dahulu.');
    } catch (error) {
        if (run !== generation) return;
        stop();
        if (stage === 'access') {
            element('operator-status').textContent = scanFailureMessage(error);
            status('Kamera belum diaktifkan karena kode akses belum terverifikasi.');
        } else {
            status(`Kamera/model belum siap: ${error.message}. Periksa izin kamera, dukungan WebGL, dan aset model lokal.`);
        }
    } finally {
        busy = false;
        controls();
    }
}

async function capture(checkPassage = false, run = generation) {
    element('position-help').textContent = 'Memeriksa gambar saat pemindaian...';
    if (video.readyState < 2 || !video.videoWidth) throw trialError('VIDEO_NOT_READY', 'Tunggu gambar kamera siap.');
    const started = performance.now();
    const inputSize = checkPassage ? detectorInputSize(element('performance-profile').value) : 320;
    const frame = document.createElement('canvas');
    const scale = Math.min(1, 640 / video.videoWidth);
    frame.width = Math.round(video.videoWidth * scale);
    frame.height = Math.round(video.videoHeight * scale);
    frame.getContext('2d').drawImage(video, 0, 0, frame.width, frame.height);
    try {
        const detections = await faceapi.detectAllFaces(frame,
            new faceapi.TinyFaceDetectorOptions({ inputSize, scoreThreshold: 0.65 }));
        const detected = performance.now();
        if (run !== generation) throw new Error('Pemindaian dibatalkan.');
        const wasLocked = checkPassage && passage.locked;
        if (!wasLocked) timing('detect', detected - started);
        if (checkPassage && passage.observe(detections.length)) {
            const waiting = new Error('Tidak ada pencocokan ulang. Silakan keluar dari gambar; kamera hanya memeriksa apakah area sudah kosong.');
            waiting.code = 'PASSAGE_WAIT';
            throw waiting;
        }
        if (wasLocked && !passage.locked) {
            const ready = new Error('Area kosong sudah teramati dua kali. Silakan masuk kembali untuk pemindaian berikutnya.');
            ready.code = 'PASSAGE_READY';
            throw ready;
        }
        if (detections.length !== 1) throw trialError(detections.length ? 'MULTIPLE_FACES' : 'NO_FACE', detections.length ? 'Lebih dari satu wajah. Uji satu peserta setiap kali.' : 'Wajah belum terdeteksi. Hadapkan wajah dan perbaiki pencahayaan.');
        const box = detections[0].box;
        const positionIssue = facePositionAssessment(box, frame.width, frame.height);
        if (positionIssue) throw trialError(positionIssue.code, positionIssue.message);
        const crop = document.createElement('canvas');
        crop.width = crop.height = 64;
        let quality;
        try {
            const context = crop.getContext('2d', { willReadFrequently: true });
            context.drawImage(frame, box.x, box.y, box.width, box.height, 0, 0, 64, 64);
            quality = faceImageQuality(context.getImageData(0, 0, 64, 64));
        } finally { crop.width = crop.height = 0; }
        if (quality.issue) throw trialError('IMAGE_QUALITY', quality.issue);
        element('position-help').textContent = 'Posisi dan kualitas gambar pada pemindaian ini memenuhi syarat. Memproses wajah...';
        const qualityChecked = performance.now();
        timing('quality', qualityChecked - detected);
        // Reuse this frozen frame and its detection; do not detect twice in one measurement.
        const extracted = await new faceapi.DetectAllFaceLandmarksTask(
            Promise.resolve(detections.map((detection) => ({ detection }))), frame, true,
        ).withFaceDescriptors();
        const finished = performance.now();
        const descriptor = Array.from(extracted[0].descriptor);
        if (descriptor.length !== 128 || !descriptor.every(Number.isFinite)) throw new Error('Ekstraksi vektor gagal.');
        timing('extract', finished - qualityChecked);
        return { descriptor, started, inputSize, frameWidth: frame.width, frameHeight: frame.height,
            detect_ms: detected - started, quality_ms: qualityChecked - detected, extract_ms: finished - qualityChecked };
    } catch (error) {
        if (run === generation) element('position-help').textContent = scanFailureMessage(error);
        throw error;
    } finally {
        frame.width = frame.height = 0;
    }
}

async function enroll() {
    if (references.length >= Number(root.dataset.maxReferences)) {
        status('Batas total referensi tercapai. Hapus referensi untuk memulai uji baru.');
        return;
    }
    const run = generation;
    const alias = element('alias').value;
    busy = true;
    controls();
    try {
        const captured = await capture();
        if (run !== generation) return;
        references.push({ alias, descriptor: captured.descriptor });
        element('position-help').textContent = 'Referensi berhasil diambil. Pertahankan jarak dan posisi saat melakukan uji.';
        const counts = {};
        references.forEach((reference) => { counts[reference.alias] = (counts[reference.alias] || 0) + 1; });
        element('references').textContent = Object.entries(counts).map(([name, count]) => `${name}: ${count} referensi`).join(' · ');
        status(`Referensi ${alias} siap. Pilih alias lain untuk peserta berikutnya atau mulai pemindaian.`);
    } catch (error) {
        if (run === generation) {
            status(error.message);
            element('position-help').textContent = scanFailureMessage(error);
        }
    }
    finally { busy = false; controls(); }
}

const nextPaint = () => new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));

function recordTrial(sample) {
    samples.push({ ...sample, sample: (samples.at(-1)?.sample || 0) + 1 });
    if (samples.length > 500) samples.shift();
    const summary = summarizeTrials(samples, sample.detector_input, sample.scenario);
    const latency = summary.p50 === null ? 'Belum ada waktu pencocokan selesai.'
        : `Waktu pencocokan selesai: p50 ${summary.p50.toFixed(1)} ms, p95 ${summary.p95.toFixed(1)} ms.`;
    element('statistics').textContent = `${summary.attempts} percobaan untuk skenario/mode ini: ${summary.candidates} kandidat, ${summary.completed - summary.candidates} tidak dikenal/meragukan, ${summary.rejected} penolakan gambar, ${summary.errors} kesalahan. ${latency} CSV berisi ${samples.length} percobaan terakhir (maksimal 500), bukan jumlah orang.`;
    element('trial-warning').textContent = trialWarning(sample.scenario, sample.status);
}

async function scan() {
    if (busy || !stream) return;
    const run = generation;
    const attempted = performance.now();
    const scenario = trialScenario(element('trial-scenario').value);
    const inputSize = detectorInputSize(element('performance-profile').value);
    let stage = 'capture';
    busy = true;
    element('result').textContent = passage.locked ? 'Sudah diproses — menunggu area kosong' : 'Memproses pemindaian…';
    if (!passage.locked) {
        element('candidate').textContent = '';
        element('received').textContent = '';
        element('payload').textContent = '';
        root.querySelectorAll('[id^="timing-"]').forEach((cell) => {
            if (cell.id !== 'timing-model') cell.textContent = '—';
        });
    }
    controls();
    try {
        const captured = await capture(true, run);
        if (run !== generation) return;
        stage = 'server';
        pendingRequest = new AbortController();
        const timeout = setTimeout(() => pendingRequest?.abort(), 15000);
        const body = JSON.stringify({ direction: 'masuk', consent: element('consent').checked,
            model: root.dataset.model, descriptor: captured.descriptor, references });
        const sent = performance.now();
        let response;
        let result;
        try {
            response = await fetch(root.dataset.endpoint, { method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', Authorization: `Bearer ${element('token').value.trim()}` },
                credentials: 'omit', cache: 'no-store', body, signal: pendingRequest.signal,
            });
            result = await response.json();
        } finally { clearTimeout(timeout); pendingRequest = null; }
        const received = performance.now();
        if (run !== generation) return;
        timing('roundtrip', received - sent);
        if (!response.ok) {
            repeating = false;
            throw new Error(faceRequestError(response.status, result.message));
        }
        if (result.status === 'candidate') passage.accept();
        element('position-help').textContent = result.status === 'candidate'
            ? 'Pemindaian selesai. Keluar dari gambar sampai area kosong diperiksa dua kali sebelum uji berikutnya.'
            : 'Posisi gambar memenuhi syarat, tetapi identitas belum diterima. Lihat hasil pencocokan.';
        const labels = { candidate: 'Kandidat ditemukan · belum mencatat absensi', ambiguous: 'Meragukan · perlu pemeriksaan', unknown: 'Tidak dikenal · kandidat ditolak' };
        element('result').textContent = labels[result.status];
        element('candidate').textContent = result.candidates.map((item) => `${item.alias} · jarak ${item.distance.toFixed(3)}`).join(' | ');
        element('received').textContent = `Waktu server: ${result.server_time}`;
        const roundtrip = received - sent;
        timing('roundtrip', roundtrip);
        timing('server', result.timings.server_ms);
        timing('bootstrap', result.timings.bootstrap_ms);
        timing('guard', result.timings.guard_ms);
        timing('validation', result.timings.dispatch_validation_ms);
        timing('match', result.timings.matching_ms);
        timing('network', Math.max(0, roundtrip - result.timings.server_ms));
        const payloadBytes = new TextEncoder().encode(body).length;
        element('payload').textContent = `${result.reference_count} referensi · payload ${(payloadBytes / 1024).toFixed(1)} KiB · backend ${faceapi.tf.getBackend()}`;
        await nextPaint();
        if (run !== generation) return;
        const painted = performance.now();
        timing('render', painted - received);
        timing('total', painted - captured.started);
        recordTrial({ scenario, reason_code: '', attempt_ms: painted - attempted, status: result.status, references: result.reference_count,
            detector_input: captured.inputSize, backend: faceapi.tf.getBackend(), graphics_hint: graphics.category,
            frame_width: captured.frameWidth, frame_height: captured.frameHeight,
            payload_bytes: payloadBytes, detect_ms: captured.detect_ms, quality_ms: captured.quality_ms, extract_ms: captured.extract_ms,
            roundtrip_ms: roundtrip, server_ms: result.timings.server_ms, match_ms: result.timings.matching_ms,
            bootstrap_ms: result.timings.bootstrap_ms, guard_ms: result.timings.guard_ms,
            dispatch_validation_ms: result.timings.dispatch_validation_ms, opcode_cache: result.opcode_cache_enabled ? 1 : 0,
            render_ms: painted - received, total_ms: painted - captured.started });
        status(result.status === 'candidate'
            ? 'Kandidat ditemukan. Keluar dari area kamera sebelum uji berikutnya. Tidak ada absensi disimpan.'
            : 'Hasil belum diterima sebagai kandidat. Perbaiki posisi atau minta pemeriksaan operator.');
    } catch (error) {
        if (run === generation) {
            element('position-help').textContent = scanFailureMessage(error);
            const outcome = failureOutcome(error, stage);
            if (outcome) recordTrial({ ...outcome, scenario, attempt_ms: performance.now() - attempted,
                detector_input: inputSize, backend: faceapi.tf.getBackend(), graphics_hint: graphics.category,
                references: references.length, total_ms: null });
            if (error.code === 'PASSAGE_WAIT' || error.code === 'PASSAGE_READY') {
                element('result').textContent = error.code === 'PASSAGE_WAIT'
                    ? 'Sudah diproses — menunggu area kosong' : 'Siap untuk peserta berikutnya';
            } else {
                element('result').textContent = 'Pemindaian belum menghasilkan kandidat';
                element('candidate').textContent = scanFailureMessage(error);
            }
            status(scanFailureMessage(error));
            // Stop on transport failures; no hidden retries or offline attendance queue.
            if (error.name === 'AbortError' || error instanceof TypeError || error.message.startsWith('Kode operator')) repeating = false;
        }
    } finally {
        busy = false;
        controls();
        if (repeating && stream && run === generation) timer = setTimeout(scan, 1200);
    }
}

element('start').addEventListener('click', start);
element('show-position-guide').addEventListener('change', () => {
    element('position-guide').hidden = !stream || !element('show-position-guide').checked;
});
element('stop').addEventListener('click', () => { stop(); status('Kamera dihentikan.'); });
element('enroll').addEventListener('click', enroll);
element('scan').addEventListener('click', scan);
element('reset').addEventListener('click', clearResults);
element('continuous').addEventListener('click', () => {
    repeating = !repeating;
    clearTimeout(timer);
    controls();
    if (repeating) scan();
});
element('consent').addEventListener('change', () => { if (!element('consent').checked) clearResults(); controls(); });
element('performance-profile').addEventListener('change', () => {
    status('Mode deteksi berubah. Referensi tetap sama. Keluar dari gambar jika kandidat sebelumnya masih dikunci, lalu lanjutkan uji.');
    element('statistics').textContent = 'Ringkasan mode yang dipilih akan diperbarui setelah pemindaian berikutnya. Sampel sebelumnya tetap tersedia di CSV.';
});
element('trial-scenario').addEventListener('change', () => {
    element('trial-warning').textContent = '';
    element('statistics').textContent = 'Skenario berubah. Hasil sebelumnya tetap ada di CSV; ringkasan diperbarui setelah percobaan berikutnya.';
    status('Gunakan Pindai sekali untuk percobaan terkontrol. Ganti skenario hanya sesuai benda/orang yang benar-benar dihadapkan ke kamera.');
    controls();
});
element('token').addEventListener('input', () => {
    element('operator-status').textContent = 'Kode berubah; akan diperiksa saat kamera diaktifkan atau saat pemindaian.';
    controls();
});
element('token').addEventListener('change', controls);
document.addEventListener('visibilitychange', () => {
    pauseHiddenCamera({ hidden: document.hidden, active: !!stream || busy, stop, notify: status });
});
window.addEventListener('pagehide', clearResults);
element('download').addEventListener('click', () => {
    try {
        const csv = metricsCsv(samples);
        element('csv-preview').value = csv;
        element('csv-fallback').hidden = false;
        const url = URL.createObjectURL(new Blob(['\uFEFF', csv], { type: 'text/csv;charset=utf-8' }));
        const link = document.createElement('a');
        link.href = url;
        link.download = 'metrik-uji-kamera.csv';
        link.hidden = true;
        document.body.appendChild(link);
        try { link.click(); }
        finally {
            link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 60000);
        }
        element('download-status').textContent = `Permintaan unduh ${samples.length} sampel dikirim ke Chrome. Tekan Ctrl+J untuk memeriksa unduhan. Jika file tidak muncul, salin isi CSV di bawah.`;
    } catch (error) {
        element('download-status').textContent = `Unduhan belum dapat dimulai: ${error.message}. Jika isi CSV tersedia di bawah, salin secara manual.`;
    }
});
element('select-csv').addEventListener('click', () => {
    element('csv-preview').focus();
    element('csv-preview').select();
});
element('today').textContent = new Intl.DateTimeFormat('id-ID', { dateStyle: 'full', timeZone: 'Asia/Jakarta' }).format(new Date());
const linkedCode = operatorCodeFromLink(window.location.hash);
if (linkedCode) {
    element('token').value = linkedCode;
    element('operator-status').textContent = 'Kode terisi dari tautan terminal. Centang persetujuan lalu aktifkan kamera.';
    history.replaceState(null, '', window.location.pathname + window.location.search);
}
controls();
