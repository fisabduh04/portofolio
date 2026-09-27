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
<main id="prototype" class="mx-auto flex max-w-6xl flex-col gap-6 p-4 md:p-8"
      data-endpoint="{{ route('face-prototype.match', absolute: false) }}"
      data-access-endpoint="{{ route('face-prototype.access', absolute: false) }}"
      data-model="{{ config('face-prototype.model') }}"
      data-max-references="{{ config('face-prototype.max_references') }}">
    <header class="flex flex-col gap-2">
        <p class="text-sm font-semibold uppercase tracking-wide text-teal-700 dark:text-teal-400">Prototipe tahap 1 · MASUK</p>
        <h1 class="text-2xl font-bold md:text-3xl">Uji kamera gerbang</h1>
        <p>Tanggal otomatis: <time id="today"></time> · <strong>Hasil uji tidak mencatat absensi.</strong></p>
        <p>Uji ketat: satu wajah jelas. Setelah kandidat ditemukan, keluar dari gambar hingga dua pemeriksaan melihat area kosong. Gunakan uji berulang agar pemeriksaan ini berjalan otomatis.</p>
        <p>Pemeriksaan kualitas belum mendeteksi foto atau video palsu. Batas kualitas masih perlu diuji pada kamera ini.</p>
    </header>
    <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-950">
        Khusus peserta uji sukarela. Gunakan alias UJI-001 dan seterusnya; jangan memakai data wajah siswa operasional.
        Referensi hanya ada di memori tab dan dikirim ke server uji untuk setiap pencocokan. Foto tidak diunggah.
        Muat ulang atau tutup tab untuk menghapus referensi. Tidak ada penyimpanan absensi atau antrean offline.
    </div>
    <div class="grid gap-6 lg:grid-cols-2">
        <section class="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900" aria-label="Kamera dan peserta">
            <label class="flex flex-col gap-2 font-medium">Kode operator dari terminal
                <input id="token" type="password" autocomplete="off" class="rounded-lg border border-slate-400 p-2" placeholder="Kode sementara saat peluncur dijalankan">
            </label>
            <p id="operator-status" role="status" class="text-sm">Kode akan diperiksa sebelum kamera diaktifkan.</p>
            <label class="flex items-start gap-3 text-sm">
                <input id="consent" type="checkbox" class="mt-1 size-5 shrink-0">
                <span>Saya memastikan semua peserta yang direkam bersedia mengikuti uji ini dan memahami penggunaan referensi wajah sementara.</span>
            </label>
            <label class="flex flex-col gap-2">Kamera
                <select id="camera" class="rounded-lg border border-slate-400 p-2">
                    <option value="user">Kamera depan / webcam</option>
                    <option value="environment">Kamera belakang</option>
                </select>
            </label>
            <div class="relative overflow-hidden rounded-xl bg-slate-950">
                <video id="video" autoplay muted playsinline class="aspect-video w-full object-contain" aria-describedby="position-help"></video>
                <div id="position-guide" hidden aria-hidden="true" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <div class="h-3/4 w-1/3 rounded-full border-2 border-dashed border-white shadow-sm"></div>
                </div>
            </div>
            <label class="flex items-center gap-3 text-sm">
                <input id="show-position-guide" type="checkbox" class="size-5 shrink-0">
                <span>Tampilkan bingkai bantuan (opsional; seluruh gambar tetap dipindai)</span>
            </label>
            <p id="position-help" role="status" class="text-sm font-semibold">Aktifkan kamera untuk melihat panduan posisi.</p>
            <p class="text-sm">Bingkai hanya panduan penempatan, bukan batas deteksi atau bukti identitas. Petunjuk diperbarui saat mengambil referensi atau memindai; tidak mengikuti gerakan secara langsung.</p>
            <div class="flex flex-wrap gap-3">
                <button id="start" aria-describedby="camera-help" class="rounded-lg bg-teal-700 px-4 py-3 font-semibold text-white">Aktifkan kamera</button>
                <button id="stop" disabled class="rounded-lg border border-slate-400 px-4 py-3">Hentikan kamera</button>
            </div>
            <p id="camera-help" role="status" class="text-sm font-semibold">Isi kode operator dan centang persetujuan, lalu klik Aktifkan kamera.</p>
            <p id="status" role="status" aria-live="polite" class="min-h-12 text-sm">Masukkan kode operator dan persetujuan peserta untuk mulai.</p>
            <div class="flex flex-col gap-3 border-t border-slate-300 pt-4">
                <label class="flex flex-col gap-2">Alias peserta untuk referensi
                    <select id="alias" class="rounded-lg border border-slate-400 p-2">
                        @for ($i = 1; $i <= 20; $i++)
                            <option>{{ sprintf('UJI-%03d', $i) }}</option>
                        @endfor
                    </select>
                </label>
                <button id="enroll" disabled class="rounded-lg bg-slate-700 px-4 py-3 font-semibold text-white">Ambil satu referensi peserta ini</button>
                <p id="references" class="text-sm">Belum ada referensi.</p>
                <p class="text-sm text-slate-600 dark:text-slate-300">Mulai dengan satu referensi. Tambahkan sudut berbeda bila diperlukan; tidak ada kewajiban lima foto. Batas total 50 referensi hanya untuk membatasi beban uji.</p>
                <button id="reset" class="self-start text-sm font-semibold text-red-700 underline dark:text-red-400">Hapus semua referensi dan hasil</button>
            </div>
        </section>
        <section class="flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm dark:bg-slate-900" aria-label="Hasil dan pengukuran">
            <h2 class="text-xl font-bold">Pencocokan MASUK</h2>
            <label class="flex flex-col gap-2">Skenario uji mandiri
                <select id="trial-scenario" class="rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                    <option value="frontal">Wajah langsung — menghadap depan</option>
                    <option value="turned">Wajah langsung — sedikit menoleh</option>
                    <option value="lighting">Wajah langsung — perubahan cahaya/jarak</option>
                    <option value="photo">Foto diri — layar atau cetakan</option>
                    <option value="replay">Video diri — diputar ulang</option>
                    <option value="empty">Area tanpa wajah</option>
                </select>
            </label>
            <p class="text-sm">Skenario adalah catatan pilihan operator, bukan hasil deteksi keaslian. Pilih wajah langsung untuk mengambil referensi. Saat uji foto/video, hanya tampilkan foto/video diri sendiri di kamera dan hindari wajah langsung ikut terlihat. Referensi tidak ditambah dari foto/video.</p>
            <p class="text-sm">Gunakan Pindai sekali sebanyak 3–5 percobaan per kondisi. Setelah kandidat ditemukan, keluar dari gambar dan pindai area kosong dua kali sebelum percobaan berikutnya. Mengganti skenario tidak membuka kunci peserta.</p>
            <label class="flex flex-col gap-2">Mode uji kecepatan
                <select id="performance-profile" class="rounded-lg border border-slate-400 p-2 dark:bg-slate-900">
                    <option value="standard">Standar — deteksi 320</option>
                    <option value="detailed">Uji lebih detail — deteksi 416 (mungkin lebih lambat)</option>
                    <option value="compact">Percobaan lebih ringan — deteksi 224</option>
                </select>
            </label>
            <p class="text-sm">Mode 224 mengurangi ukuran masukan detektor, bukan mengganti model pengenal. Wajah kecil dapat lebih mudah terlewat. Referensi selalu diambil dengan deteksi 320. Bandingkan kecepatan dan keberhasilan dengan jarak serta cahaya yang sama.</p>
            <p class="text-sm">Mode 416 menguji deteksi dengan masukan lebih detail. Batas deteksi, kualitas gambar, dan pencocokan tetap sama; keberhasilan dan akurasinya perlu dibandingkan. Gunakan referensi yang sama saat berganti mode.</p>
            <p id="performance-info" role="status" class="text-sm">Informasi pemrosesan muncul setelah kamera aktif. Tidak ada pengaturan Chrome yang diubah otomatis.</p>
            <p class="text-sm">Hadapkan satu wajah ke kamera. Peserta yang tidak terdaftar juga perlu diuji untuk mengukur penolakan.</p>
            <div class="flex flex-col gap-3 rounded-xl border border-amber-300 bg-amber-50 p-4 text-amber-950">
                <p class="font-semibold">Uji tantangan gerakan (eksperimental)</p>
                <p class="text-sm">Urutan menutup–membuka mata dan menoleh dipilih acak. Awali dengan mata terbuka dan wajah lurus. Saat diminta, tutup mata sebentar lalu buka, bukan hanya berkedip sangat cepat. Ikuti petunjuk arah pada gambar kamera.</p>
                <p class="text-sm">Batas waktu 25 detik. Wajah hilang, lebih dari satu wajah, atau konsistensi wajah berubah akan menghentikan percobaan. Ini bukan model anti-spoofing: foto bergerak, video, dan manipulasi browser masih perlu diuji. Tidak ada bukti liveness terverifikasi server.</p>
                <button id="challenge" disabled class="rounded-lg bg-teal-700 px-4 py-3 font-semibold text-white">Uji gerakan acak lalu cocokkan</button>
                <p id="motion-status" role="status" aria-live="polite" class="text-sm font-semibold">Belum ada uji gerakan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button id="scan" disabled class="rounded-lg bg-teal-700 px-4 py-3 font-semibold text-white">Pindai sekali</button>
                <button id="continuous" disabled class="rounded-lg border border-slate-400 px-4 py-3">Mulai uji berulang</button>
            </div>
            <p class="text-sm">Pindai sekali dan Mulai uji berulang tetap mode pembanding tanpa tantangan gerakan.</p>
            <div class="rounded-xl bg-slate-100 p-4 dark:bg-slate-800">
                <p id="result" class="text-lg font-semibold" aria-live="polite">Belum ada hasil</p>
                <p id="candidate" class="mt-2 text-sm">Kandidat akan ditampilkan dengan alias peserta.</p>
                <p id="received" class="mt-2 text-sm"></p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <caption class="pb-3 text-left font-semibold">Waktu pemindaian terakhir (milidetik)</caption>
                    <thead><tr class="border-b border-slate-300"><th class="py-2">Tahap</th><th>Waktu</th></tr></thead>
                    <tbody>
                    @foreach (['model' => 'Muat model (sekali)', 'detect' => 'Deteksi di perangkat', 'quality' => 'Pemeriksaan kualitas gambar', 'extract' => 'Landmark + ekstraksi vektor', 'roundtrip' => 'Kirim sampai respons diterima (RTT)', 'server' => 'Server sampai hasil siap', 'match' => 'Pencocokan saja di server', 'network' => 'Sisa RTT di luar server (perkiraan)', 'render' => 'Respons sampai frame tampilan', 'total' => 'Deteksi sampai frame tampilan'] as $key => $label)
                        <tr class="border-b border-slate-200 dark:border-slate-700"><td class="py-2">{{ $label }}</td><td id="timing-{{ $key }}">—</td></tr>
                    @endforeach
                    @foreach (['bootstrap' => 'Detail server: persiapan aplikasi', 'guard' => 'Detail server: pemeriksaan akses dan isolasi', 'validation' => 'Detail server: routing dan validasi'] as $key => $label)
                        <tr class="border-b border-slate-200 dark:border-slate-700"><td class="py-2">{{ $label }}</td><td id="timing-{{ $key }}">—</td></tr>
                    @endforeach
                    <tr><td class="py-2">Penyimpanan absensi</td><td>Tidak dijalankan</td></tr>
                    </tbody>
                </table>
            </div>
            <p id="payload" class="text-sm"></p>
            <p class="text-sm">Rincian server sudah termasuk waktu server dan RTT; jangan dijumlahkan lagi. Pengukuran berakhir sebelum pengiriman respons selesai.</p>
            <p id="statistics" class="text-sm">Belum ada sampel. Pengukuran pertama biasanya lebih lambat.</p>
            <p class="text-sm">Pada uji gerakan, total waktu mencakup rangkaian tantangan; deteksi, kualitas, dan ekstraksi menampilkan frame terakhir saja. CSV menambahkan motion_status, motion_ms, motion_frames, dan motion_plan. passed_motion_check hanya berarti gerakan teramati, bukan jaminan keaslian. Jangan membandingkan total waktunya langsung dengan pemindaian biasa.</p>
            <p id="trial-warning" role="status" class="text-sm font-semibold"></p>
            <button id="download" disabled class="self-start rounded-lg border border-slate-400 px-4 py-2">Unduh metrik CSV</button>
            <p id="download-status" role="status" class="text-sm"></p>
            <div id="csv-fallback" hidden>
                <label for="csv-preview" class="text-sm">Salinan CSV dari saat tombol unduh ditekan. Jika file tidak muncul, pilih teks lalu tekan Ctrl+C dan tempel di percakapan.</label>
                <textarea id="csv-preview" readonly rows="6" class="mt-2 w-full rounded-lg border border-slate-400 p-2 text-sm dark:bg-slate-900"></textarea>
                <button id="select-csv" type="button" class="rounded-lg border border-slate-400 px-4 py-2">Pilih seluruh isi CSV</button>
            </div>
            <details class="text-sm">
                <summary class="font-semibold">Cara membaca hasil dan batas uji</summary>
                <p class="mt-3">Kandidat bukan keputusan kehadiran. Jarak lebih kecil berarti vektor lebih dekat; angka ini bukan persentase keyakinan. Ambang sementara {{ config('face-prototype.threshold') }} dan selisih antarpeserta {{ config('face-prototype.minimum_gap') }} masih perlu kalibrasi. Referensi dari orang yang sama dikelompokkan.</p>
                <p class="mt-3">RTT sudah termasuk waktu server; jangan menjumlahkannya lagi. Sisa RTT bukan pengukuran upload murni. Waktu layar memakai pergantian frame browser, bukan pengukuran piksel fisik. CSV hanya berisi metrik, jumlah referensi, dan status, tanpa alias atau vektor.</p>
                <p class="mt-3">CSV mencatat percobaan selesai, penolakan gambar, dan kesalahan beserta skenario yang dipilih. Kolom attempt_ms mengukur durasi percobaan; total_ms hanya diisi untuk pencocokan selesai. Pemeriksaan jeda peserta, pendaftaran referensi, dan percobaan yang dibatalkan tidak dihitung. Angka ini bukan akurasi atau jumlah siswa. Tidak ada alias, foto, atau vektor di CSV. Jeda peserta hanya berlaku di tab ini, bukan pencegahan absensi ganda di server. Wajah gagal terdeteksi bisa dianggap area kosong. Belum ada pemeriksaan keaslian wajah.</p>
            </details>
        </section>
    </div>
</main>
</body>
</html>
