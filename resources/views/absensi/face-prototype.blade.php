<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uji Absensi Wajah Gerbang</title>
    <link rel="stylesheet" href="/prototype-assets/app.css?v={{ $assetVersions['css'] }}">
    <script type="module" src="/prototype-assets/app.js?v={{ $assetVersions['js'] }}"></script>
</head>
<body class="bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
<main id="prototype" class="mx-auto flex max-w-7xl flex-col gap-3 p-3 md:px-6"
      data-endpoint="{{ route('face-prototype.match', absolute: false) }}"
      data-access-endpoint="{{ route('face-prototype.access', absolute: false) }}"
      data-model="{{ config('face-prototype.model') }}"
      data-max-references="{{ config('face-prototype.max_references') }}">
    <header class="flex flex-wrap items-center justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold">Uji kamera gerbang · MASUK</h1>
            <p class="text-sm text-slate-600 dark:text-slate-300"><time id="today"></time> · Peserta uji sukarela</p>
        </div>
        <p class="rounded-lg bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-950">Hasil uji tidak mencatat absensi.</p>
    </header>

    <details id="camera-setup" open class="rounded-xl bg-white p-3 shadow-sm dark:bg-slate-900">
        <summary class="font-semibold">1. Pengaturan awal — kode operator, izin, dan kamera</summary>
        <div class="mt-3 grid gap-3 md:grid-cols-3">
            <label class="flex flex-col gap-1 text-sm">Kode operator dari terminal
                <input id="token" type="password" autocomplete="off" class="min-w-0 rounded-lg border border-slate-400 p-2 dark:bg-slate-900" placeholder="Tempel kode sementara">
            </label>
            <label class="flex flex-col gap-1 text-sm">Kamera
                <select id="camera" class="min-w-0 rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                    <option value="user">Kamera depan / webcam</option>
                    <option value="environment">Kamera belakang</option>
                </select>
            </label>
            <label class="flex items-start gap-2 text-sm">
                <input id="consent" type="checkbox" class="mt-1 size-5 shrink-0">
                <span>Peserta bersedia mengikuti uji dan memahami penggunaan referensi wajah sementara. Jangan gunakan data wajah siswa operasional.</span>
            </label>
        </div>
        <p id="operator-status" role="status" class="mt-2 text-sm">Kode akan diperiksa sebelum kamera diaktifkan.</p>
    </details>

    <div id="camera-workspace" class="grid items-start gap-3 md:grid-cols-2">
        <section class="flex min-w-0 flex-col gap-2 rounded-xl bg-white p-3 shadow-sm dark:bg-slate-900" aria-label="Kamera dan petunjuk">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-semibold">2. Kamera</h2>
                <div class="flex flex-wrap gap-2">
                    <button id="start" aria-describedby="camera-help" class="rounded-lg bg-teal-700 px-3 py-2 text-sm font-semibold text-white">Aktifkan kamera</button>
                    <button id="stop" disabled class="rounded-lg border border-slate-400 px-3 py-2 text-sm">Hentikan kamera</button>
                </div>
            </div>
            <p id="camera-help" role="status" class="text-sm">Isi kode operator dan centang persetujuan, lalu klik Aktifkan kamera.</p>
            <div class="relative overflow-hidden rounded-xl bg-slate-950">
                <video id="video" autoplay muted playsinline class="block h-[min(38dvh,340px)] min-h-40 w-full object-contain" aria-describedby="position-help"></video>
                <div id="position-guide" hidden aria-hidden="true" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="h-3/4 w-1/3 rounded-full border-2 border-dashed border-white shadow-sm"></div>
                </div>
            </div>
            <div class="rounded-lg bg-teal-50 p-3 text-teal-950">
                <p class="text-sm font-semibold">Petunjuk gerakan</p>
                <p id="motion-status" role="status" aria-live="polite" class="mt-1 text-lg font-semibold leading-snug">Belum ada uji gerakan.</p>
            </div>
            <p id="position-help" role="status" class="text-sm">Aktifkan kamera untuk melihat panduan posisi.</p>
            <label class="flex items-center gap-2 text-sm">
                <input id="show-position-guide" type="checkbox" class="size-4 shrink-0">
                <span>Bingkai bantuan opsional — seluruh gambar tetap dipindai</span>
            </label>
        </section>

        <section class="flex min-w-0 flex-col gap-3 rounded-xl bg-white p-3 shadow-sm dark:bg-slate-900" aria-label="Kontrol uji dan hasil">
            <details id="reference-setup" open>
                <summary class="font-semibold">3. Referensi peserta</summary>
                <div class="mt-2 flex flex-wrap items-end gap-2">
                    <label class="flex flex-col gap-1 text-sm">Alias peserta
                        <select id="alias" class="rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                            @for ($i = 1; $i <= 20; $i++)
                                <option>{{ sprintf('UJI-%03d', $i) }}</option>
                            @endfor
                        </select>
                    </label>
                    <button id="enroll" disabled class="rounded-lg bg-slate-700 px-3 py-2 text-sm font-semibold text-white">Ambil referensi</button>
                </div>
                <p class="mt-2 text-sm">Satu orang selalu memakai alias yang sama. Mulai dari satu referensi; tambahan sudut bersifat opsional.</p>
            </details>
            <p id="references" class="text-sm">Belum ada referensi.</p>
            <div class="grid gap-2 sm:grid-cols-2">
                <label class="flex min-w-0 flex-col gap-1 text-sm">Skenario uji
                    <select id="trial-scenario" class="w-full min-w-0 rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                        <option value="frontal">Wajah — menghadap depan</option>
                        <option value="turned">Wajah — sedikit menoleh</option>
                        <option value="lighting">Wajah — cahaya/jarak</option>
                        <option value="photo">Foto diri</option>
                        <option value="replay">Video diri</option>
                        <option value="empty">Area tanpa wajah</option>
                    </select>
                </label>
                <label class="flex min-w-0 flex-col gap-1 text-sm">Mode deteksi
                    <select id="performance-profile" class="w-full min-w-0 rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                        <option value="standard">Standar — 320</option>
                        <option value="detailed">Lebih detail — 416</option>
                        <option value="compact">Lebih ringan — 224</option>
                    </select>
                </label>
            </div>
            <button id="challenge" disabled class="rounded-lg bg-teal-700 px-3 py-3 font-semibold text-white">Uji gerakan acak lalu cocokkan</button>
            <p class="text-sm text-slate-600 dark:text-slate-300">Ikuti petunjuk di bawah kamera. Batas 25 detik. Gerakan belum menjamin keaslian wajah.</p>
            <div class="flex flex-wrap gap-2">
                <button id="scan" disabled class="rounded-lg border border-slate-400 px-3 py-2 text-sm">Pindai sekali</button>
                <button id="continuous" disabled class="rounded-lg border border-slate-400 px-3 py-2 text-sm">Mulai uji berulang</button>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300">Dua tombol di atas adalah pembanding tanpa tantangan gerakan.</p>
            <div class="rounded-lg bg-slate-100 p-3 dark:bg-slate-800">
                <p id="result" class="text-lg font-semibold" aria-live="polite">Belum ada hasil</p>
                <p id="candidate" class="mt-1 text-sm">Kandidat akan ditampilkan dengan alias peserta.</p>
                <p id="received" class="mt-1 text-sm"></p>
            </div>
            <p id="status" role="status" aria-live="polite" class="text-sm">Masukkan kode operator dan persetujuan peserta untuk mulai.</p>
            <p id="trial-warning" role="status" class="text-sm font-semibold text-amber-800 dark:text-amber-300"></p>
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-300 pt-2">
                <button id="download" disabled class="rounded-lg border border-slate-400 px-3 py-2 text-sm">Unduh metrik CSV</button>
                <button id="reset" class="text-sm font-semibold text-red-700 underline dark:text-red-400">Hapus referensi dan hasil</button>
            </div>
            <p id="download-status" role="status" class="text-sm"></p>
        </section>
    </div>

    <details id="metrics-panel" class="rounded-xl bg-white p-3 shadow-sm dark:bg-slate-900">
        <summary class="font-semibold">Rincian waktu dan ringkasan percobaan</summary>
        <div class="mt-3 grid gap-4 md:grid-cols-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <caption class="pb-2 text-left">Waktu pemindaian terakhir (milidetik)</caption>
                    <thead><tr class="border-b border-slate-300"><th class="py-2">Tahap</th><th>Waktu</th></tr></thead>
                    <tbody>
                    @foreach (['model' => 'Muat model (sekali)', 'detect' => 'Deteksi di perangkat', 'quality' => 'Pemeriksaan kualitas gambar', 'extract' => 'Landmark + ekstraksi vektor', 'roundtrip' => 'Kirim sampai respons diterima (RTT)', 'server' => 'Server sampai hasil siap', 'match' => 'Pencocokan saja di server', 'network' => 'Sisa RTT di luar server (perkiraan)', 'render' => 'Respons sampai frame tampilan', 'total' => 'Deteksi sampai frame tampilan', 'bootstrap' => 'Detail server: persiapan aplikasi', 'guard' => 'Detail server: akses dan isolasi', 'validation' => 'Detail server: routing dan validasi'] as $key => $label)
                        <tr class="border-b border-slate-200 dark:border-slate-700"><td class="py-2">{{ $label }}</td><td id="timing-{{ $key }}">—</td></tr>
                    @endforeach
                    <tr><td class="py-2">Penyimpanan absensi</td><td>Tidak dijalankan</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 text-sm">
                <p id="statistics">Belum ada sampel.</p>
                <p id="payload"></p>
                <p id="performance-info" role="status">Informasi pemrosesan muncul setelah kamera aktif.</p>
                <p>Waktu server sudah termasuk dalam RTT. Jangan dijumlahkan lagi. Sisa RTT bukan pengukuran upload murni.</p>
                <p>Pada uji gerakan, total mencakup rangkaian tantangan; deteksi, kualitas, dan ekstraksi hanya menampilkan frame terakhir. Jangan membandingkan total ini langsung dengan pemindaian biasa.</p>
            </div>
        </div>
    </details>

    <div id="csv-fallback" hidden class="rounded-xl bg-white p-3 dark:bg-slate-900">
        <label for="csv-preview" class="text-sm">Cadangan CSV. Jika unduhan tidak muncul, pilih teks lalu salin dengan Ctrl+C.</label>
        <textarea id="csv-preview" readonly rows="5" class="mt-2 w-full rounded-lg border border-slate-400 p-2 text-sm dark:bg-slate-900"></textarea>
        <button id="select-csv" type="button" class="rounded-lg border border-slate-400 px-3 py-2 text-sm">Pilih seluruh isi CSV</button>
    </div>
    <details class="rounded-xl bg-white p-3 text-sm shadow-sm dark:bg-slate-900">
        <summary class="font-semibold">Panduan pengujian dan batas prototipe</summary>
        <div class="mt-3 flex flex-col gap-3">
            <p>Setelah kandidat ditemukan, keluar dari gambar hingga dua pemeriksaan melihat area kosong. Gunakan Pindai sekali dua kali saat area kosong, atau uji berulang agar pemeriksaan berjalan otomatis. Mengganti skenario tidak membuka kunci.</p>
            <p>Skenario merupakan catatan operator. Saat uji foto/video, gunakan milik peserta yang bersedia dan hindari wajah langsung ikut terlihat. Jangan mengambil referensi dari foto/video. Orang yang tidak terdaftar juga perlu diuji.</p>
            <p>Tantangan mengacak urutan menutup–membuka mata dan menoleh. Awali dengan mata terbuka dan wajah lurus. Tutup mata sebentar, lalu buka; kedipan sangat cepat dapat terlewat. Wajah hilang, lebih dari satu wajah, atau konsistensi wajah berubah akan menghentikan percobaan.</p>
            <p>Ini bukan model anti-spoofing: foto bergerak, video, dan manipulasi browser masih perlu diuji. Tidak ada bukti liveness terverifikasi server. Peserta yang sulit mengikuti gerakan perlu pemeriksaan manual.</p>
            <p>Bingkai hanya panduan, bukan batas deteksi. Petunjuk diperbarui saat mengambil referensi atau memindai, bukan setiap gerakan. Mode 224 memakai masukan deteksi lebih kecil; 416 lebih detail dan mungkin lebih lambat. Referensi selalu diambil dengan 320. Gunakan jarak, cahaya, dan referensi yang sama untuk membandingkan mode.</p>
            <p>Kandidat bukan keputusan kehadiran. Jarak bukan persentase keyakinan. Ambang {{ config('face-prototype.threshold') }} dan selisih antarpeserta {{ config('face-prototype.minimum_gap') }} masih perlu kalibrasi. Referensi dengan alias sama dikelompokkan; batas 50 referensi hanya membatasi beban uji.</p>
            <p>Referensi ada di memori tab, dikirim ke server uji setiap pencocokan, dan hilang saat halaman dimuat ulang. Foto tidak diunggah. Tidak ada penyimpanan absensi atau antrean offline. Jeda peserta hanya berlaku di tab ini; wajah gagal terdeteksi bisa dianggap area kosong.</p>
            <p>CSV tidak memuat alias, foto, atau vektor. attempt_ms adalah durasi percobaan; total_ms hanya diisi pada pencocokan selesai. Pemeriksaan jeda, pendaftaran, dan pembatalan tidak dihitung. motion_status, motion_ms, motion_frames, dan motion_plan mencatat uji gerakan; passed_motion_check bukan jaminan keaslian. Angka percobaan bukan akurasi atau jumlah siswa.</p>
        </div>
    </details>
</main>
</body>
</html>
