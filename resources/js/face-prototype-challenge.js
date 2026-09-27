// Experimental motion checks, not a presentation-attack detection model.
export function motionSignals(landmarks) {
    const distance = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);
    const eyeRatio = (eye) => (distance(eye[1], eye[5]) + distance(eye[2], eye[4])) / (2 * distance(eye[0], eye[3]));
    const left = landmarks.getLeftEye();
    const right = landmarks.getRightEye();
    const center = (points) => points.reduce((sum, p) => ({ x: sum.x + p.x / points.length, y: sum.y + p.y / points.length }), { x: 0, y: 0 });
    const a = center(left);
    const b = center(right);
    const nose = landmarks.getNose()[3];
    const dx = b.x - a.x;
    const dy = b.y - a.y;
    return { leftEar: eyeRatio(left), rightEar: eyeRatio(right),
        yaw: ((nose.x - (a.x + b.x) / 2) * dx + (nose.y - (a.y + b.y) / 2) * dy) / (dx * dx + dy * dy) };
}

export function randomMotionPlan(random = globalThis.crypto) {
    const bytes = random.getRandomValues(new Uint8Array(2));
    return { actions: bytes[0] % 2 ? ['turn', 'blink'] : ['blink', 'turn'], direction: bytes[1] % 2 ? 1 : -1 };
}

export function createMotionChallenge(plan, started, duration = 25000) {
    if (plan.actions.length !== 2 || new Set(plan.actions).size !== 2
        || !plan.actions.includes('blink') || !plan.actions.includes('turn') || ![-1, 1].includes(plan.direction)) {
        throw new Error('Invalid motion plan');
    }
    let baseline;
    let previous;
    let stage = 'prepare';
    let actionIndex = 0;
    let consecutive = 0;
    let lastAt = started;
    let done = false;
    let failed = '';
    const fail = (code) => { failed = code; return { failed: code, done: false }; };
    const consistent = (descriptor, reference) => Math.sqrt(descriptor.reduce((sum, value, i) => sum + (value - reference[i]) ** 2, 0)) <= 0.5;
    const next = () => {
        actionIndex++;
        consecutive = 0;
        if (actionIndex === plan.actions.length) done = true;
        else stage = plan.actions[actionIndex];
    };
    return {
        get prompt() {
            if (failed) return 'Tantangan dihentikan. Mulai ulang jika ingin mencoba lagi.';
            if (done) return 'Gerakan selesai; belum membuktikan keaslian wajah.';
            if (stage === 'prepare') return 'Hadapkan wajah lurus, buka kedua mata, dan diam sebentar.';
            if (stage === 'blink') return 'Tutup kedua mata sebentar, lalu buka kembali. Jangan sekadar menggerakkan foto.';
            if (stage === 'reopen') return 'Buka kembali kedua mata dan hadapkan wajah lurus.';
            if (stage === 'return') return 'Kembali menghadap lurus ke kamera.';
            return `Putar kepala sedikit agar hidung mengarah ke sisi ${plan.direction === 1 ? 'kanan' : 'kiri'} gambar kamera, lalu kembali lurus.`;
        },
        observe(sample, now) {
            if (failed) return { failed, done: false };
            if (done) return { done: true };
            if (!Number.isFinite(now) || now < lastAt || now - started > duration) return fail('MOTION_TIMEOUT');
            if (now - lastAt > 2500) return fail('MOTION_INTERRUPTED');
            lastAt = now;
            const { leftEar, rightEar, yaw, descriptor } = sample;
            if (![leftEar, rightEar, yaw].every(Number.isFinite) || !Array.isArray(descriptor)
                || descriptor.length !== 128 || !descriptor.every(Number.isFinite)) return fail('MOTION_INVALID');
            if (previous && !consistent(descriptor, previous)) return fail('MOTION_FACE_CHANGED');
            if (baseline && !consistent(descriptor, baseline.descriptor)) return fail('MOTION_FACE_CHANGED');
            previous = [...descriptor];
            if (stage === 'prepare') {
                consecutive = leftEar >= 0.2 && rightEar >= 0.2 && Math.abs(yaw) <= 0.2 ? consecutive + 1 : 0;
                if (consecutive >= 2) {
                    baseline = { leftEar, rightEar, yaw, descriptor: [...descriptor] };
                    stage = plan.actions[0];
                    consecutive = 0;
                }
                return { done: false };
            }
            const open = leftEar >= baseline.leftEar * 0.85 && rightEar >= baseline.rightEar * 0.85;
            const closed = leftEar < baseline.leftEar * 0.65 && rightEar < baseline.rightEar * 0.65;
            const neutral = Math.abs(yaw - baseline.yaw) <= 0.10;
            if (stage === 'blink') {
                if (closed && neutral) { stage = 'reopen'; consecutive = 0; }
            } else if (stage === 'reopen' || stage === 'return') {
                consecutive = open && neutral ? consecutive + 1 : 0;
                if (consecutive >= 2) next();
            } else if (stage === 'turn') {
                consecutive = open && (yaw - baseline.yaw) * plan.direction >= 0.18 ? consecutive + 1 : 0;
                if (consecutive >= 2) { stage = 'return'; consecutive = 0; }
            }
            return { done };
        },
    };
}

export async function runMotionChallenge({ capture, active, notify, now = () => performance.now(),
    pause = () => new Promise((resolve) => setTimeout(resolve, 60)), plan = randomMotionPlan() }) {
    const started = now();
    const challenge = createMotionChallenge(plan, started);
    let firstStarted;
    let frames = 0;
    const error = (code, message) => Object.assign(new Error(message), { code });
    const messages = {
        MOTION_TIMEOUT: 'Waktu tantangan habis. Coba lagi dengan cahaya merata dan wajah terlihat jelas.',
        MOTION_INTERRUPTED: 'Rangkaian gambar terputus atau pemrosesan terlalu lambat. Ulangi tantangan.',
        MOTION_INVALID: 'Gerakan mata atau kepala belum dapat diukur. Ulangi tantangan.',
        MOTION_FACE_CHANGED: 'Konsistensi wajah berubah selama tantangan. Ulangi dengan satu orang yang sama.',
    };
    try {
        while (active()) {
            notify(challenge.prompt);
            const frame = await capture();
            if (!active()) throw error('MOTION_CANCELLED', 'Tantangan dibatalkan.');
            firstStarted ??= frame.started;
            frames++;
            const result = challenge.observe({ ...frame.motion, descriptor: frame.descriptor }, now());
            if (result.failed) throw error(result.failed, messages[result.failed]);
            if (result.done) return { ...frame, started: firstStarted, motion_ms: now() - started, motion_frames: frames,
                motion_plan: `${plan.actions.join('_')}_${plan.direction === 1 ? 'right' : 'left'}` };
            await pause();
        }
        throw error('MOTION_CANCELLED', 'Tantangan dibatalkan.');
    } catch (failure) {
        failure.motion_ms = now() - started;
        failure.motion_frames = frames;
        failure.motion_plan = `${plan.actions.join('_')}_${plan.direction === 1 ? 'right' : 'left'}`;
        throw failure;
    }
}
