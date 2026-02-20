<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; font-size: 14px;">{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-size: 11px;">{{ $sekolah->alamat ?? 'Alamat Sekolah' }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; font-weight: bold; text-decoration: underline;">LAPORAN KEHADIRAN PEGAWAI</th>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Nama</td>
            <td colspan="4">: {{ $pegawai->name }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">NIP/NIPPK</td>
            <td colspan="4">: {{ $pegawai->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Jabatan</td>
            <td colspan="4">: {{ $pegawai->role }}</td>
        </tr>
         <tr>
            <td colspan="2" style="font-weight: bold;">Periode</td>
            <td colspan="4">: {{ $months[$month] }} {{ $year }}</td>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">No</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">Tanggal</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">Jam Masuk</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">Jam Pulang</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">Status</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f3f4f6;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dates as $index => $date)
            @php
                $attendance = $attendanceData->firstWhere('date', $date->format('Y-m-d'));
                $isHoliday = \App\Models\HariLibur::isLibur($date->format('Y-m-d'));
                $status = '-';
                $color = '#ffffff';

                if ($attendance) {
                    $status = $attendance->status;
                    if ($status == 'Hadir') $color = '#ffffff';
                    elseif ($status == 'Sakit') $color = '#fef3c7'; // yellow-50
                    elseif ($status == 'Izin') $color = '#dbeafe'; // blue-50
                    elseif ($status == 'Alpha') $color = '#fee2e2'; // red-50
                    elseif ($status == 'Telat') $color = '#ffedd5'; // orange-50
                } elseif ($isHoliday) {
                    $status = 'Libur';
                    $color = '#e5e7eb'; // gray-200
                } else {
                     if ($date->dayOfWeek == 0) { 
                         $status = 'Libur';
                         $color = '#e5e7eb';
                    }
                }
            @endphp
        <tr>
            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $color }}">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; background-color: {{ $color }}">{{ $date->isoFormat('dddd, D MMMM Y') }}</td>
            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $color }}">{{ $attendance->clock_in ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $color }}">{{ $attendance->clock_out ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: {{ $color }}">{{ $status }}</td>
            <td style="border: 1px solid #000000; background-color: {{ $color }}">{{ $attendance->notes ?? ($isHoliday ? 'Hari Libur' : '-') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
