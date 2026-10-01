export async function waitForFaceStep(operation, timeoutMs, message) {
    let timer;
    try {
        return await Promise.race([
            operation,
            new Promise((resolve, reject) => {
                timer = setTimeout(() => {
                    const error = new Error(message);
                    error.name = 'FaceStartupTimeout';
                    reject(error);
                }, timeoutMs);
            }),
        ]);
    } finally {
        clearTimeout(timer);
    }
}

export async function loadFaceWeights(network, io, manifest, weights, baseUrl) {
    const asset = new URL(weights, baseUrl);
    const filename = asset.pathname.slice(asset.pathname.lastIndexOf('/') + 1) + asset.search;
    const directory = new URL('.', asset).href;
    const weightMap = await waitForFaceStep(
        io.loadWeights(manifest.map(group => ({ ...group, paths: [filename] })), directory),
        30000,
        'Unduhan model wajah terlalu lama. Periksa koneksi lalu aktifkan kamera kembali.',
    );
    network.loadFromWeightMap(weightMap);
}
