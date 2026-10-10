<section data-face-enrollment data-save-url="{{ route('siswa.face.store', $siswa) }}" data-student-name="{{ $siswa->nama }}" data-maximum-sample-distance="{{ config('face-enrollment.maximum_sample_distance') }}" class="space-y-5" aria-labelledby="face-heading">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 id="face-heading" class="text-xl font-semibold text-gray-900 dark:text-white">Perekaman wajah</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ambil tiga posisi wajah {{ $siswa->nama }} dengan pencahayaan yang baik.</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
            <span class="size-1.5 rounded-full bg-amber-500" aria-hidden="true"></span><span data-enrollment-status>{{ $faceSampleCount > 0 ? $faceSampleCount.' sampel terdaftar' : 'Belum terdaftar' }}</span>
        </span>
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 xl:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Kamera siswa</h3>
                <span data-camera-status class="text-xs text-gray-500 dark:text-gray-400">Kamera nonaktif</span>
            </div>
            <div class="p-4 sm:p-5">
                <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="enrollment-camera-facing" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Kamera</label>
                            <select id="enrollment-camera-facing" data-camera-facing aria-describedby="enrollment-settings-help" class="block min-h-11 w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                <option value="user">Kamera depan / webcam</option>
                                <option value="environment">Kamera belakang</option>
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label for="enrollment-detector" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Model deteksi wajah</label>
                            <select id="enrollment-detector" data-face-detector aria-describedby="enrollment-detector-help enrollment-settings-help" class="block min-h-11 w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                <option value="tiny">TinyFaceDetector</option>
                                <option value="ssd" selected>SSD MobileNet V1</option>
                            </select>
                            <p id="enrollment-detector-help" class="mt-1.5 text-xs leading-5 text-gray-500 dark:text-gray-400">Tiny lebih ringan untuk HP. SSD adalah model awal perekaman.</p>
                        </div>
                    </div>
                    <p id="enrollment-settings-help" class="mt-3 border-t border-gray-200 pt-3 text-xs leading-5 text-gray-500 dark:border-gray-700 dark:text-gray-400">Matikan kamera untuk mengganti kamera atau model. Uji dengan posisi dan pencahayaan yang sama. Sampel baru tersimpan setelah Anda menekan Simpan wajah.</p>
                </div>
                <div class="relative aspect-[4/3] overflow-hidden rounded-lg bg-gray-950 sm:aspect-video">
                    <video data-face-video autoplay muted playsinline class="size-full -scale-x-100 object-contain" aria-label="Pratinjau kamera siswa"></video>
                    <div data-camera-placeholder class="absolute inset-0 flex flex-col items-center justify-center gap-3 px-6 text-center">
                        <div class="flex size-16 items-center justify-center rounded-full bg-gray-800 text-gray-300">
                            <svg class="size-8" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7 9.5 4h5L16 7h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3Z"/><circle cx="12" cy="13" r="4"/></svg>
                        </div>
                        <p class="text-base font-semibold text-white">Siap untuk mengambil wajah?</p>
                        <p class="max-w-xs text-sm leading-6 text-gray-400">Aktifkan kamera, lalu izinkan akses kamera pada browser Anda.</p>
                    </div>
                    <div data-face-guide class="pointer-events-none absolute inset-0 hidden items-center justify-center" aria-hidden="true">
                        <div class="h-4/5 w-2/5 rounded-[50%] border-2 border-dashed border-white/70"></div>
                    </div>
                </div>
                <div class="mt-4 flex items-start gap-3 rounded-lg bg-blue-50 p-3 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                    <span data-step-number class="flex size-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold dark:bg-blue-800">1</span>
                    <div>
                        <p data-pose-title class="text-sm font-semibold">Posisi 1: Menghadap depan</p>
                        <p data-pose-help class="mt-1 text-xs leading-5">Tatap kamera, posisikan seluruh wajah di dalam panduan.</p>
                    </div>
                </div>
                <p data-face-message role="status" aria-live="polite" class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">Kamera hanya aktif setelah Anda menekan tombol di bawah.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button data-camera-start type="button" class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Aktifkan kamera</button>
                    <button data-face-capture type="button" disabled class="rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Ambil sampel</button>
                    <button data-camera-stop type="button" disabled class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Matikan kamera</button>
                </div>
            </div>
        </div>

        <aside class="space-y-5">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Siswa yang dipilih</h3>
                <div class="mt-4 flex items-center gap-3">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300" aria-hidden="true">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="4"/><path stroke-linecap="round" d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="break-words text-sm font-semibold text-gray-900 dark:text-white">{{ $siswa->nama }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">NIPD {{ $siswa->nipd ?: '—' }} · {{ $kelasAktif ?? 'Tanpa Rombel' }}</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sebelum mulai</h3>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-gray-600 dark:text-gray-300">
                    <li class="flex gap-3"><span class="font-semibold text-blue-600 dark:text-blue-400">01</span>Pastikan hanya satu siswa berada di depan kamera.</li>
                    <li class="flex gap-3"><span class="font-semibold text-blue-600 dark:text-blue-400">02</span>Hadapkan wajah ke sumber cahaya. Hindari cahaya dari belakang.</li>
                    <li class="flex gap-3"><span class="font-semibold text-blue-600 dark:text-blue-400">03</span>Pastikan wajah terlihat jelas dan kamera sejajar dengan mata.</li>
                </ul>
                <div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">Foto hanya untuk pratinjau. Saat disimpan, pola wajah dienkripsi dan dikaitkan dengan siswa ini. Rekam ulang mengganti seluruh sampel lama; hanya tiga sampel terbaru yang disimpan.</p>
                </div>
            </div>
        </aside>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pratinjau sampel</h3>
            <span data-sample-count class="text-xs font-medium text-gray-500 dark:text-gray-400">0 dari 3 sampel</span>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            @foreach (['Menghadap depan', 'Sedikit ke kiri', 'Sedikit ke kanan'] as $pose)
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="relative aspect-video bg-gray-50 dark:bg-gray-900">
                        <div data-sample-placeholder class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-gray-400 dark:text-gray-500">
                            <span class="flex size-9 items-center justify-center rounded-full border border-dashed border-gray-300 text-sm dark:border-gray-600">{{ $loop->iteration }}</span>
                            <span class="text-xs">Belum diambil</span>
                        </div>
                        <img data-face-sample class="hidden size-full -scale-x-100 object-contain" alt="Sampel wajah: {{ $pose }}">
                    </div>
                    <div class="flex items-center justify-between gap-2 p-3">
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ $loop->iteration }}. {{ $pose }}</span>
                        <button data-retake="{{ $loop->index }}" type="button" disabled class="rounded px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-40 dark:text-blue-400 dark:hover:bg-gray-700" aria-label="Ulangi sampel {{ $pose }}">Ulangi</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
            <p data-save-message role="status" aria-live="polite" class="text-xs leading-5 text-gray-500 dark:text-gray-400">Ambil ketiga posisi, periksa hasilnya, lalu simpan. Pratinjau foto tidak disimpan.</p>
            <button data-face-save type="button" disabled class="shrink-0 rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Simpan wajah</button>
            <button data-face-reset type="button" disabled class="shrink-0 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Hapus pratinjau</button>
        </div>
    </div>
    <form method="POST" action="{{ route('siswa.face.destroy', $siswa) }}" data-face-delete-form data-identity="{{ $siswa->nama }} — {{ $kelasAktif ?? 'Kelas belum ditentukan' }}" class="rounded-lg border border-red-200 p-4 dark:border-red-900">
        @csrf
        @method('DELETE')
        <p class="text-sm font-medium text-gray-900 dark:text-white">Hapus data wajah tersimpan</p>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan jika wajah direkam pada siswa yang salah. Profil siswa dan riwayat absensi tetap tersimpan. Wajah perlu direkam kembali untuk pemindaian berikutnya.</p>
        <label class="mt-3 flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
            <input type="checkbox" name="confirm_delete" value="1" required class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
            <span>Saya ingin menghapus seluruh data wajah {{ $siswa->nama }} ({{ $kelasAktif ?? 'Kelas belum ditentukan' }}).</span>
        </label>
        <button type="submit" data-face-delete class="mt-3 rounded-lg bg-red-700 px-4 py-2 text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-red-900">Hapus data wajah</button>
    </form>
</section>
