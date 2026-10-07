<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => $mode === 'mapel' ? 'Presensi Guru Mapel' : 'Guru Piket', 'href' => $mode === 'mapel' ? route('jadwal.presensiHarian') : route('absensi.piket')],
        ['name' => 'Absensi Wajah', 'href' => '#'],
    ]" />
    <section data-face-attendance data-endpoint="{{ route('face-attendance.store') }}" data-mode="{{ $mode }}" data-jadwal="{{ $jadwal?->id }}" data-type="{{ $type }}" class="mt-5 space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $mode === 'mapel' ? $jadwal->kelas->kelas.' · '.$jadwal->mapel->mapel : 'Semua kelas · '.($type === 'masuk' ? 'Presensi masuk' : 'Presensi pulang') }}</p>
            <span class="w-fit rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ now()->locale('id')->translatedFormat('l, d F Y') }} · {{ config('app.timezone') }}</span>
        </div>
        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
            <div class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pindai siswa</h2>
                    <span data-scan-count class="text-xs text-gray-500 dark:text-gray-400">0 presensi baru</span>
                </div>
                <div class="space-y-3 p-3 sm:space-y-4 sm:p-5">
                    <div>
                        <div class="flex items-center gap-3">
                            <label for="scan-camera-facing" class="shrink-0 text-sm font-medium text-gray-900 dark:text-white">Kamera</label>
                            <select id="scan-camera-facing" data-scan-facing aria-describedby="scan-camera-help" class="block min-h-11 min-w-0 flex-1 rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                <option value="user">Kamera depan / webcam</option>
                                <option value="environment">Kamera belakang</option>
                            </select>
                        </div>
                        <p id="scan-camera-help" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Matikan kamera untuk mengganti depan / belakang.</p>
                    </div>
                    <div>
                        <label for="scan-detector" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Model deteksi wajah</label>
                        <select id="scan-detector" data-scan-detector aria-describedby="scan-detector-help" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="tiny" selected>TinyFaceDetector</option>
                            <option value="ssd">SSD MobileNet V1</option>
                        </select>
                        <p id="scan-detector-help" class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Matikan kamera untuk mengganti model. Bandingkan jarak Euclidean dan waktu deteksi dengan siswa serta pencahayaan yang sama. Pemindaian tetap mencatat presensi.</p>
                    </div>
                    <div class="relative aspect-video overflow-hidden rounded-lg bg-gray-950">
                        <video data-scan-video autoplay muted playsinline class="size-full -scale-x-100 object-contain" aria-label="Kamera absensi wajah"></video>
                        <div data-scan-placeholder class="absolute inset-0 flex items-center justify-center px-6 text-center text-sm text-gray-300">Aktifkan kamera untuk memulai absensi.</div>
                    </div>
                    <div role="group" aria-label="Kontrol kamera dan pemindaian" class="grid grid-cols-2 gap-2 rounded-xl bg-gray-50 p-2 dark:bg-gray-900/50 xl:grid-cols-4">
                        <button data-scan-start type="button" class="h-10 min-w-0 touch-manipulation whitespace-nowrap rounded-lg border border-blue-200 bg-white px-2 text-xs font-medium leading-5 sm:text-sm text-blue-700 transition-colors hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50 dark:border-blue-800 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-gray-700">Aktifkan</button>
                        <button data-scan-stop type="button" disabled class="h-10 min-w-0 touch-manipulation whitespace-nowrap rounded-lg border border-gray-300 bg-white px-2 text-xs font-medium leading-5 sm:text-sm text-gray-700 transition-colors hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Matikan</button>
                        <button data-scan-auto type="button" disabled class="h-10 min-w-0 touch-manipulation whitespace-nowrap rounded-lg bg-green-700 px-2 text-xs font-medium leading-5 sm:text-sm text-white transition-colors hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:cursor-not-allowed disabled:opacity-50">Mulai otomatis</button>
                        <button data-scan-capture type="button" disabled class="h-10 min-w-0 touch-manipulation whitespace-nowrap rounded-lg bg-blue-700 px-2 text-xs font-medium leading-5 sm:text-sm text-white transition-colors hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50">Pindai & catat</button>
                    </div>
                    <p data-scan-message role="status" aria-live="polite" class="text-sm leading-5 text-gray-600 dark:text-gray-300">Satu siswa setiap pemindaian. Hasil yang cocok langsung dicatat ke logbook.</p>
                    <div class="rounded-lg bg-blue-50 p-3 dark:bg-blue-900/30 sm:p-4" role="status" aria-live="polite">
                        <p data-scan-auto-status class="text-sm font-medium text-blue-800 dark:text-blue-200">Untuk antrean: aktifkan kamera, lalu mulai pemindaian otomatis sekali.</p>
                    </div>
                </div>
            </div>
            <aside aria-labelledby="scan-results-heading" class="min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:sticky lg:top-20">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 id="scan-results-heading" class="text-base font-semibold text-gray-900 dark:text-white">Hasil sesi ini</h2>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cocokkan nama dengan siswa di depan kamera.</p>
                </div>
                <div class="p-4 sm:p-5">
                    <div data-scan-feedback data-state="ready" role="status" aria-live="polite" aria-atomic="true" class="rounded-xl border border-gray-200 bg-gray-50 p-5 data-[state=success]:border-emerald-300 data-[state=success]:bg-emerald-50 data-[state=error]:border-red-300 data-[state=error]:bg-red-50 data-[state=processing]:border-blue-300 data-[state=processing]:bg-blue-50 dark:border-gray-600 dark:bg-gray-900 dark:data-[state=success]:border-emerald-700 dark:data-[state=success]:bg-emerald-950 dark:data-[state=error]:border-red-700 dark:data-[state=error]:bg-red-950 dark:data-[state=processing]:border-blue-700 dark:data-[state=processing]:bg-blue-950">
                        <p data-scan-result-label class="text-sm font-semibold text-gray-700 dark:text-gray-200">Menunggu pemindaian</p>
                        <p data-scan-identity class="mt-3 break-words text-2xl font-bold leading-tight text-gray-900 dark:text-white xl:text-3xl">Belum ada hasil</p>
                        <p data-scan-result-detail class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">Aktifkan kamera, lalu mulai pemindaian.</p>
                    </div>
                    <div class="mt-4 rounded-xl border border-gray-200 p-4 dark:border-gray-600" role="status" aria-live="polite" aria-atomic="true">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Monitor jarak Euclidean</h3>
                        <p data-scan-match-status class="mt-2 text-sm text-gray-600 dark:text-gray-300">Belum ada hasil pencocokan untuk pemindaian ini.</p>
                        <p data-scan-match-candidates class="mt-2 break-words text-sm text-gray-900 dark:text-white">Belum ada kandidat siswa.</p>
                        <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                            @foreach ([
                                'distance' => 'Jarak terdekat',
                                'threshold' => 'Ambang maksimum',
                                'second_distance' => 'Jarak kandidat kedua',
                                'gap' => 'Selisih dua kandidat',
                                'minimum_gap' => 'Selisih minimum',
                            ] as $metric => $label)
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                                    <dd data-scan-match-{{ $metric }} class="mt-1 font-semibold tabular-nums text-gray-900 dark:text-white">—</dd>
                                </div>
                            @endforeach
                        </dl>
                        <p class="mt-3 text-xs leading-5 text-gray-500 dark:text-gray-400">Semakin kecil jarak, semakin mirip; bukan persentase keyakinan. Jarak harus ≤ ambang dan, jika ada dua kandidat siswa, selisih harus ≥ minimum. Angka dibulatkan ke 4 desimal; keputusan memakai nilai asli. Tanda — berarti tidak tersedia.</p>
                    </div>
                    <div class="mt-5 flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Riwayat pemindaian</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">20 hasil terakhir</span>
                    </div>
                    <p data-scan-empty class="mt-3 text-sm text-gray-500 dark:text-gray-400">Belum ada siswa dipindai.</p>
                    <ul data-scan-results aria-label="Riwayat hasil pemindaian sesi ini" class="mt-3 max-h-72 overflow-y-auto overscroll-contain divide-y divide-gray-200 dark:divide-gray-700 lg:max-h-80"></ul>
                </div>
            </aside>
        </div>
        <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Sesi presensi</h2>
                    @if ($jadwal)
                        <dl class="mt-4 space-y-3 text-sm">
                            <div><dt class="text-gray-500 dark:text-gray-400">Guru mapel</dt><dd class="mt-1 text-gray-900 dark:text-white">{{ $jadwal->pegawai->name }}</dd></div>
                            <div><dt class="text-gray-500 dark:text-gray-400">Jam pelajaran</dt><dd class="mt-1 text-gray-900 dark:text-white">{{ substr($jadwal->mulai, 0, 5) }} – {{ substr($jadwal->akhir, 0, 5) }}</dd></div>
                        </dl>
                        <label for="face-materi" class="mb-2 mt-4 block text-sm font-medium text-gray-900 dark:text-white">Materi pelajaran <span class="text-red-600">*</span></label>
                        <textarea id="face-materi" data-scan-materi rows="3" maxlength="500" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-3 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Contoh: Latihan materi hari ini"></textarea>
                        <a href="{{ route('absensi.create', ['jadwal_id' => $jadwal->id, 'date' => $date]) }}" class="mt-4 inline-block text-sm font-medium text-blue-700 hover:underline dark:text-blue-400">Lihat logbook / presensi manual →</a>
                    @else
                        <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">Kelas ditentukan dari penempatan siswa pada periode aktif. Tidak perlu memilih kelas satu per satu.</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('face-attendance.index', ['mode' => 'piket', 'type' => 'masuk']) }}" class="rounded-lg border px-3 py-2 text-sm {{ $type === 'masuk' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-500' }}">Masuk</a>
                            <a href="{{ route('face-attendance.index', ['mode' => 'piket', 'type' => 'pulang']) }}" class="rounded-lg border px-3 py-2 text-sm {{ $type === 'pulang' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-300 text-gray-500' }}">Pulang</a>
                        </div>
                        <a href="{{ route('absensi.harian.index', ['date' => $date]) }}" class="mt-4 inline-block text-sm font-medium text-blue-700 hover:underline dark:text-blue-400">Lihat presensi harian →</a>
                    @endif
                </div>
                <details class="self-start rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Panduan pemindaian</summary>
                    <ul class="mt-3 space-y-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        <li>Hadapkan satu wajah ke kamera dengan cahaya yang cukup.</li>
                        <li>Mode otomatis: tunggu nama dan status muncul, lalu siswa keluar dari bingkai. Siswa berikutnya maju setelah tulisan Siap muncul. Siswa yang mengantre harus berada di luar bingkai.</li>
                        <li>Klik Jeda otomatis untuk berhenti sementara. Pemindaian juga berhenti saat berpindah tab atau kamera dimatikan. Gangguan koneksi/sesi akan menjeda otomatis agar guru dapat memeriksa hasil.</li>
                        <li>Pastikan nama hasil pemindaian sesuai dengan siswa di depan guru.</li>
                        <li>Status yang sudah tercatat tetap dipertahankan. Koreksi dilakukan melalui presensi manual.</li>
                    </ul>
                </details>
        </div>
        <details class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <summary class="cursor-pointer text-base font-semibold text-gray-900 dark:text-white">Performa absensi wajah</summary>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Server: <span data-scan-perf-host class="font-medium"></span>. <span data-scan-perf-environment></span></p>
            <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    'camera' => 'Persiapan kamera + izin',
                    'model' => 'Persiapan model',
                    'backend' => 'Mesin deteksi',
                    'status' => 'Status pengukuran terakhir',
                    'detection' => 'Deteksi + pola wajah (perangkat)',
                    'response' => 'Respons server (pulang-pergi)',
                    'server' => 'Proses aplikasi server',
                    'overhead' => 'Jaringan + proses lain (perkiraan)',
                    'total' => 'Total pemindaian terakhir',
                    'average' => 'Rata-rata total sesi ini',
                ] as $metric => $label)
                    <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900">
                        <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd data-scan-perf-{{ $metric }} class="mt-2 font-medium tabular-nums text-gray-900 dark:text-white">—</dd>
                    </div>
                @endforeach
            </dl>
            <p class="mt-4 text-xs leading-5 text-gray-500 dark:text-gray-400">Satuan ms (1.000 ms = 1 detik). Total dihitung sejak klik pindai hingga hasil diterima; tidak termasuk persiapan kamera/model. Respons mencakup kirim data, tunggu server, unduh dan baca hasil. Proses aplikasi mengukur validasi, pencocokan dan pencatatan di dalam controller, termasuk akses database; belum mencakup antrean hosting, bootstrap dan middleware. Selisih bukan ping jaringan murni. Data server tersedia pada respons berhasil; tanda — berarti belum tersedia. Pindai pertama bisa lebih lambat karena pemanasan model. Rata-rata memakai maksimal 20 pindai berhasil terakhir, termasuk siswa yang sudah tercatat; hilang saat halaman dimuat ulang.</p>
            <details class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                <summary class="cursor-pointer font-medium">Membaca hasil dan menentukan perbaikan</summary>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-6">
                    <li>Deteksi paling lama: periksa akselerasi Chrome, tutup aplikasi berat, bandingkan perangkat lain. Jika konsisten, pertimbangkan perangkat dengan CPU/GPU lebih baik.</li>
                    <li>Proses aplikasi server paling lama: periksa query/database dan batas CPU, RAM serta proses bersamaan pada hosting. Pertimbangkan kapasitas hosting lebih tinggi setelah membandingkan beberapa pemindaian pada jam sibuk.</li>
                    <li>Respons lama tetapi proses aplikasi singkat: bandingkan Wi-Fi/koneksi lain, lokasi server, serta antrean hosting. Selisih ini belum membuktikan jaringan atau Hostinger sebagai penyebab.</li>
                    <li>Model lama hanya pada pembukaan pertama: periksa unduhan dan cache aset. Bandingkan pemindaian berikutnya sebelum memutuskan upgrade.</li>
                    <li>Uji di domain produksi untuk menilai Hostinger. Panel ini tidak membaca pemakaian CPU/RAM atau batas paket hosting; cocokkan waktunya dengan metrik hosting sebelum upgrade.</li>
                </ul>
            </details>
        </details>
    </section>
</x-layout.layout>
