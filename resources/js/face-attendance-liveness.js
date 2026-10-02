import { waitForFaceStep } from './siswa-face-weights.js';

// Local motion experiment only; browser observations are not trusted server evidence.
export async function runHeadTurnChallenge({ capture, reference, active, notify, mirrored = false,
    random = globalThis.crypto, now = () => performance.now(),
    pause = () => new Promise(resolve => setTimeout(resolve, 80)) }) {
    const direction = random.getRandomValues(new Uint8Array(1))[0] % 2 ? 1 : -1;
    const screenDirection = direction * (mirrored ? -1 : 1);
    const started = now();
    let lastAt = started;
    let stage = 0;
    let heldSince;
    let observations = 0;
    let baseline;
    let previousYaw;
    const fail = (message, code = 'liveness_failed') => Object.assign(new Error(message), { code });
    const prompts = [
        '1/3 · Hadapkan wajah lurus ke kamera dan tahan sebentar.',
        `2/3 · Perlahan arahkan hidung ke sisi ${screenDirection === 1 ? 'kanan →' : '← kiri'} layar, lalu tahan sebentar.`,
        '3/3 · Kembali menghadap lurus dan tahan sebentar.',
    ];
    while (active()) {
        if (now() - started >= 20000) throw fail('Waktu uji gerakan habis. Hadapkan wajah dengan cahaya merata lalu coba lagi.');
        notify(prompts[stage]);
        let frame;
        try {
            frame = await waitForFaceStep(capture(), 4000, 'Gambar terlalu lambat untuk uji gerakan. Coba lagi.');
        } catch (error) {
            throw fail(error.code === 'no_face' || error.code === 'multiple_faces'
                ? 'Wajah hilang atau lebih dari satu wajah selama uji gerakan. Ulangi dengan satu orang.' : error.message);
        }
        if (!active()) throw fail('Uji gerakan dibatalkan.', 'liveness_cancelled');
        const at = now();
        if (at - started >= 20000 || at - lastAt > 4000 || at < lastAt) {
            throw fail('Rangkaian gambar terputus atau waktu habis. Ulangi uji gerakan.');
        }
        lastAt = at;
        const { yaw, descriptor } = frame;
        if (!Number.isFinite(yaw) || !Array.isArray(descriptor) || descriptor.length !== 128
            || !descriptor.every(Number.isFinite)) throw fail('Gerakan wajah belum terbaca. Coba lagi.');
        const distance = Math.sqrt(descriptor.reduce((sum, value, index) => sum + (value - reference[index]) ** 2, 0));
        if (!Number.isFinite(distance) || distance > 0.5) throw fail('Wajah berubah selama pemeriksaan. Ulangi dengan orang yang sama.');
        if (previousYaw !== undefined && Math.abs(yaw - previousYaw) > 0.35) {
            throw fail('Gerakan terlalu mendadak. Gerakkan kepala perlahan lalu coba lagi.');
        }
        previousYaw = yaw;
        const accepted = stage === 0 ? Math.abs(yaw) <= 0.12
            : stage === 1 ? (yaw - baseline) * direction >= 0.18 : Math.abs(yaw - baseline) <= 0.08;
        if (!accepted) {
            heldSince = undefined;
            observations = 0;
            await pause();
            continue;
        }
        heldSince ??= at;
        observations++;
        if (observations >= 2 && at - heldSince >= 200) {
            if (stage === 0) baseline = yaw;
            stage++;
            heldSince = undefined;
            observations = 0;
            if (stage === 3) return { descriptor, elapsed: at - started };
        }
        await pause();
    }
    throw fail('Uji gerakan dibatalkan.', 'liveness_cancelled');
}
