import * as faceapi from '@vladmandic/face-api/dist/face-api.esm.js';
import detectorManifest from '@vladmandic/face-api/model/ssd_mobilenetv1_model-weights_manifest.json';
import detectorWeights from '@vladmandic/face-api/model/ssd_mobilenetv1_model.bin?url';
import tinyManifest from '@vladmandic/face-api/model/tiny_face_detector_model-weights_manifest.json';
import tinyWeights from '@vladmandic/face-api/model/tiny_face_detector_model.bin?url';
import landmarkManifest from '@vladmandic/face-api/model/face_landmark_68_tiny_model-weights_manifest.json';
import landmarkWeights from '@vladmandic/face-api/model/face_landmark_68_tiny_model.bin?url';
import recognitionManifest from '@vladmandic/face-api/model/face_recognition_model-weights_manifest.json';
import recognitionWeights from '@vladmandic/face-api/model/face_recognition_model.bin?url';
import { initializeFaceBackend } from './face-prototype-performance';
import { loadFaceWeights, waitForFaceStep } from './siswa-face-weights';
import { createFaceExtractor } from './siswa-face-extractor.js';

let loading;
const detectors = new Map();

export async function loadFaceModels(onProgress = () => {}, detector = 'ssd') {
    if (!['tiny', 'ssd'].includes(detector)) throw new Error('Model deteksi tidak tersedia.');
    if (!loading) {
        loading = (async () => {
            onProgress('Menyiapkan mesin pengenalan wajah…');
            await waitForFaceStep(initializeFaceBackend(faceapi.tf), 15000, 'Mesin pengenalan wajah tidak merespons. Tutup tab ini lalu coba kembali.');
            for (const [network, manifest, weights, label] of [
                [faceapi.nets.faceLandmark68TinyNet, landmarkManifest, landmarkWeights, 'Memuat penanda wajah…'],
                [faceapi.nets.faceRecognitionNet, recognitionManifest, recognitionWeights, 'Memuat pengenal wajah (6,4 MB)…'],
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
    if (!detectors.has(detector)) {
        const [network, manifest, weights] = detector === 'tiny'
            ? [faceapi.nets.tinyFaceDetector, tinyManifest, tinyWeights]
            : [faceapi.nets.ssdMobilenetv1, detectorManifest, detectorWeights];
        onProgress('Memuat pendeteksi wajah…');
        detectors.set(detector, loadFaceWeights(network, faceapi.tf.io, manifest, weights, window.location.href)
            .catch(error => { detectors.delete(detector); throw error; }));
    }
    await detectors.get(detector);
    const extract = createFaceExtractor(faceapi, detector);
    extract.backend = faceapi.tf.getBackend();
    return extract;
}
