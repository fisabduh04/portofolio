<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Jadwal', 'href' => '#'],
        ['name' => 'Jadwal Libur', 'href' => '#'],
    ]" />

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
               Jadwal Libur
            </h1>
            <p class="mt-1 text-base text-gray-500 dark:text-gray-400">Atur hari libur mingguan dan libur khusus di sini.</p>
        </div>
    </div>

    @if(!$tahun)
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/10 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
            <div class="flex items-center">
                <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="font-medium">Peringatan!</span> Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.
            </div>
        </div>
    @else
        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- 1. Libur Mingguan (Left Column) -->
            <div class="lg:col-span-1">
                <div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-full flex flex-col">
                    <div class="mb-5">
                         <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Libur Mingguan</h3>
                         <p class="text-sm text-gray-500 dark:text-gray-400">
                             Pilih hari yang secara rutin menjadi hari libur untuk seluruh tahun ajaran aktif.
                         </p>
                    </div>
                    
                    <form action="{{ route('hari-libur.updateWeekly') }}" method="POST" class="flex-1 flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="tahun_id" value="{{ $tahun->id }}">

                        <!-- Form Masa Berlaku (Baru) -->
                        <div class="p-4 mb-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                            <h6 class="text-xs font-bold uppercase text-blue-800 dark:text-blue-300 mb-2 tracking-wider">Masa Berlaku (Opsional)</h6>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mb-3 leading-relaxed">
                                Jika diisi, aturan libur ini hanya berlaku dalam rentang tanggal tersebut. Kosongkan untuk berlaku selamanya dalam tahun ajaran ini.
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="weekly_start" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="weekly_start" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                </div>
                                <div>
                                    <label for="weekly_end" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai</label>
                                    <input type="date" name="tanggal_akhir" id="weekly_end" class="bg-white border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3 mb-6">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                                @php
                                    $hariLibur = $liburMingguan->get($day);
                                    $isChecked = $hariLibur ? $hariLibur->is_active : false;
                                    $dateInfo = null;
                                    if ($isChecked && $hariLibur && $hariLibur->tanggal_mulai) {
                                        $start = \Carbon\Carbon::parse($hariLibur->tanggal_mulai)->locale('id')->isoFormat('D MMM Y');
                                        $end = $hariLibur->tanggal_akhir 
                                            ? \Carbon\Carbon::parse($hariLibur->tanggal_akhir)->locale('id')->isoFormat('D MMM Y') 
                                            : 'Seterusnya';
                                        $dateInfo = "($start - $end)";
                                    }
                                @endphp
                                <div class="flex flex-col p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                    <div class="flex items-center">
                                        <input id="day-{{ $day }}" type="checkbox" value="{{ $day }}" name="days[]"
                                            {{ $isChecked ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
                                        <label for="day-{{ $day }}" class="w-full ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                            {{ $day }}
                                        </label>
                                    </div>
                                    @if($dateInfo)
                                        <p class="ml-6 mt-1 text-xs text-blue-600 dark:text-blue-400 font-medium">
                                            Berlaku: {{ $dateInfo }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <x-btn type="submit" color="blue" class="w-full justify-center">
                            Simpan Pengaturan
                        </x-btn>
                    </form>
                </div>
            </div>

            <!-- 2. Libur Khusus (Right Column - Wider) -->
            <div class="lg:col-span-2">
                <div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 h-full">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div>
                             <h3 class="text-lg font-bold text-gray-900 dark:text-white">Libur Khusus</h3>
                             <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar hari libur nasional atau cuti bersama.</p>
                        </div>
                        
                        <!-- Modal Toggle -->
                         <x-btn type="button" color="green" icon="plus" data-modal-target="add-libur-modal" data-modal-toggle="add-libur-modal">
                            Tambah Libur
                        </x-btn>
                    </div>

                    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Keterangan</th>
                                    <th scope="col" class="px-6 py-4">Tanggal / Rentang</th>
                                    <th scope="col" class="px-6 py-4 text-center">Durasi</th>
                                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($liburKhusus as $libur)
                                    @php
                                        $start = \Carbon\Carbon::parse($libur->tanggal_mulai);
                                        $end = $libur->tanggal_akhir ? \Carbon\Carbon::parse($libur->tanggal_akhir) : $start;
                                        $diff = $start->diffInDays($end) + 1;
                                        $isRange = $diff > 1;
                                    @endphp
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $libur->keterangan }}
                                        </th>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                @if($isRange)
                                                    <div class="flex flex-col">
                                                        <span class="font-medium text-gray-900 dark:text-white">{{ $start->translatedFormat('d M Y') }}</span>
                                                        <span class="text-xs text-gray-500">s/d {{ $end->translatedFormat('d M Y') }}</span>
                                                    </div>
                                                @else
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $start->translatedFormat('d F Y') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                {{ $diff }} Hari
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <x-btn type="button" color="yellow" size="xs" icon="pencil" 
                                                    data-modal-target="edit-libur-modal" 
                                                    data-modal-toggle="edit-libur-modal"
                                                    onclick="editLibur('{{ $libur->id }}', '{{ $libur->keterangan }}', '{{ \Carbon\Carbon::parse($libur->tanggal_mulai)->format('Y-m-d') }}', '{{ $libur->tanggal_akhir ? \Carbon\Carbon::parse($libur->tanggal_akhir)->format('Y-m-d') : '' }}')" />
                                                    
                                                <x-btn type="button" color="red" size="xs" icon="trash" data-modal-target="popup-modal-{{ $libur->id }}" data-modal-toggle="popup-modal-{{ $libur->id }}" />
                                                <x-modal.hapus :id="$libur->id" :action="route('hari-libur.destroy', $libur->id)" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-base font-medium">Belum ada data libur khusus.</p>
                                                <p class="text-sm mt-1">Tambahkan hari libur nasional atau cuti bersama menggunakan tombol di atas.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main modal (ADD) -->
    <div id="add-libur-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white items-center flex gap-2">
                        <svg class="w-5 h-5 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Hari Libur
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-libur-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form class="space-y-5" action="{{ route('hari-libur.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tahun_id" value="{{ $tahun->id ?? '' }}">
                        
                        <div>
                            <x-form.input label="Keterangan Libur" name="keterangan" id="keterangan" placeholder="Contoh: Libur Nasional / Cuti Bersama" required />
                        </div>

                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Jenis Libur</label>
                                <div class="flex items-center">
                                    <input id="is-range" type="checkbox" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" onchange="toggleRange(this)">
                                    <label for="is-range" class="ms-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">Rentang Tanggal (> 1 hari)</label>
                                </div>
                            </div>
    
                            <div class="grid grid-cols-1 gap-4" id="date-inputs">
                               <div>
                                    <label for="tanggal_mulai" class="block mb-1.5 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required />
                               </div>
                               <div id="end-date-container" class="hidden">
                                     <label for="tanggal_akhir" class="block mb-1.5 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                     <p class="mt-1 text-xs text-gray-400">Sampai dengan tanggal ini (inklusif).</p>
                               </div>
                            </div>
                        </div>
                        
                        <x-btn type="submit" color="blue" class="w-full justify-center" icon="save">
                            Simpan Libur
                        </x-btn>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit modal -->
    <div id="edit-libur-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
             <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-600">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Hari Libur
                    </h3>
                    <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-libur-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <form id="edit-libur-form" class="space-y-5" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="tahun_id" value="{{ $tahun->id ?? '' }}">
                        
                        <div>
                             <x-form.input label="Keterangan Libur" name="keterangan" id="edit_keterangan" placeholder="Contoh: Libur Nasional" required />
                        </div>

                         <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Jenis Libur</label>
                                <div class="flex items-center">
                                    <input id="edit-is-range" type="checkbox" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" onchange="toggleEditRange(this)">
                                    <label for="edit-is-range" class="ms-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">Rentang Tanggal (> 1 hari)</label>
                                </div>
                            </div>
    
                            <div class="grid grid-cols-1 gap-4">
                               <div>
                                    <label for="edit_tanggal_mulai" class="block mb-1.5 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required />
                               </div>
                               <div id="edit-end-date-container" class="hidden">
                                    <label for="edit_tanggal_akhir" class="block mb-1.5 text-xs font-medium text-gray-500 uppercase dark:text-gray-400">Tanggal Akhir</label>
                                    <input type="date" name="tanggal_akhir" id="edit_tanggal_akhir" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" />
                                    <p class="mt-1 text-xs text-gray-400">Sampai dengan tanggal ini (inklusif).</p>
                               </div>
                            </div>
                        </div>

                         <x-btn type="submit" color="blue" class="w-full justify-center" icon="save">
                            Simpan Perubahan
                        </x-btn>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Define functions in global scope
        function toggleRange(checkbox) {
            const endContainer = document.getElementById('end-date-container');
            const mulInput = document.getElementById('tanggal_mulai');
            const endInput = document.getElementById('tanggal_akhir');
            
            if (checkbox.checked) {
                endContainer.classList.remove('hidden');
                // Optional: set min date for end date to be same as start date
                if(mulInput.value) endInput.min = mulInput.value;
            } else {
                endContainer.classList.add('hidden');
                endInput.value = '';
            }
        }

        function toggleEditRange(checkbox) {
            const endContainer = document.getElementById('edit-end-date-container');
            const mulInput = document.getElementById('edit_tanggal_mulai');
            const endInput = document.getElementById('edit_tanggal_akhir');
            
            if (checkbox.checked) {
                endContainer.classList.remove('hidden');
                if(mulInput.value) endInput.min = mulInput.value;
            } else {
                endContainer.classList.add('hidden');
                endInput.value = '';
            }
        }

        function editLibur(id, keterangan, mulai, akhir) {
            // Set Action URL
            const form = document.getElementById('edit-libur-form');
            form.action = `/hari-libur/${id}`;

            // Set Values
            document.getElementById('edit_keterangan').value = keterangan;
            document.getElementById('edit_tanggal_mulai').value = mulai;
            // Handle null akhir
            const akhirVal = (akhir && akhir !== 'null' && akhir !== mulai) ? akhir : '';
            document.getElementById('edit_tanggal_akhir').value = akhirVal;

            // Handle Range Checkbox
            // Check if akhir exists AND is not same as mulai
            const isRange = (akhirVal !== '');
            const checkbox = document.getElementById('edit-is-range');
            
            checkbox.checked = isRange;
            
            // Force trigger change event or manually call toggle
            toggleEditRange(checkbox);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to start date inputs to update min of end date
            const startInput = document.getElementById('tanggal_mulai');
            if(startInput) {
                startInput.addEventListener('change', function() {
                    document.getElementById('tanggal_akhir').min = this.value;
                });
            }

            const editStartInput = document.getElementById('edit_tanggal_mulai');
            if(editStartInput) {
                editStartInput.addEventListener('change', function() {
                    document.getElementById('edit_tanggal_akhir').min = this.value;
                });
            }
        });
    </script>

</x-layout.layout>