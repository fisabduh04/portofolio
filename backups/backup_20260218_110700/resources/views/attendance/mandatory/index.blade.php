<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Jadwal Wajib', 'href' => '']
    ]" />

    <div class="mb-6">
        <!-- Tab Navigation (Updated to include Mandatory) -->
        <x-attendance.tab-nav active="mandatory" />
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm">
        <div class="p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Atur Jadwal Wajib Pegawai</h2>
            <p class="text-sm text-gray-500 mb-6">
                Tentukan hari-hari wajib hadir untuk setiap pegawai. 
                <br>
                <ul>
                    <li>- <strong>Staff:</strong> Centang semua hari kerja (Senin-Sabtu/Minggu).</li>
                    <li>- <strong>Guru:</strong> Centang hari tambahan selain jadwal mengajar & piket (misal: Rapat di hari Rabu).</li>
                </ul>
            </p>

            <form action="{{ route('attendance.mandatory.store') }}" method="POST">
                @csrf
                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium sticky left-0 bg-neutral-secondary-soft z-10">Pegawai</th>
                                @foreach($days as $day)
                                    <th scope="col" class="px-6 py-3 font-medium text-center">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pegawais as $pegawai)
                                <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                    <td class="px-6 py-4 font-medium text-heading whitespace-nowrap sticky left-0 bg-neutral-primary hover:bg-neutral-secondary-soft/50 z-10">
                                        {{ $pegawai->name }}
                                        <div class="text-xs text-body-subtle font-normal">{{ $pegawai->jabatan ?? 'Pegawai' }}</div>
                                    </td>
                                    @php
                                        // Fetch from relationship (pre-loaded in controller with active year filter)
                                        $manualDays = $pegawai->mandatorySchedules->pluck('hari')->toArray();
                                        $autoDays = $automaticDays[$pegawai->id] ?? [];
                                    @endphp
                                    @foreach($days as $day)
                                        @php
                                           $isAuto = in_array($day, $autoDays);
                                           $isManual = in_array($day, $manualDays);
                                        @endphp
                                        <td class="px-6 py-4 text-center">
                                            @if($isAuto)
                                                <!-- Automatic Day (Jadwal/Piket) - Locked -->
                                                <div class="flex justify-center items-center" title="Jadwal Otomatis (Mengajar/Piket)">
                                                    <div class="w-4 h-4 bg-green-100 border-green-500 border rounded flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- Manual Day - Editable -->
                                                <input type="checkbox" name="wajib_hadir[{{ $pegawai->id }}][]" value="{{ $day }}" 
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                    {{ $isManual ? 'checked' : '' }}>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                     <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout.layout>
