import { facePositionAssessment, faceImageQuality } from './face-prototype-quality.js';

export function createFaceExtractor(faceapi) {
    return async (canvas, { checkQuality = false } = {}) => {
        const faces = await faceapi.detectAllFaces(canvas, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.65 }))
            .withFaceLandmarks(true).withFaceDescriptors();
        if (faces.length !== 1) {
            const error = new Error(faces.length ? 'Ada lebih dari satu wajah. Pastikan hanya siswa ini di depan kamera.' : 'Wajah belum terdeteksi. Perbaiki posisi dan pencahayaan, lalu coba lagi.');
            error.code = faces.length ? 'multiple_faces' : 'no_face';
            throw error;
        }
        if (checkQuality) {
            const box = faces[0].detection.box;
            const position = facePositionAssessment(box, canvas.width, canvas.height);
            if (position) throw new Error(position.message);
            const crop = canvas.ownerDocument.createElement('canvas');
            crop.width = 64;
            crop.height = 64;
            const context = crop.getContext('2d', { willReadFrequently: true });
            if (!context) throw new Error('Kualitas gambar belum dapat diperiksa. Muat ulang halaman lalu ambil ulang.');
            context.drawImage(canvas, box.x, box.y, box.width, box.height, 0, 0, 64, 64);
            const quality = faceImageQuality(context.getImageData(0, 0, 64, 64));
            if (quality.issue) throw new Error(quality.issue);
        }
        const descriptor = Array.from(faces[0].descriptor);
        if (descriptor.length !== 128 || !descriptor.every(Number.isFinite)) {
            throw new Error('Pola wajah belum terbaca. Silakan ambil ulang.');
        }
        return descriptor;
    };
}
