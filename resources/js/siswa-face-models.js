import * as faceapi from '@vladmandic/face-api/dist/face-api.esm.js';
import detectorManifest from '@vladmandic/face-api/model/tiny_face_detector_model-weights_manifest.json';
import detectorWeights from '@vladmandic/face-api/model/tiny_face_detector_model.bin?url';
import landmarkManifest from '@vladmandic/face-api/model/face_landmark_68_tiny_model-weights_manifest.json';
import landmarkWeights from '@vladmandic/face-api/model/face_landmark_68_tiny_model.bin?url';
import recognitionManifest from '@vladmandic/face-api/model/face_recognition_model-weights_manifest.json';
import recognitionWeights from '@vladmandic/face-api/model/face_recognition_model.bin?url';
import { initializeFaceBackend } from './face-prototype-performance';
import { loadFaceWeights, waitForFaceStep } from './siswa-face-weights';
import { motionSignals } from './face-prototype-challenge.js';

let loading;

export async function loadFaceModels(onProgress = () => {}) {
    if (!loading) {
        loading = (async () => {
            onProgress('Menyiapkan mesin pengenalan wajah…');
            await waitForFaceStep(initializeFaceBackend(faceapi.tf), 15000, 'Mesin pengenalan wajah tidak merespons. Tutup tab ini lalu coba kembali.');
            for (const [network, manifest, weights, label] of [
                [faceapi.nets.tinyFaceDetector, detectorManifest, detectorWeights, '1/3: Memuat pendeteksi wajah…'],
                [faceapi.nets.faceLandmark68TinyNet, landmarkManifest, landmarkWeights, '2/3: Memuat penanda wajah…'],
                [faceapi.nets.faceRecognitionNet, recognitionManifest, recognitionWeights, '3/3: Memuat pengenal wajah (6,4 MB)…'],
            ]) {
                onProgress(label);
                await loadFaceWeights(network, faceapi.tf.io, manifest, weights, window.location.href);
            }
        })().catch(error => {
            loading = null;
            throw error;
        });
    }
    await loading;
    const observe = async canvas => {
        const faces = await faceapi.detectAllFaces(canvas, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.65 }))
            .withFaceLandmarks(true).withFaceDescriptors();
        if (faces.length !== 1) {
            const error = new Error(faces.length ? 'Ada lebih dari satu wajah. Pastikan hanya siswa ini di depan kamera.' : 'Wajah belum terdeteksi. Perbaiki posisi dan pencahayaan, lalu coba lagi.');
            error.code = faces.length ? 'multiple_faces' : 'no_face';
            throw error;
        }
        const descriptor = Array.from(faces[0].descriptor);
        if (descriptor.length !== 128 || !descriptor.every(Number.isFinite)) {
            throw new Error('Pola wajah belum terbaca. Silakan ambil ulang.');
        }
        return { descriptor, landmarks: faces[0].landmarks };
    };
    const extract = async canvas => (await observe(canvas)).descriptor;
    extract.motion = async canvas => {
        const { descriptor, landmarks } = await observe(canvas);
        return { descriptor, yaw: motionSignals(landmarks).yaw };
    };
    extract.backend = faceapi.tf.getBackend();
    return extract;
}
