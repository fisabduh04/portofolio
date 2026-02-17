@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="mb-4">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Tambah Jadwal Khusus</h1>
    </div>
    
    <form action="{{ route('attendance.overrides.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="pegawai_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pegawai</label>
                <select name="pegawai_id" id="pegawai_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="">Pilih Pegawai</option>
                    @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->id }}">{{ $pegawai->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal</label>
                <input type="date" name="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="attendance_rule_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rule / Aturan yang Berlaku</label>
                <select name="attendance_rule_id" id="attendance_rule_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="">Pilih Aturan</option>
                    @foreach($rules as $rule)
                        <option value="{{ $rule->id }}">{{ $rule->name }} ({{ $rule->jam_masuk }} - {{ $rule->jam_pulang }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan / Catatan</label>
                <input type="text" name="reason" id="reason" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Simpan</button>
            <a href="{{ route('attendance.overrides.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 ml-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Batal</a>
        </div>
    </form>
</div>
@endsection
