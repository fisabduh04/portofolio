export function cameraPrerequisiteMessage(token, consent) {
    if (token.trim().length < 32) {
        return 'Langkah 1: salin kode operator sementara dari terminal, lalu tempelkan ke kolom Kode operator di atas.';
    }
    if (!consent) {
        return 'Langkah 2: centang persetujuan peserta uji di atas agar tombol Aktifkan kamera dapat digunakan.';
    }
    return 'Siap. Klik Aktifkan kamera, lalu pilih Izinkan jika Chrome meminta akses kamera.';
}

export function cameraActivationIssue(token, consent) {
    if (token.trim().length < 32) return { field: 'token', message: 'Kode operator belum lengkap. Tempelkan kode dari terminal terlebih dahulu.' };
    if (!consent) return { field: 'consent', message: 'Centang persetujuan peserta uji terlebih dahulu, lalu klik Aktifkan kamera.' };
    return null;
}

export function pauseHiddenCamera({ hidden, active, stop, notify }) {
    if (!hidden || !active) return;
    stop();
    notify('Kamera dihentikan karena tab tidak aktif. Kembali ke halaman ini lalu klik Aktifkan kamera untuk melanjutkan.');
}

export function faceRequestError(status, message) {
    if (status === 401) {
        return 'Kode operator tidak cocok dengan server yang sedang berjalan. Tempelkan kode dari terminal aktif, lalu pindai kembali.';
    }
    return `Server menolak uji (${status}): ${message || 'Periksa data uji.'}`;
}

export function scanFailureMessage(error) {
    return error.name === 'AbortError' || error instanceof TypeError
        ? 'Server/jaringan tidak tersedia atau waktu habis. Tidak ada absensi tersimpan. Coba pindai ulang.'
        : error.message;
}

export function operatorCodeFromLink(fragment) {
    const code = new URLSearchParams(fragment.replace(/^#/, '')).get('code') || '';
    return /^[a-f0-9]{48}$/.test(code) ? code : '';
}

export async function verifyOperatorCode(endpoint, token, signal, request = fetch) {
    const response = await request(endpoint, {
        method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', Authorization: `Bearer ${token.trim()}` },
        body: '{}', credentials: 'omit', cache: 'no-store', signal,
    });
    const result = await response.json();
    if (!response.ok) throw new Error(faceRequestError(response.status, result.message));
    if (result.authorized !== true) throw new Error('Server belum mengonfirmasi kode operator.');
}
