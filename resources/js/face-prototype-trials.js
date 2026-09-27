export const trialScenarios = {
    frontal: 'Wajah langsung, menghadap depan',
    turned: 'Wajah langsung, sedikit menoleh',
    lighting: 'Wajah langsung, perubahan cahaya/jarak',
    photo: 'Foto diri pada layar atau cetakan',
    replay: 'Video diri yang diputar ulang',
    empty: 'Area kamera tanpa wajah',
};

export function trialScenario(value) {
    return Object.hasOwn(trialScenarios, value) ? value : 'frontal';
}

export function trialError(code, message) {
    const error = new Error(message);
    error.code = code;
    return error;
}

export function failureOutcome(error, stage) {
    if (['PASSAGE_WAIT', 'PASSAGE_READY'].includes(error.code)) return null;
    const captureCodes = ['NO_FACE', 'MULTIPLE_FACES', 'POSITION', 'POSITION_INVALID', 'FACE_TOO_SMALL', 'FACE_NEAR_EDGE', 'IMAGE_QUALITY', 'VIDEO_NOT_READY'];
    if (captureCodes.includes(error.code)) return { status: 'capture_rejected', reason_code: error.code };
    return { status: 'error', reason_code: stage === 'server' ? 'SERVER_OR_NETWORK' : 'PROCESSING_ERROR' };
}

export function trialWarning(scenario, outcome) {
    if (['photo', 'replay'].includes(scenario)) {
        if (outcome === 'error') return 'Percobaan foto/video gagal diproses. Ketahanan terhadap manipulasi belum dapat dinilai dari percobaan ini.';
        return outcome === 'candidate'
            ? 'Foto/video menghasilkan kandidat. Ini menunjukkan celah pada uji ini; bukan bukti orang hadir. Belum ada pemeriksaan keaslian wajah.'
            : 'Foto/video tidak menghasilkan kandidat pada percobaan ini. Ini belum membuktikan perlindungan antimanipulasi.';
    }
    return '';
}

export function summarizeTrials(samples, inputSize, scenario) {
    const selected = samples.filter((item) => item.detector_input === inputSize && item.scenario === scenario);
    const completed = selected.filter((item) => ['candidate', 'unknown', 'ambiguous'].includes(item.status));
    const times = completed.map((item) => item.total_ms).filter(Number.isFinite).sort((a, b) => a - b);
    const percentile = (p) => times.length ? times[Math.ceil(p * times.length) - 1] : null;
    return { attempts: selected.length, completed: completed.length,
        candidates: selected.filter((item) => item.status === 'candidate').length,
        rejected: selected.filter((item) => item.status === 'capture_rejected').length,
        errors: selected.filter((item) => item.status === 'error').length,
        p50: percentile(0.5), p95: percentile(0.95) };
}
