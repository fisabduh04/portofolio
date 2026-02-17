@extends('layouts.app')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Jadwal Khusus (Override)</h1>
            <p class="text-sm text-gray-500">Atur jadwal/rule khusus untuk pegawai tertentu pada tanggal tertentu (Manual Exception).</p>
        </div>
        <div class="sm:flex">
            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <a href="{{ route('attendance.overrides.create') }}" class="inline-flex items-center justify-center w-half px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Tambah Jadwal Khusus
                </a>
            </div>
        </div>
    </div>
</div>

<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Pegawai</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Rule yang Berlaku</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Alasan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($overrides as $override)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400">
                                <div class="text-base font-semibold text-gray-900 dark:text-white">{{ $override->pegawai->name }}</div>
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($override->date)->isoFormat('D MMMM Y') }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $override->attendanceRule->name }}
                                </span>
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 dark:text-gray-400">
                                {{ $override->reason ?? '-' }}
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <form action="{{ route('attendance.overrides.destroy', $override->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jadwal khusus ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="px-4 py-2">
    {{ $overrides->links() }}
</div>
@endsection
