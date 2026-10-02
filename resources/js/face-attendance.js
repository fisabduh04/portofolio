import { requestFaceCamera } from './siswa-face-enrollment.js';
import { waitForFaceStep } from './siswa-face-weights.js';

export function initializeFaceAttendance(documentRoot = document, environment = window) {
    const root = documentRoot.querySelector('[data-face-attendance]');
    if (!root) return;
    const get = name => root.querySelector(`[data-scan-${name}]`);
    const video = get('video');
    let stream;
    let extract;
    let busy = false;
    let submitting = false;
    let generation = 0;
    let count = 0;
    let automatic = false;
    let automaticGeneration = 0;
    let automaticTimer;
    let waitingForClear = false;
    let emptyFrames = 0;
    const clock = () => (environment.performance || performance).now();
    const duration = value => `${Math.round(value)} ms`;
    const metric = (name, text) => { get(`perf-${name}`).textContent = text; };
    const samples = [];
    metric('host', environment.location?.host || 'Server saat ini');
    metric('environment', /(^localhost$|\.test$|^127\.)/.test(environment.location?.hostname || '')
        ? 'Lingkungan lokal: hasil ini belum mengukur hosting produksi.'
        : 'Pengukuran menuju server halaman ini; bandingkan saat jam sibuk.');
    const message = text => { get('message').textContent = text; };
    const autoMessage = text => { get('auto-status').textContent = text; };
    function feedback(state, label, identity, detail) {
        get('feedback').dataset.state = state;
        get('result-label').textContent = label;
        get('identity').textContent = identity;
        get('result-detail').textContent = detail;
    }
    const cameraReady = () => !busy && !submitting && stream && extract && video.readyState >= 2 && video.videoWidth && video.videoHeight;
    function pauseAutomatic() {
        automatic = false;
        automaticGeneration++;
        environment.clearTimeout(automaticTimer);
        autoMessage('Otomatis dijeda. Klik Mulai pemindaian otomatis untuk melanjutkan.');
        render();
    }
    async function automaticTick() {
        const run = automaticGeneration;
        if (!automatic) return;
        await scan(true);
        if (automatic && run === automaticGeneration) {
            automaticTimer = environment.setTimeout(automaticTick, 600);
        }
    }
    function toggleAutomatic() {
        if (automatic) { pauseAutomatic(); return; }
        if (!cameraReady()) return;
        if (root.dataset.mode === 'mapel' && !get('materi').value.trim()) {
            message('Isi materi pelajaran terlebih dahulu.');
            get('materi').focus();
            return;
        }
        automatic = true;
        automaticGeneration++;
        autoMessage(waitingForClear ? 'Kosongkan bingkai sebelum siswa berikutnya.' : 'Otomatis aktif. Satu siswa maju ke kamera.');
        render();
        return automaticTick();
    }
    function render() {
        get('facing').disabled = busy || submitting || !!stream;
        get('start').disabled = busy || submitting || !!stream;
        get('stop').disabled = submitting || (!busy && !stream);
        get('capture').disabled = automatic || !cameraReady();
        get('auto').disabled = !automatic && !cameraReady();
        get('auto').textContent = automatic ? 'Jeda otomatis' : 'Mulai otomatis';
        get('capture').textContent = submitting ? 'Mencatat…' : busy ? 'Menyiapkan…' : 'Pindai & catat';
        get('placeholder').classList.toggle('hidden', !!stream);
    }
    function stop() {
        pauseAutomatic();
        waitingForClear = false;
        emptyFrames = 0;
        if (busy && !submitting) {
            metric('status', 'Gagal / dibatalkan');
            feedback('ready', 'Pemindaian dihentikan', 'Kamera nonaktif', 'Aktifkan kembali kamera untuk melanjutkan.');
        }
        generation++;
        stream?.getTracks().forEach(track => track.stop());
        stream = null;
        extract = null;
        video.srcObject = null;
        busy = false;
        render();
    }
    async function start() {
        if (busy || submitting || stream) return;
        if (!environment.isSecureContext || !environment.navigator.mediaDevices?.getUserMedia) {
            message('Kamera memerlukan HTTPS atau localhost.');
            feedback('error', 'Kamera belum tersedia', 'Periksa akses kamera', 'Kamera memerlukan HTTPS atau localhost.');
            return;
        }
        const run = ++generation;
        const cameraStarted = clock();
        metric('camera', 'Menunggu izin / gambar…');
        metric('model', 'Belum dimulai');
        metric('backend', 'Belum siap');
        busy = true;
        render();
        message('Menunggu izin kamera. Pilih Izinkan pada browser.');
        feedback('processing', 'Menyiapkan kamera', 'Tunggu sebentar…', 'Izinkan akses kamera jika diminta oleh browser.');
        try {
            const facingMode = get('facing').value || 'user';
            const acquired = await requestFaceCamera(environment.navigator.mediaDevices, 30000, facingMode);
            if (run !== generation) {
                acquired.getTracks().forEach(track => track.stop());
                return;
            }
            stream = acquired;
            video.classList.toggle('-scale-x-100', (stream.getVideoTracks()[0]?.getSettings?.().facingMode || facingMode) === 'user');
            stream.getVideoTracks().forEach(track => track.addEventListener('ended', () => {
                if (run !== generation) return;
                stop();
                message('Kamera terputus. Aktifkan kembali.');
            }, { once: true }));
            video.muted = true;
            video.playsInline = true;
            video.srcObject = stream;
            render();
            message('Menunggu gambar kamera…');
            await waitForFaceStep(video.play(), 15000, 'Gambar kamera belum siap. Coba aktifkan kembali.');
            if (run !== generation) return;
            metric('camera', duration(clock() - cameraStarted));
            const modelStarted = clock();
            metric('model', 'Memuat…');
            message('Memuat model wajah…');
            const progress = text => { if (run === generation) message(text); };
            const model = await waitForFaceStep(environment.loadFaceModels ? environment.loadFaceModels(progress)
                : import('./siswa-face-models').then(module => module.loadFaceModels(progress)), 60000, 'Model terlalu lama dimuat. Muat ulang halaman lalu coba lagi.');
            if (run !== generation) return;
            extract = model;
            metric('model', duration(clock() - modelStarted));
            metric('backend', model.backend === 'webgl' ? 'WebGL (GPU fisik belum diverifikasi)'
                : model.backend === 'cpu' ? 'CPU' : model.backend || 'Tidak tersedia');
            busy = false;
            message('Kamera siap. Hadapkan satu siswa lalu klik Pindai & catat absensi.');
            feedback('ready', 'Kamera siap', 'Silakan maju', 'Satu siswa di depan kamera. Mulai otomatis atau tekan Pindai & catat.');
            render();
        } catch (error) {
            if (run !== generation) return;
            stop();
            metric('backend', 'Persiapan gagal');
            metric('camera', 'Tidak selesai / lihat pesan kamera');
            metric('model', 'Tidak selesai');
            const detail = error.name === 'NotAllowedError' ? 'Izin kamera ditolak. Izinkan kamera melalui pengaturan situs.' : error.message;
            message(detail);
            feedback('error', 'Kamera belum siap', 'Periksa kamera', detail);
        }
    }
    async function scan(isAutomatic = false) {
        if (!cameraReady() || (!isAutomatic && automatic)) return;
        const materi = get('materi')?.value.trim() || '';
        if (root.dataset.mode === 'mapel' && !materi) {
            if (isAutomatic) pauseAutomatic();
            message('Isi materi pelajaran terlebih dahulu.');
            get('materi').focus();
            return;
        }
        const run = generation;
        const autoRun = automaticGeneration;
        const scanStarted = clock();
        let detectionMs;
        let requestStarted;
        let responseMs;
        let success = false;
        let rejectedFace = false;
        let measured = false;
        function beginMeasurement() {
            measured = true;
            for (const name of ['detection', 'response', 'server', 'overhead', 'total']) metric(name, '—');
            metric('status', 'Memindai…');
        }
        if (!isAutomatic) beginMeasurement();
        busy = true;
        render();
        if (!isAutomatic) {
            message('Memeriksa wajah…');
            feedback('processing', 'Sedang memindai', 'Memeriksa wajah…', 'Tetap menghadap kamera sampai hasil muncul.');
        }
        try {
            const canvas = documentRoot.createElement('canvas');
            canvas.width = Math.min(video.videoWidth, 960);
            canvas.height = Math.round(video.videoHeight * canvas.width / video.videoWidth);
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const descriptor = await waitForFaceStep(extract(canvas), 30000, 'Pemindaian terlalu lama. Perbaiki pencahayaan lalu coba lagi.');
            if (run !== generation) return;
            if (isAutomatic && (!automatic || autoRun !== automaticGeneration)) return;
            if (isAutomatic) {
                emptyFrames = 0;
                if (waitingForClear) {
                    autoMessage('Siswa sudah diproses. Keluar dari bingkai sebelum siswa berikutnya maju.');
                    return;
                }
                beginMeasurement();
                autoMessage('Sedang mencatat. Tunggu hasil sebelum berganti siswa.');
            }
            detectionMs = clock() - scanStarted;
            metric('detection', duration(detectionMs));
            submitting = true;
            render();
            message('Mencocokkan dan mencatat presensi…');
            feedback('processing', 'Wajah terdeteksi', 'Mencocokkan siswa…', 'Tunggu konfirmasi pencatatan sebelum meninggalkan kamera.');
            requestStarted = clock();
            const response = await environment.fetch(root.dataset.endpoint, {
                method: 'POST', credentials: 'same-origin', signal: AbortSignal.timeout(30000),
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': documentRoot.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ mode: root.dataset.mode, jadwal_id: root.dataset.jadwal || null, type: root.dataset.type, materi, descriptor }),
            });
            const result = await response.json().catch(() => ({}));
            responseMs = clock() - requestStarted;
            metric('response', duration(responseMs));
            if (Number.isFinite(result.server_ms) && result.server_ms >= 0) {
                metric('server', duration(result.server_ms));
                metric('overhead', duration(Math.max(0, responseMs - result.server_ms)));
            }
            if (!response.ok) {
                if (isAutomatic) {
                    if (response.status === 422 && result.errors?.descriptor) {
                        rejectedFace = true;
                        waitingForClear = true;
                        autoMessage('Wajah belum cocok. Siswa keluar dari bingkai lalu coba lagi atau gunakan presensi manual.');
                    } else {
                        pauseAutomatic();
                    }
                }
                if (response.status === 429) throw new Error('Batas permintaan tercapai. Tunggu satu menit lalu lanjutkan otomatis.');
                throw new Error(response.status === 419 || response.status === 401 ? 'Sesi berakhir. Muat ulang halaman dan login kembali.'
                    : Object.values(result.errors || {}).flat()[0] || result.message || 'Presensi belum dapat dicatat. Coba lagi.');
            }
            if (!result.already_recorded) count++;
            success = true;
            waitingForClear = true;
            emptyFrames = 0;
            feedback('success', result.already_recorded ? 'Sudah tercatat' : 'Berhasil dicatat', result.student,
                `${result.kelas} · ${result.status} · ${result.time}. ${result.already_recorded ? 'Status sebelumnya tetap dipertahankan.' : 'Silakan keluar dari bingkai kamera.'}`);
            if (isAutomatic && automatic) autoMessage('Berhasil. Siswa keluar dari bingkai; berikutnya maju setelah tanda siap.');
            get('count').textContent = `${count} presensi baru`;
            const item = documentRoot.createElement('li');
            item.className = 'whitespace-pre-line break-words py-3 text-sm leading-6 text-gray-700 dark:text-gray-200';
            item.textContent = `${result.student}\n${result.kelas} · ${result.status} · ${result.time}\n${result.already_recorded ? 'Sudah tercatat' : 'Berhasil dicatat'}`;
            get('results').prepend(item);
            while (get('results').children.length > 20) get('results').lastElementChild.remove();
            get('empty').classList.add('hidden');
            message(`${result.student}: ${result.message} Siap untuk siswa berikutnya.`);
        } catch (error) {
            if (isAutomatic && run === generation && automatic && autoRun === automaticGeneration
                && ['no_face', 'multiple_faces'].includes(error.code)) {
                if (error.code === 'no_face') {
                    emptyFrames++;
                    if (emptyFrames >= 2) waitingForClear = false;
                    autoMessage(waitingForClear ? 'Tunggu sebentar, memastikan bingkai kosong…' : 'Siap. Siswa berikutnya silakan maju.');
                    if (!waitingForClear) feedback('ready', 'Siap memindai', 'Silakan maju', 'Belum ada wajah terdeteksi. Satu siswa masuk ke bingkai kamera.');
                } else {
                    emptyFrames = 0;
                    autoMessage('Lebih dari satu wajah. Siswa lain mundur dari bingkai kamera.');
                    feedback('error', 'Pemindaian tertunda', 'Lebih dari satu wajah', 'Pastikan hanya satu siswa berada di depan kamera.');
                }
                return;
            }
            if (isAutomatic && run === generation && autoRun === automaticGeneration && !rejectedFace) pauseAutomatic();
            if (run === generation || submitting) {
                const uncertain = error.name === 'TimeoutError' || error instanceof TypeError;
                const detail = uncertain ? 'Koneksi terputus atau waktu habis. Periksa logbook atau pindai ulang; presensi ganda dicegah.' : error.message;
                message(detail);
                feedback('error', uncertain ? 'Periksa hasil di logbook' : 'Pemindaian belum berhasil',
                    error.code === 'no_face' ? 'Wajah belum terdeteksi' : error.code === 'multiple_faces' ? 'Lebih dari satu wajah' : 'Perlu diperiksa', detail);
            }
        } finally {
            if (measured && (run === generation || submitting)) {
                const totalMs = clock() - scanStarted;
                metric('total', duration(totalMs));
                metric('status', success ? 'Selesai' : 'Gagal / dibatalkan');
                if (detectionMs === undefined) metric('detection', `${duration(totalMs)} (tidak selesai)`);
                if (requestStarted !== undefined && responseMs === undefined) {
                    metric('response', `${duration(clock() - requestStarted)} (tidak selesai)`);
                }
                if (success) {
                    samples.push(totalMs);
                    if (samples.length > 20) samples.shift();
                    metric('average', `${duration(samples.reduce((sum, value) => sum + value, 0) / samples.length)} · ${samples.length} pindai berhasil terakhir`);
                }
            }
            submitting = false;
            if (run === generation) busy = false;
            render();
        }
    }
    get('start').addEventListener('click', start);
    get('capture').addEventListener('click', () => scan());
    get('auto').addEventListener('click', toggleAutomatic);
    get('stop').addEventListener('click', () => { stop(); message('Kamera dimatikan. Presensi yang tersimpan tetap tersedia di logbook.'); });
    for (const eventName of ['loadeddata', 'canplay', 'playing', 'resize']) video.addEventListener(eventName, render);
    documentRoot.addEventListener('visibilitychange', () => { if (documentRoot.hidden) stop(); });
    environment.addEventListener('pagehide', stop);
    environment.addEventListener('beforeunload', event => { if (submitting) { event.preventDefault(); event.returnValue = ''; } });
    render();
}
