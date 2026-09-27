export function detectorInputSize(profile) {
    if (profile === 'detailed') return 416;
    return profile === 'compact' ? 224 : 320;
}

export async function initializeFaceBackend(tf) {
    for (const name of ['webgl', 'cpu']) {
        try {
            const enabled = await tf.setBackend(name);
            if (enabled === false) continue;
            await tf.ready();
            if (tf.getBackend() === name) return name;
        } catch {
            // Unavailable WebGL must not prevent a supported CPU camera trial.
        }
    }
    throw new Error('Pemrosesan wajah tidak dapat dimulai melalui WebGL maupun CPU.');
}

export function rendererCategory(renderer) {
    if (!renderer) return 'unknown';
    return /swiftshader|llvmpipe|softpipe|software|basic render/i.test(renderer)
        ? 'software' : 'unconfirmed';
}

// This independent WebGL probe is a hint, not proof of the inference context's GPU.
export function graphicsDiagnostic(document) {
    let gl;
    try {
        const canvas = document.createElement('canvas');
        gl = canvas.getContext('webgl2') || canvas.getContext('webgl');
        if (!gl) return { category: 'unknown', label: 'Informasi grafis tidak tersedia.' };
        const extension = gl.getExtension('WEBGL_debug_renderer_info');
        const renderer = extension ? gl.getParameter(extension.UNMASKED_RENDERER_WEBGL) : '';
        const category = rendererCategory(renderer);
        return { category, label: category === 'software'
            ? 'Probe WebGL menunjukkan renderer perangkat lunak. Periksa akselerasi grafis Chrome.'
            : 'Probe WebGL tersedia; GPU fisik untuk pengenalan belum terkonfirmasi. Periksa chrome://gpu.' };
    } catch {
        return { category: 'unknown', label: 'Browser tidak mengizinkan pemeriksaan grafis.' };
    } finally {
        gl?.getExtension('WEBGL_lose_context')?.loseContext();
    }
}
