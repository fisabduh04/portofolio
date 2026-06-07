<table>
    <thead>
        <tr>
            <th colspan="18" style="text-align: center; font-weight: bold;">LAPORAN PRESENSI TAHUNAN - {{ $kelas }} - {{ $year }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000000;">No</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000000; width: 300px;">Nama Siswa</th>
            <th colspan="12" style="font-weight: bold; border: 1px solid #000000; text-align: center;">Bulan</th>
            <th colspan="6" style="font-weight: bold; border: 1px solid #000000; text-align: center;">Total Akhir</th>
        </tr>
        <tr>
            @for($m=1; $m<=12; $m++)
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 50px;">{{ \Carbon\Carbon::create(null, $m)->translatedFormat('M') }}</th>
            @endfor
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d1fae5;">H</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #fef3c7;">S</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #dbeafe;">I</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #fee2e2;">A</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3e8ff;">P</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #e0e7ff;">T</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $index => $student)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $student->nama }}</td>
            @for($m=1; $m<=12; $m++)
                @php
                    $stats = $student->months[$m];
                    $hasData = (($stats['H'] ?? 0) + ($stats['S'] ?? 0) + ($stats['I'] ?? 0) + ($stats['A'] ?? 0) + ($stats['P'] ?? 0) + ($stats['T'] ?? 0)) > 0;
                    $text = '';
                    if($hasData) {
                        if($stats['H'] ?? 0) $text .= $stats['H'].'H ';
                        if($stats['S'] ?? 0) $text .= $stats['S'].'S ';
                        if($stats['I'] ?? 0) $text .= $stats['I'].'I ';
                        if($stats['A'] ?? 0) $text .= $stats['A'].'A ';
                        if($stats['P'] ?? 0) $text .= $stats['P'].'P ';
                        if($stats['T'] ?? 0) $text .= $stats['T'].'T';
                    }
                @endphp
                <td style="border: 1px solid #000000; text-align: center; font-size: 10px;">{{ $text ?: '-' }}</td>
            @endfor
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['H'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['S'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['I'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['A'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['P'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->total['T'] ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
