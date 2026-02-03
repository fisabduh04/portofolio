@extends('tampilan.main')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <x-breadcrumb :breadcrumbs="[
                ['name' => 'Home', 'href' => route('dashboard.index')],
                ['name' => 'Laporan Harian', 'href' => '#'],
            ]" />
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Presensi Harian Seluruh Siswa</h1>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-1">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('absensi.rekap') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Kembali ke Laporan
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-6 bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 mb-8">
        <form action="{{ route('absensi.rekap-harian') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Pilih Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
            </div>

            <div>
                <label class="block mb-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Filter Kelas</label>
                <select name="kelas_id" class="bg-gray-50 border border-gray-200 text-sm rounded-xl block w-full p-2.5 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                    <option value="">Semua Kelas</option>
                    @foreach($listKelas as $lk)
                        <option value="{{ $lk->id }}" {{ $kelasId == $lk->id ? 'selected' : '' }}>{{ $lk->kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2 flex items-end">
                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-lg shadow-primary-500/20">
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>

    {{-- Daily Status Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-[10px] text-gray-400 uppercase font-black tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Nama Siswa</th>
                        <th class="px-6 py-4">Kelas</th>
                        <th class="px-6 py-4 text-center">Sesi Terisi</th>
                        <th class="px-6 py-4 text-center">Status Akhir Hari</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rekapData as $data)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $data->nama }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-[10px] font-bold text-gray-500">{{ $data->kelas }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $data->sessions }} Sesi</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($data->status == 'Alpha')
                                <span class="px-3 py-1 bg-rose-100 text-rose-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Alpha</span>
                            @elseif($data->status == 'Sakit')
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Sakit</span>
                            @elseif($data->status == 'Izin')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Izin</span>
                            @elseif($data->status == 'Hadir')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-widest">Hadir</span>
                            @else
                                <span class="text-[10px] text-gray-300 font-bold uppercase tracking-widest italic">Belum Absensi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="#" class="text-primary-600 hover:text-primary-700 text-xs font-bold uppercase tracking-widest">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center text-gray-400 font-bold uppercase tracking-widest">Data tidak tersedia untuk tanggal ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
