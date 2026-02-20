@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="mb-4">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Tambah Event Baru</h1>
    </div>
    
    <form action="{{ route('attendance.events.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Event</label>
                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal</label>
                <input type="date" name="date" id="date" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jam Mulai</label>
                <input type="time" name="start_time" id="start_time" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="end_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jam Selesai</label>
                <input type="time" name="end_time" id="end_time" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
            </div>
            <div>
                <label for="bantuan_hadir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bantuan Transport/Hadir (Rp)</label>
                <input type="number" name="bantuan_hadir" id="bantuan_hadir" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
            </div>
            <div class="flex items-center pt-6">
                <input id="is_mandatory" name="is_mandatory" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="is_mandatory" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Wajib Hadir (Absen Alpha jika tidak hadir)</label>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Peserta Event</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="col-span-full mb-2">
                    <button type="button" onclick="document.querySelectorAll('.participant-checkbox').forEach(c => c.checked = true)" class="text-xs text-blue-600 hover:underline">Select All</button>
                    |
                    <button type="button" onclick="document.querySelectorAll('.participant-checkbox').forEach(c => c.checked = false)" class="text-xs text-blue-600 hover:underline">Deselect All</button>
                </div>
                @foreach($pegawais as $pegawai)
                <div class="flex items-center">
                    <input id="p_{{ $pegawai->id }}" name="participants[]" type="checkbox" value="{{ $pegawai->id }}" class="participant-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="p_{{ $pegawai->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 truncate">{{ $pegawai->name }}</label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Simpan Event</button>
            <a href="{{ route('attendance.events.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 ml-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Batal</a>
        </div>
    </form>
</div>
@endsection
