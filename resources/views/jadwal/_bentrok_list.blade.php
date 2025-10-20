@props(['bentrokJadwalList'])

<ul class="mt-2 list-disc list-inside">
    @foreach ($bentrokJadwalList->unique('id') as $j)
        <li>
            Guru <strong>{{ $j->pegawai->name ?? 'N/A' }}</strong> bentrok pada Hari <strong>{{ $j->hari }}</strong>, jam
            <strong>{{ \Carbon\Carbon::parse($j->mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->akhir)->format('H:i') }}</strong> di kelas
            <strong>{{ $j->kelas->kelas ?? 'N/A' }}</strong>.
        </li>
    @endforeach
</ul>
<p class="mt-3">Mohon perbaiki jadwal yang bentrok di bawah ini.</p>
