<x-layout.layout>
    <x-breadcrumb :title="'Rekap Presensi Guru'" :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Kepegawaian', 'href' => route('attendance.index')],
        ['name' => 'Rekap Presensi Guru', 'href' => route('attendance.rekap-guru')],
    ]" />
    @php
        $totalRecords = array_sum($summary);
        $presentRate = $totalRecords ? round(($summary['Hadir'] + $summary['Telat']) / $totalRecords * 100, 1) : null;
        $periodLabel = \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y');
        if ($startDate !== $endDate) {
            $periodLabel .= ' - '.\Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y');
        }
        $statusColors = [
            'Hadir' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-800',
            'Sakit' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:ring-amber-800',
            'Izin' => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/30 dark:text-sky-300 dark:ring-sky-800',
            'Alpha' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-900/30 dark:text-rose-300 dark:ring-rose-800',
            'Pulang' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-900/30 dark:text-violet-300 dark:ring-violet-800',
            'Telat' => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-900/30 dark:text-orange-300 dark:ring-orange-800',
        ];
        $controlClass = 'block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-xs focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white';
        $labelClass = 'mb-2 block text-xs font-semibold text-slate-600 dark:text-slate-300';
        $modeParams = array_merge($filters, ['date' => $date, 'month' => $month, 'year' => $year, 'start_date' => $startDate, 'end_date' => $endDate]);
    @endphp

    <div class="mt-6 min-w-0 space-y-6">
        <header class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="hidden rounded-2xl bg-blue-600 p-3 text-white shadow-sm sm:block" aria-hidden="true">
                    <svg class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 10h16M5 5h14a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm3 10 2 2 5-5"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400">Laporan kehadiran</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Rekap Presensi Guru</h1>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pantau kehadiran guru dari fingerprint dan presensi manual dalam satu laporan.</p>
                </div>
            </div>
            <a href="{{ route('attendance.create') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Isi Presensi
            </a>
        </header>

        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-900/20 dark:text-rose-300">{{ $errors->first() }}</div>
        @endif

        <section aria-label="Filter rekap" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 dark:border-slate-700">
                <nav class="flex max-w-full gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1 dark:bg-slate-900" aria-label="Jenis rekap guru">
                    @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan', 'periode' => 'Rentang Tanggal'] as $key => $label)
                        <a href="{{ route('attendance.rekap-guru', array_merge($modeParams, ['mode' => $key])) }}" @if ($mode === $key) aria-current="page" @endif class="whitespace-nowrap rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline-2 focus-visible:outline-blue-500 {{ $mode === $key ? 'bg-white text-blue-700 shadow-sm dark:bg-slate-700 dark:text-blue-300' : 'text-slate-500 hover:bg-white/70 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white' }}">{{ $label }}</a>
                    @endforeach
                </nav>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" d="M8 3v4m8-4v4M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z"/></svg>
                    {{ $periodLabel }}
                </span>
            </div>
            <form action="{{ route('attendance.rekap-guru') }}" method="GET" class="grid grid-cols-1 items-end gap-4 p-4 sm:grid-cols-2 sm:p-5 xl:flex xl:flex-wrap">
                <input type="hidden" name="mode" value="{{ $mode }}">
                @if ($mode === 'harian')
                    <div class="xl:w-44"><label for="report-date" class="{{ $labelClass }}">Tanggal</label><input id="report-date" type="date" name="date" value="{{ $date }}" required class="{{ $controlClass }}"></div>
                @elseif ($mode === 'periode')
                    <div class="xl:w-44"><label for="start-date" class="{{ $labelClass }}">Dari tanggal</label><input id="start-date" type="date" name="start_date" value="{{ $startDate }}" required class="{{ $controlClass }}"></div>
                    <div class="xl:w-44"><label for="end-date" class="{{ $labelClass }}">Sampai tanggal</label><input id="end-date" type="date" name="end_date" value="{{ $endDate }}" min="{{ $startDate }}" required class="{{ $controlClass }}"></div>
                @else
                    @if ($mode === 'bulanan')
                        <div class="xl:w-40">
                            <label for="report-month" class="{{ $labelClass }}">Bulan</label>
                            <select id="report-month" name="month" class="{{ $controlClass }}">
                                @foreach (range(1, 12) as $number)
                                    <option value="{{ $number }}" @selected($month === $number)>{{ \Carbon\Carbon::create($year, $number, 1)->locale('id')->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="xl:w-28"><label for="report-year" class="{{ $labelClass }}">Tahun</label><input id="report-year" type="number" name="year" value="{{ $year }}" min="2000" max="2100" required class="{{ $controlClass }}"></div>
                @endif
                <div class="min-w-0 xl:min-w-52 xl:flex-1">
                    <label for="report-teacher" class="{{ $labelClass }}">Nama guru</label>
                    <select id="report-teacher" name="pegawai_id" class="{{ $controlClass }}">
                        <option value="">Semua Guru</option>
                        @foreach ($pegawais as $pegawai)
                            <option value="{{ $pegawai->id }}" @selected(($filters['pegawai_id'] ?? null) == $pegawai->id)>{{ $pegawai->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="xl:w-44">
                    <label for="report-view" class="{{ $labelClass }}">Tampilan laporan</label>
                    <select id="report-view" name="view_mode" class="{{ $controlClass }}">
                        <option value="sederhana" @selected($viewMode === 'sederhana')>Ringkasan</option>
                        <option value="detail" @selected($viewMode === 'detail')>Rincian presensi</option>
                    </select>
                </div>
                <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500 dark:bg-blue-600 dark:hover:bg-blue-500">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16l-6 7v6l-4 2v-8L4 5Z"/></svg>
                    Tampilkan
                </button>
            </form>
        </section>

        <section aria-label="Ringkasan kehadiran" class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
            @foreach ($statuses as $status => $short)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-700 dark:bg-slate-800">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $status }}</span>
                        <span class="inline-flex size-8 items-center justify-center rounded-lg text-xs font-bold ring-1 ring-inset {{ $statusColors[$status] }}">{{ $short }}</span>
                    </div>
                    <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900 tabular-nums dark:text-white">{{ number_format($summary[$status], 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">catatan kehadiran</p>
                </div>
            @endforeach
        </section>

        <section aria-labelledby="report-table-title" class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="flex flex-col gap-4 border-b border-slate-100 p-5 lg:flex-row lg:items-center lg:justify-between dark:border-slate-700">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 id="report-table-title" class="text-base font-bold text-slate-900 dark:text-white">Daftar Presensi Guru</h2>
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-300">{{ $rows->count() }} guru</span>
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">{{ number_format($totalRecords, 0, ',', '.') }} catatan &middot; Kehadiran <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ $presentRate === null ? '-' : $presentRate.'%' }}</strong> &middot; {{ $periodLabel }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('attendance.rekap-guru.export', array_merge($filters, ['format' => 'excel'])) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m-4-4 4 4 4-4M5 16v4h14v-4"/></svg>
                        Ekspor Excel
                    </a>
                    <a href="{{ route('attendance.rekap-guru.export', array_merge($filters, ['format' => 'pdf'])) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8V3h10v5M7 17H4V9h16v8h-3M7 14h10v7H7v-7Z"/></svg>
                        Cetak / PDF
                    </a>
                </div>
            </div>
            <div class="max-h-[640px] overflow-auto" role="region" aria-label="Tabel rekap presensi guru, dapat digeser" tabindex="0">
                @include('attendance.report.guru_table', ['webView' => true])
            </div>
            <div class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-slate-700 dark:bg-slate-900/30">
                <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs">
                    @foreach ($statuses as $label => $short)
                        <span class="inline-flex items-center gap-1.5 text-slate-500 dark:text-slate-400"><span class="inline-flex size-5 items-center justify-center rounded text-[10px] font-bold {{ $statusColors[$label] }}">{{ $short }}</span>{{ $label }}</span>
                    @endforeach
                </div>
                <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">Tanda "-" berarti belum ada catatan, bukan Alpha. Persentase hadir = (Hadir + Telat) / jumlah catatan status &times; 100%. Geser tabel untuk melihat seluruh tanggal.</p>
            </div>
        </section>
    </div>
</x-layout.layout>
