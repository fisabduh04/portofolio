<table>
    <tr><th>Rekap Presensi Guru — {{ ucfirst($mode) }}</th></tr>
    <tr><td>{{ $startDate }} s.d. {{ $endDate }}</td></tr>
</table>
@include('attendance.report.guru_table')
