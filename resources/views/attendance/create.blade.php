<x-layout.layout>
    <x-breadcrumb :title="'Presensi Manual Guru'" :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Kepegawaian', 'href' => route('attendance.index')],
        ['name' => 'Presensi Manual Guru', 'href' => route('attendance.create')],
    ]" />

    <div class="mt-4 space-y-6">
        <div class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:flex-row md:items-end md:justify-between">
            <form action="{{ route('attendance.create') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="date" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal presensi</label>
                    <input id="date" name="date" type="date" value="{{ $date }}" max="{{ now()->toDateString() }}" required class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <button class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800">Tampilkan</button>
            </form>
            <a href="{{ route('attendance.rekap-guru', ['mode' => 'harian', 'date' => $date]) }}" class="text-sm font-semibold text-blue-700 hover:underline dark:text-blue-400">Lihat Rekap Presensi Guru &rarr;</a>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-gray-800 dark:text-red-400">
                <p class="font-semibold">Presensi belum disimpan. Periksa isian berikut:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attendance.store') }}" method="POST" id="guru-attendance-form" class="space-y-5">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $date }}">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Kehadiran Guru</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }} &middot; {{ $pegawais->count() }} guru wajib hadir</p>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Sesuai centang manual, jadwal mengajar, dan piket pada tahun ajaran aktif di <a href="{{ route('attendance.wajib-hadir.index') }}" class="text-blue-700 hover:underline dark:text-blue-400">Jadwal Wajib Hadir</a>.</p>
                    </div>
                    <button type="button" id="all-teachers-present" @disabled($pegawais->isEmpty()) class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 disabled:opacity-50 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Semua Hadir</button>
                </div>
                <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-300" aria-live="polite">
                    @foreach ($statuses as $label => $short)
                        <span>{{ $short }}: {{ $label }} (<span data-status-count="{{ $label }}">0</span>)</span>
                    @endforeach
                </div>
                @if ($attendance->isNotEmpty())
                    <p class="mt-4 rounded-lg bg-blue-50 p-3 text-sm text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Data tanggal ini sudah tersedia. Perubahan akan memperbarui presensi guru yang bersangkutan.</p>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead class="hidden bg-gray-50 text-xs uppercase dark:bg-gray-700 md:table-header-group">
                        <tr><th class="p-4">No</th><th class="p-4">Nama Guru</th><th class="p-4">Status Kehadiran</th><th class="p-4">Keterangan</th></tr>
                    </thead>
                    <tbody class="block divide-y divide-gray-100 dark:divide-gray-700 md:table-row-group">
                        @forelse ($pegawais as $pegawai)
                            @php
                                $existing = $attendance->get($pegawai->id);
                                $status = old('attendance.'.$pegawai->id.'.status', $existing?->presensiStatus());
                                $note = old('attendance.'.$pegawai->id.'.keterangan', $existing?->keterangan);
                            @endphp
                            <tr class="block p-4 md:table-row md:p-0">
                                <td class="hidden p-4 text-gray-500 md:table-cell">{{ $loop->iteration }}</td>
                                <td class="block py-2 font-semibold text-gray-900 dark:text-white md:table-cell md:p-4">
                                    {{ $pegawai->name }}
                                    <input type="hidden" name="attendance[{{ $pegawai->id }}][pegawai_id]" value="{{ $pegawai->id }}">
                                </td>
                                <td class="block py-2 md:table-cell md:p-4">
                                    <fieldset class="flex flex-wrap gap-2">
                                        <legend class="sr-only">Kehadiran {{ $pegawai->name }}</legend>
                                        @foreach ($statuses as $label => $short)
                                            <label class="cursor-pointer" title="{{ $label }}">
                                                <input type="radio" name="attendance[{{ $pegawai->id }}][status]" value="{{ $label }}" @checked($status === $label) required class="peer sr-only">
                                                <span @class([
                                                    'flex h-10 w-10 items-center justify-center rounded-xl border-2 border-gray-200 text-xs font-bold text-gray-500 transition peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500 peer-checked:text-white dark:border-gray-600 dark:text-gray-300',
                                                    'peer-checked:border-emerald-500 peer-checked:bg-emerald-500' => $label === 'Hadir',
                                                    'peer-checked:border-amber-500 peer-checked:bg-amber-500' => $label === 'Sakit',
                                                    'peer-checked:border-blue-500 peer-checked:bg-blue-500' => $label === 'Izin',
                                                    'peer-checked:border-rose-500 peer-checked:bg-rose-500' => $label === 'Alpha',
                                                    'peer-checked:border-purple-500 peer-checked:bg-purple-500' => $label === 'Pulang',
                                                    'peer-checked:border-indigo-500 peer-checked:bg-indigo-500' => $label === 'Telat',
                                                ])><span aria-hidden="true">{{ $short }}</span><span class="sr-only">{{ $label }}</span></span>
                                            </label>
                                        @endforeach
                                    </fieldset>
                                </td>
                                <td class="block py-2 md:table-cell md:p-4">
                                    <label for="note-{{ $pegawai->id }}" class="sr-only">Keterangan {{ $pegawai->name }}</label>
                                    <input id="note-{{ $pegawai->id }}" type="text" name="attendance[{{ $pegawai->id }}][keterangan]" value="{{ $note }}" maxlength="1000" placeholder="Keterangan (opsional)" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400">Tidak ada guru yang wajib hadir pada hari ini. Periksa Jadwal Wajib Hadir dan tahun ajaran aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end">
                <button type="submit" @disabled($pegawais->isEmpty()) class="rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">Simpan Presensi Guru</button>
            </div>
        </form>
    </div>
    <script>
        const guruAttendanceForm = document.getElementById('guru-attendance-form');
        function updateGuruAttendanceCounts() {
            document.querySelectorAll('[data-status-count]').forEach(counter => {
                counter.textContent = Array.from(guruAttendanceForm.querySelectorAll('input[type="radio"]:checked'))
                    .filter(input => input.value === counter.dataset.statusCount).length;
            });
        }
        guruAttendanceForm.addEventListener('change', updateGuruAttendanceCounts);
        document.getElementById('all-teachers-present').addEventListener('click', () => {
            guruAttendanceForm.querySelectorAll('input[type="radio"][value="Hadir"]').forEach(input => input.checked = true);
            updateGuruAttendanceCounts();
        });
        updateGuruAttendanceCounts();
    </script>
</x-layout.layout>
