@extends('layouts.app')

@section('content')
<div class="px-4 pt-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 text-white ">
        <div>
            <h1 class="text-2xl font-bold flex gap-2 items-center">
                <span class="p-2 border border-blue-400 bg-blue-400/10 rounded-xl ">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </span>
                Buat Event Khusus
            </h1>
            <p class="text-sm text-gray-400 mt-2">Daftarkan kegiatan sekolah and otomatisasi absensi peserta.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('attendance.events.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <x-form.input label="Nama Kegiatan" name="name" placeholder="Contoh: Rapat Kerja Awal Semester" required />

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form.input label="Tanggal" name="date" type="date" required />
                                <div class="grid grid-cols-2 gap-2">
                                    <x-form.input label="Waktu Mulai" name="start_time" type="time" value="07:00" required />
                                    <x-form.input label="Waktu Selesai" name="end_time" type="time" value="14:00" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form.input label="Uang Saku / Bantuan Hadir" name="bantuan_hadir" type="number" placeholder="Rp 0" value="0" required />
                                <div class="flex items-center h-full pt-6">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_mandatory" value="1" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-300 decoration-blue-500 underline underline-offset-4">Wajib Hadir</span>
                                    </label>
                                </div>
                            </div>

                            <x-form.textarea label="Keterangan Tambahan" name="description" placeholder="Informasi detail mengenai agenda kegiatan..." />

                            <div class="pt-6 border-t border-gray-700">
                                <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Pilih Peserta
                                </h3>
                                
                                <div class="bg-gray-900/50 border border-gray-700 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Daftar Pegawai Aktif</span>
                                        <div class="flex gap-4">
                                            <button type="button" onclick="selectAll()" class="text-xs text-blue-400 hover:text-blue-300 font-medium">Pilih Semua</button>
                                            <button type="button" onclick="deselectAll()" class="text-xs text-gray-500 hover:text-gray-400 font-medium">Reset</button>
                                        </div>
                                    </div>
                                    
                                    <div class="max-h-80 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-3 pr-2 scrollbar-thin scrollbar-thumb-gray-700">
                                        @foreach($pegawais as $p)
                                        <label class="flex items-center p-3 rounded-lg border border-gray-700 hover:bg-gray-700/50 cursor-pointer transition-all group">
                                            <input type="checkbox" name="participants[]" value="{{ $p->id }}" class="participant-checkbox w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-600 focus:ring-offset-gray-800">
                                            <span class="ml-3 text-sm text-gray-300 group-hover:text-white">{{ $p->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-700">
                                <a href="{{ route('attendance.events.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors">Batal</a>
                                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Simpan Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-blue-600/10 border border-blue-500/20 p-6 rounded-2xl">
                <h4 class="text-blue-400 font-bold mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Informasi Sistem
                </h4>
                <div class="space-y-4 text-sm text-gray-400 leading-relaxed">
                    <p>Sistem akan secara otomatis menghitung absensi peserta berdasarkan log fingerprint pada tanggal kegiatan.</p>
                    <p>Jika peserta <span class="text-blue-400 font-semibold">Wajib Hadir</span> namun tidak melakukan scan, sistem akan menandai sebagai <span class="text-red-400 font-semibold text-xs border border-red-900 px-1 bg-red-900/20 rounded">ALPHA</span>.</p>
                    <p>Bantuan hadir akan ditambahkan ke total pendapatan harian pegawai di laporan penggajian.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function selectAll() {
        document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = true);
    }
    function deselectAll() {
        document.querySelectorAll('.participant-checkbox').forEach(cb => cb.checked = false);
    }
</script>
@endsection
