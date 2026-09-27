@php
    $webView = $webView ?? false;
    $heading = 'border-b border-slate-200 px-4 py-4 text-xs font-semibold text-slate-500 dark:border-slate-700 dark:text-slate-300';
    $cell = 'border-b border-slate-100 px-4 py-3.5 dark:border-slate-700/60';
    $nameCell = $webView ? 'sticky left-0 z-10 min-w-44 bg-white font-semibold text-slate-800 group-hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-100 dark:group-hover:bg-slate-700' : 'font-medium';
@endphp
<table class="w-full border-separate border-spacing-0 text-left text-sm text-slate-600 dark:text-slate-300">
    @if ($webView)<caption class="sr-only">Rekap presensi guru {{ $startDate }} sampai {{ $endDate }}</caption>@endif
    <thead class="sticky top-0 z-20 bg-slate-50 dark:bg-slate-900">
        <tr>
            <th scope="col" class="{{ $heading }} w-12 text-center">No</th>
            <th scope="col" class="{{ $heading }} {{ $webView ? 'sticky left-0 z-30 min-w-44 bg-slate-50 dark:bg-slate-900' : '' }}">Nama Guru</th>
            @if ($viewMode === 'detail' || $mode === 'harian')
                <th scope="col" class="{{ $heading }} whitespace-nowrap">Tanggal</th>
                <th scope="col" class="{{ $heading }}">Status</th>
                <th scope="col" class="{{ $heading }}">Sumber</th>
                <th scope="col" class="{{ $heading }} min-w-48">Keterangan</th>
            @else
                @if ($mode === 'bulanan')
                    @foreach ($dates as $day)
                        <th scope="col" class="{{ $heading }} min-w-10 text-center" title="{{ $day }}">{{ (int) substr($day, 8, 2) }}</th>
                    @endforeach
                @endif
                @foreach ($statuses as $label => $short)
                    <th scope="col" class="{{ $heading }} text-center" title="{{ $label }}">{{ $short }}</th>
                @endforeach
                <th scope="col" class="{{ $heading }} text-center">Total</th>
                <th scope="col" class="{{ $heading }} whitespace-nowrap text-center">% Hadir</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $row)
            @if ($viewMode === 'detail' || $mode === 'harian')
                @forelse ($row['attendance']->sortKeys() as $record)
                    <tr class="group transition-colors hover:bg-slate-50 dark:hover:bg-slate-700">
                        <td class="{{ $cell }} text-center text-slate-400 tabular-nums">{{ $loop->parent->iteration }}</td>
                        <td data-type="s" class="{{ $cell }} {{ $nameCell }} whitespace-nowrap">{{ $row['pegawai']->name }}</td>
                        <td class="{{ $cell }} whitespace-nowrap tabular-nums">{{ $record->tanggal->format('d-m-Y') }}</td>
                        <td class="{{ $cell }}">
                            @if ($webView)
                                <span class="inline-flex items-center whitespace-nowrap rounded-lg px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusColors[$record->presensiStatus() ?? ''] ?? 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600' }}">{{ $record->status ?? '-' }}</span>
                            @else
                                {{ $record->status ?? '-' }}
                            @endif
                        </td>
                        <td class="{{ $cell }} whitespace-nowrap text-xs">{{ filled($record->attendance_source) ? $record->attendance_source : '-' }}</td>
                        <td data-type="s" class="{{ $cell }} max-w-sm break-words text-xs leading-relaxed">{{ filled($record->keterangan) ? $record->keterangan : '-' }}</td>
                    </tr>
                @empty
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700">
                        <td class="{{ $cell }} text-center text-slate-400">{{ $loop->iteration }}</td>
                        <td data-type="s" class="{{ $cell }} {{ $nameCell }} whitespace-nowrap">{{ $row['pegawai']->name }}</td>
                        @foreach (range(1, 4) as $column)
                            <td class="{{ $cell }} text-xs text-slate-400" title="Belum ada catatan presensi">-</td>
                        @endforeach
                    </tr>
                @endforelse
            @else
                <tr class="group transition-colors hover:bg-slate-50 dark:hover:bg-slate-700">
                    <td class="{{ $cell }} text-center text-slate-400 tabular-nums">{{ $loop->iteration }}</td>
                    <td data-type="s" class="{{ $cell }} {{ $nameCell }} whitespace-nowrap">{{ $row['pegawai']->name }}</td>
                    @if ($mode === 'bulanan')
                        @foreach ($dates as $day)
                            @php($record = $row['attendance']->get($day))
                            <td class="border-b border-slate-100 p-1.5 text-center dark:border-slate-700/60" title="{{ $day }}: {{ $record?->status ?? 'Belum ada catatan' }}{{ $record?->keterangan ? ' - '.$record->keterangan : '' }}">
                                @if ($webView && $record?->presensiStatus())
                                    <span class="inline-flex size-7 items-center justify-center rounded-lg text-[11px] font-bold {{ $statusColors[$record->presensiStatus()] }}">{{ $statuses[$record->presensiStatus()] }}</span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">{{ $statuses[$record?->presensiStatus() ?? ''] ?? '-' }}</span>
                                @endif
                            </td>
                        @endforeach
                    @endif
                    @foreach ($statuses as $status => $short)
                        <td class="{{ $cell }} text-center tabular-nums {{ $row['counts'][$status] > 0 ? 'font-semibold text-slate-800 dark:text-slate-100' : 'text-slate-300 dark:text-slate-600' }}">{{ $row['counts'][$status] }}</td>
                    @endforeach
                    <td class="{{ $cell }} text-center font-bold text-slate-900 tabular-nums dark:text-white">{{ $row['total'] }}</td>
                    <td class="{{ $cell }} text-center font-semibold tabular-nums">{{ $row['total'] ? round(($row['counts']['Hadir'] + $row['counts']['Telat']) / $row['total'] * 100, 1) : 0 }}%</td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="{{ ($viewMode === 'detail' || $mode === 'harian') ? 6 : 10 + ($mode === 'bulanan' ? $dates->count() : 0) }}" class="px-6 py-16 text-center">
                    @if ($webView)
                        <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-700">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 10h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Zm4 10h6"/></svg>
                        </div>
                    @endif
                    <p class="font-semibold text-slate-700 dark:text-slate-200">Tidak ada data guru untuk filter ini.</p>
                    @if ($webView)<p class="mt-2 text-xs text-slate-400">Pilih guru atau periode lain untuk melihat rekap presensi.</p>@endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
