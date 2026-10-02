import { waitForFaceStep } from './siswa-face-weights.js';

const poses = [
    ['Menghadap depan', 'Tatap kamera, posisikan seluruh wajah di dalam panduan.'],
    ['Sedikit ke kiri', 'Putar wajah sedikit ke kiri. Kedua mata tetap terlihat.'],
    ['Sedikit ke kanan', 'Putar wajah sedikit ke kanan. Kedua mata tetap terlihat.'],
];

export async function requestFaceCamera(mediaDevices, timeoutMs = 30000, facingMode = 'user') {
    let expired = false;
    const pending = (async () => {
        try {
            return await mediaDevices.getUserMedia({ video: { facingMode: facingMode === 'environment' ? { exact: 'environment' } : 'user', width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false });
        } catch (error) {
            if (expired || facingMode !== 'environment' || error.name !== 'OverconstrainedError') throw error;
            const stream = await mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            if (stream.getVideoTracks()[0]?.getSettings?.().facingMode === 'user') {
                stream.getTracks().forEach(track => track.stop());
                throw error;
            }
            return stream;
        }
    })();
    try {
        return await waitForFaceStep(pending, timeoutMs, 'Kamera belum memberikan respons. Periksa izin kamera di browser, lalu klik Aktifkan kamera kembali.');
    } catch (error) {
        if (facingMode === 'environment' && ['OverconstrainedError', 'NotFoundError'].includes(error.name)) {
            throw new Error('Kamera belakang tidak tersedia. Pilih kamera depan / webcam lalu aktifkan kembali.');
        }
        if (error.name === 'FaceStartupTimeout') {
            expired = true;
            pending.then(stream => stream.getTracks().forEach(track => track.stop()), () => {});
        }
        throw error;
    }
}

export function initializeFaceEnrollment(documentRoot = document, environment = window) {
    const root = documentRoot.querySelector('[data-face-enrollment]');
    if (!root) return;
    const get = name => root.querySelector(`[data-${name}]`);
    const video = get('face-video');
    const previews = [...root.querySelectorAll('[data-face-sample]')];
    const placeholders = [...root.querySelectorAll('[data-sample-placeholder]')];
    const retakes = [...root.querySelectorAll('[data-retake]')];
    let samples = [null, null, null];
    let selected = 0;
    let stream = null;
    let generation = 0;
    let busy = false;
    let saving = false;
    let dirty = false;
    let extract;

    const message = text => { get('face-message').textContent = text; };
    function render() {
        get('camera-facing').disabled = busy || saving || !!stream;
        const count = samples.filter(Boolean).length;
        get('sample-count').textContent = `${count} dari 3 sampel`;
        get('camera-start').disabled = busy || saving || !!stream;
        get('camera-stop').disabled = saving || (!stream && !busy);
        get('face-capture').disabled = busy || saving || !stream || !extract || selected < 0 || video.readyState < 2 || !video.videoWidth || !video.videoHeight;
        get('face-capture').textContent = busy ? (extract ? 'Memeriksa wajah…' : 'Menyiapkan kamera…') : 'Ambil sampel';
        get('face-save').disabled = busy || saving || count !== 3 || !dirty || selected >= 0;
        get('face-reset').disabled = busy || saving || count === 0;
        get('camera-status').textContent = stream ? 'Kamera aktif' : 'Kamera nonaktif';
        get('camera-placeholder').classList.toggle('hidden', !!stream);
        get('face-guide').classList.toggle('hidden', !stream);
        get('face-guide').classList.toggle('flex', !!stream);
        retakes.forEach((button, index) => { button.disabled = busy || saving || !samples[index]; });
        get('step-number').textContent = selected < 0 ? '✓' : String(selected + 1);
        get('pose-title').textContent = selected < 0 ? 'Ketiga sampel siap diperiksa' : `Posisi ${selected + 1}: ${poses[selected][0]}`;
        get('pose-help').textContent = selected < 0 ? 'Pastikan semua sampel milik siswa yang sama, lalu simpan.' : poses[selected][1];
    }

    function stopCamera() {
        generation++;
        stream?.getTracks().forEach(track => track.stop());
        stream = null;
        extract = null;
        video.srcObject = null;
        busy = false;
        render();
    }

    async function startCamera() {
        if (busy || saving || stream) return;
        if (!environment.isSecureContext || !environment.navigator.mediaDevices?.getUserMedia) {
            message('Kamera memerlukan HTTPS atau localhost. Buka aplikasi melalui koneksi aman.');
            return;
        }
        const run = ++generation;
        busy = true;
        render();
        message('Menunggu izin kamera dari browser. Pilih Izinkan jika muncul permintaan akses.');
        try {
            const facingMode = get('camera-facing').value || 'user';
            const acquired = await requestFaceCamera(environment.navigator.mediaDevices, 30000, facingMode);
            if (run !== generation) {
                acquired.getTracks().forEach(track => track.stop());
                return;
            }
            stream = acquired;
            video.classList.toggle('-scale-x-100', (stream.getVideoTracks()[0]?.getSettings?.().facingMode || facingMode) === 'user');
            stream.getVideoTracks().forEach(track => track.addEventListener('ended', () => {
                if (run !== generation) return;
                stopCamera();
                message('Kamera terputus. Aktifkan kembali untuk melanjutkan.');
            }, { once: true }));
            video.muted = true;
            video.playsInline = true;
            video.srcObject = stream;
            render();
            message('Menunggu gambar kamera…');
            await waitForFaceStep(video.play(), 15000, 'Gambar kamera belum siap. Matikan aplikasi lain yang menggunakan kamera lalu coba lagi.');
            if (run !== generation) return;
            message('Memuat modul pengenalan wajah…');
            const progress = text => { if (run === generation) message(text); };
            const models = await waitForFaceStep(
                environment.loadFaceModels ? environment.loadFaceModels(progress) : import('./siswa-face-models').then(module => module.loadFaceModels(progress)),
                60000,
                'Pemuatan model belum selesai setelah satu menit. Muat ulang halaman dengan Ctrl + Shift + R lalu coba lagi.',
            );
            if (run !== generation) return;
            extract = models;
            message('Kamera siap. Ikuti panduan posisi, lalu klik Ambil sampel.');
        } catch (error) {
            if (run !== generation) return;
            stopCamera();
            const messages = {
                NotAllowedError: 'Akses kamera ditolak. Izinkan kamera melalui pengaturan situs, lalu coba lagi.',
                NotFoundError: 'Kamera tidak ditemukan. Hubungkan kamera lalu coba lagi.',
                NotReadableError: 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi tersebut lalu coba lagi.',
            };
            message(messages[error.name] || error.message || 'Persiapan wajah gagal. Muat ulang halaman lalu coba lagi.');
            if (!messages[error.name]) console.error('Persiapan kamera/model wajah gagal:', error);
        } finally {
            if (run === generation) {
                busy = false;
                render();
            }
        }
    }

    async function capture() {
        if (busy || saving || !stream || !extract || selected < 0 || video.readyState < 2) return;
        if (!video.videoWidth || !video.videoHeight) {
            message('Tunggu gambar kamera muncul sebelum mengambil sampel.');
            return;
        }
        const run = generation;
        const target = selected;
        busy = true;
        render();
        message('Memeriksa wajah…');
        try {
            const canvas = documentRoot.createElement('canvas');
            canvas.width = Math.min(video.videoWidth, 960);
            canvas.height = Math.round(video.videoHeight * canvas.width / video.videoWidth);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const descriptor = await extract(canvas);
            if (run !== generation) return;
            const reference = samples.find((sample, index) => sample && index !== target);
            if (reference && Math.hypot(...descriptor.map((value, index) => value - reference[index])) > 0.6) {
                throw new Error('Sampel berbeda dari wajah sebelumnya. Pastikan siswa yang sama dan coba lagi.');
            }
            previews[target].src = canvas.toDataURL('image/jpeg', 0.85);
            previews[target].classList.remove('hidden');
            placeholders[target].classList.add('hidden');
            samples[target] = descriptor;
            selected = samples.findIndex(sample => !sample);
            dirty = true;
            get('save-message').textContent = 'Ada sampel baru yang belum disimpan.';
            message(selected < 0 ? 'Tiga sampel lengkap. Periksa pratinjau, kemudian klik Simpan wajah.' : 'Sampel berhasil diambil. Lanjutkan ke posisi berikutnya.');
        } catch (error) {
            if (run === generation) message(error.message);
        } finally {
            if (run === generation) {
                busy = false;
                render();
            }
        }
    }

    async function save() {
        if (get('face-save').disabled) return;
        saving = true;
        render();
        get('face-save').textContent = 'Menyimpan…';
        get('save-message').textContent = 'Sedang menyimpan sampel wajah…';
        try {
            const response = await environment.fetch(root.dataset.saveUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': documentRoot.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ samples }),
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(response.status === 419 ? 'Sesi berakhir. Muat ulang halaman dan rekam kembali.' : Object.values(result.errors || {}).flat()[0] || result.message || 'Penyimpanan gagal. Coba simpan kembali.');
            }
            dirty = false;
            get('enrollment-status').textContent = '3 sampel terdaftar';
            get('save-message').textContent = 'Wajah berhasil disimpan. Anda dapat meninggalkan halaman ini.';
            stopCamera();
            message('Perekaman selesai. Kamera telah dimatikan.');
        } catch (error) {
            get('save-message').textContent = error.message || 'Koneksi terputus. Sampel masih tersedia; coba simpan kembali.';
        } finally {
            saving = false;
            get('face-save').textContent = 'Simpan wajah';
            render();
        }
    }

    get('camera-start').addEventListener('click', startCamera);
    get('camera-stop').addEventListener('click', () => { stopCamera(); message('Kamera dimatikan. Sampel yang sudah diambil tetap tersedia.'); });
    get('face-capture').addEventListener('click', capture);
    get('face-save').addEventListener('click', save);
    for (const eventName of ['loadeddata', 'canplay', 'playing', 'resize']) {
        video.addEventListener(eventName, render);
    }
    retakes.forEach((button, index) => button.addEventListener('click', () => {
        selected = index;
        render();
        message('Ambil ulang posisi yang dipilih. Aktifkan kamera jika belum aktif.');
        get(stream ? 'face-capture' : 'camera-start').focus();
    }));
    get('face-reset').addEventListener('click', () => {
        samples = [null, null, null];
        selected = 0;
        dirty = false;
        previews.forEach((preview, index) => {
            preview.removeAttribute('src');
            preview.classList.add('hidden');
            placeholders[index].classList.remove('hidden');
        });
        get('save-message').textContent = 'Pratinjau dibersihkan. Data wajah yang sudah tersimpan tidak berubah.';
        render();
    });
    documentRoot.addEventListener('visibilitychange', () => { if (documentRoot.hidden) stopCamera(); });
    documentRoot.querySelectorAll('[data-tabs-target]').forEach(button => button.addEventListener('click', () => {
        if (button.dataset.tabsTarget !== '#face-enrollment') stopCamera();
    }));
    environment.addEventListener('pagehide', stopCamera);
    environment.addEventListener('beforeunload', event => {
        if (dirty || saving) { event.preventDefault(); event.returnValue = ''; }
    });
    render();
}
