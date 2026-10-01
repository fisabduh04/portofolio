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
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
                <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Pindai siswa</h2>
                    <span data-scan-count class="text-xs text-gray-500 dark:text-gray-400">0 presensi baru</span>
                </div>
                <div class="space-y-4 p-5">
                    <div class="relative aspect-video overflow-hidden rounded-lg bg-gray-950">
                        <video data-scan-video autoplay muted playsinline class="size-full -scale-x-100 object-contain" aria-label="Kamera absensi wajah"></video>
                        <div data-scan-placeholder class="absolute inset-0 flex items-center justify-center px-6 text-center text-sm text-gray-300">Aktifkan kamera untuk memulai absensi.</div>
                    </div>
                    <p data-scan-message role="status" aria-live="polite" class="min-h-12 text-sm leading-6 text-gray-600 dark:text-gray-300">Satu siswa setiap pemindaian. Hasil yang cocok langsung dicatat ke logbook.</p>
                    <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/30" role="status" aria-live="polite">
                        <p data-scan-auto-status class="text-sm font-medium text-blue-800 dark:text-blue-200">Untuk antrean: aktifkan kamera, lalu mulai pemindaian otomatis sekali.</p>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Hasil terakhir</p>
                        <p data-scan-identity class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">Belum ada hasil</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button data-scan-start type="button" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50">Aktifkan kamera</button>
                        <button data-scan-auto type="button" disabled class="rounded-lg bg-green-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 disabled:cursor-not-allowed disabled:opacity-50">Mulai pemindaian otomatis</button>
                        <button data-scan-capture type="button" disabled class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50">Pindai & catat absensi</button>
                        <button data-scan-stop type="button" disabled class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Matikan kamera</button>
                    </div>
                </div>
            </div>
            <aside class="space-y-5">
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
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Panduan pemindaian</h2>
                    <ul class="mt-3 space-y-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        <li>Hadapkan satu wajah ke kamera dengan cahaya yang cukup.</li>
                        <li>Mode otomatis: tunggu nama dan status muncul, lalu siswa keluar dari bingkai. Siswa berikutnya maju setelah tulisan Siap muncul. Siswa yang mengantre harus berada di luar bingkai.</li>
                        <li>Klik Jeda otomatis untuk berhenti sementara. Pemindaian juga berhenti saat berpindah tab atau kamera dimatikan. Gangguan koneksi/sesi akan menjeda otomatis agar guru dapat memeriksa hasil.</li>
                        <li>Pastikan nama hasil pemindaian sesuai dengan siswa di depan guru.</li>
                        <li>Status yang sudah tercatat tetap dipertahankan. Koreksi dilakukan melalui presensi manual.</li>
                    </ul>
                </div>
            </aside>
        </div>
        <details open class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
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
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Hasil sesi ini</h2>
            <p data-scan-empty class="mt-3 text-sm text-gray-500 dark:text-gray-400">Belum ada siswa dipindai.</p>
            <ul data-scan-results class="mt-3 divide-y divide-gray-200 dark:divide-gray-700" aria-live="polite"></ul>
        </div>
    </section>
</x-layout.layout>
