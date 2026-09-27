// Initial experiment thresholds, not calibrated biometric or liveness guarantees.
export function facePositionIssue(box, width, height) {
    return facePositionAssessment(box, width, height)?.message ?? '';
}

export function facePositionAssessment(box, width, height) {
    if (![box.x, box.y, box.width, box.height, width, height].every(Number.isFinite)
        || width <= 0 || height <= 0 || box.width <= 0 || box.height <= 0) {
        return { code: 'POSITION_INVALID', message: 'Posisi wajah tidak valid. Coba pindai kembali.' };
    }
    if (box.width < 100 || box.height < 100) return { code: 'FACE_TOO_SMALL', message: 'Wajah terlalu kecil. Mendekatlah sedikit ke kamera, lalu pindai kembali.' };
    if (box.x < 8 || box.y < 8 || box.x + box.width > width - 8 || box.y + box.height > height - 8) {
        return { code: 'FACE_NEAR_EDGE', message: 'Wajah terlalu dekat tepi gambar. Posisikan seluruh wajah di tengah kamera, lalu pindai kembali.' };
    }
    return null;
}

export function faceImageQuality({ data, width, height }) {
    if (width < 3 || height < 3 || data.length !== width * height * 4) throw new Error('Gambar pemeriksaan kualitas tidak valid.');
    const gray = new Float64Array(width * height);
    let brightness = 0;
    for (let i = 0; i < gray.length; i++) {
        gray[i] = 0.299 * data[i * 4] + 0.587 * data[i * 4 + 1] + 0.114 * data[i * 4 + 2];
        brightness += gray[i];
    }
    brightness /= gray.length;
    let sum = 0;
    let squares = 0;
    let count = 0;
    for (let y = 1; y < height - 1; y++) {
        for (let x = 1; x < width - 1; x++) {
            const i = y * width + x;
            const edge = gray[i - 1] + gray[i + 1] + gray[i - width] + gray[i + width] - 4 * gray[i];
            sum += edge;
            squares += edge * edge;
            count++;
        }
    }
    const sharpness = Math.max(0, squares / count - (sum / count) ** 2);
    const issue = brightness < 45 ? 'Wajah terlalu gelap. Tambahkan cahaya dari depan.'
        : brightness > 220 ? 'Wajah terlalu terang. Hindari cahaya langsung yang berlebihan.'
            : sharpness < 35 ? 'Gambar wajah kurang tajam. Diam sebentar dan periksa fokus kamera.' : '';
    return { brightness, sharpness, issue };
}

export function createPassageGate() {
    let locked = false;
    let emptyFrames = 0;
    return {
        get locked() { return locked; },
        observe(faceCount) {
            emptyFrames = faceCount === 0 ? emptyFrames + 1 : 0;
            if (emptyFrames >= 2) locked = false;
            return locked;
        },
        accept() { locked = true; emptyFrames = 0; },
        reset() { locked = false; emptyFrames = 0; },
    };
}
