<table>
    <thead>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold; font-size: 16px;">
                REKAPITULASI PRESENSI HARIAN - {{ $kelas }}
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center;">Tanggal: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr></tr>
        <tr style="background-color: #cccccc; font-weight: bold;">
            <th style="border: 1px solid #000000; text-align: center;">NO</th>
            <th style="border: 1px solid #000000; text-align: left;">NAMA SISWA</th>
            <th style="border: 1px solid #000000; text-align: center;">H</th>
            <th style="border: 1px solid #000000; text-align: center;">S</th>
            <th style="border: 1px solid #000000; text-align: center;">I</th>
            <th style="border: 1px solid #000000; text-align: center;">A</th>
            <th style="border: 1px solid #000000; text-align: left;">KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $index => $data)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000000;">{{ $data->nama }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data->stats['Hadir'] > 0 ? $data->stats['Hadir'] : '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data->stats['Sakit'] > 0 ? $data->stats['Sakit'] : '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $data->stats['Izin'] > 0 ? $data->stats['Izin'] : '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center; color: red;">{{ $data->stats['Alpha'] > 0 ? $data->stats['Alpha'] : '-' }}</td>
            <td style="border: 1px solid #000000;">
                @php
                    $details = [];
                    // Alpha
                    if (!empty($data->stat_details['Alpha'])) {
                         foreach($data->stat_details['Alpha'] as $m) $details[] = "[ALPHA] $m";
                         // Note: stat_details only has mapel names in the controller logic I added previously? 
                         // Check Controller logic. No, I updated controller to keep details in $details collection.
                         // $stat_details array I made only has mapel names.
                         // BUT $data->details has full objects. I should use $data->details.
                    }
                @endphp
                
                @if($data->details->count() > 0)
                     {{-- Use details collection for rich info --}}
                     @foreach($data->details as $log)
                        @if ($log->status !== 'Hadir')
                            [{{ strtoupper($log->status) }}] {{ $log->mapel }} ({{ $log->guru }})
                            @if(!$loop->last), @endif
                        @endif
                     @endforeach
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
