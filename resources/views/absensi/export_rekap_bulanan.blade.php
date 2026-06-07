<table>
    <thead>
        <tr>
            <th colspan="{{ count($dates) + 8 }}" style="text-align: center; font-weight: bold;">LAPORAN PRESENSI BULANAN - {{ $kelas }} - {{ $month }}/{{ $year }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000000;">No</th>
            <th rowspan="2" style="font-weight: bold; border: 1px solid #000000; width: 300px;">Nama Siswa</th>
            <th colspan="{{ count($dates) }}" style="font-weight: bold; border: 1px solid #000000; text-align: center;">Tanggal</th>
            <th colspan="5" style="font-weight: bold; border: 1px solid #000000; text-align: center;">Total</th>
        </tr>
        <tr>
            @foreach($dates as $d)
                <th style="font-weight: bold; border: 1px solid #000000; text-align: center; width: 30px;">{{ $d }}</th>
            @endforeach
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d1fae5;">H</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #fef3c7;">S</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #dbeafe;">I</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #fee2e2;">A</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3e8ff;">P</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rekapData as $index => $student)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $student->nama }}</td>
            @foreach($dates as $d)
                @php
                    $dayData = $student->statuses[$d] ?? ['code' => '-', 'is_libur' => false];
                    $code = $dayData['code'];
                    $color = '#ffffff';
                    if($code == 'H' || $code == 'T') $color = '#d1fae5';
                    elseif($code == 'S') $color = '#fef3c7';
                    elseif($code == 'I') $color = '#dbeafe';
                    elseif($code == 'A') $color = '#fee2e2';
                    elseif($code == 'P') $color = '#f3e8ff';
                @endphp
                <td style="border: 1px solid #000000; text-align: center; background-color: {{ $code !== '-' ? $color : '#ffffff' }};">{{ $code !== '-' ? ($code == 'T' ? 'H' : $code) : '' }}</td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ ($student->stats['H'] ?? 0) + ($student->stats['T'] ?? 0) }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->stats['S'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->stats['I'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->stats['A'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $student->stats['P'] ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
