<x-layout.layout>
    <x-breadcrumb :breadcrumbs="[
        ['name' => 'Home', 'href' => route('dashboard.index')],
        ['name' => 'Absensi', 'href' => route('attendance.index')],
        ['name' => 'Alokasi Aturan', 'href' => '']
    ]" />

    <div class="mb-6">
        <!-- Tab Navigation (Updated) -->
        <x-attendance.tab-nav active="allocations" />
    </div>

    <!-- Filter Year -->
    <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('attendance.rule-allocations.index') }}" method="GET" class="flex items-center gap-4">
            <label for="tahun_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Tahun Ajaran:</label>
            <select name="tahun_id" id="tahun_id" onchange="this.form.submit()" 
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach($tahuns as $tahun)
                    <option value="{{ $tahun->id }}" {{ ($selectedYear && $selectedYear->id == $tahun->id) ? 'selected' : '' }}>
                        {{ $tahun->tahun }} {{ $tahun->semester }} {{ $tahun->isActive ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-base shadow-sm">
        <div class="p-4 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Alokasi Aturan Per Tahun</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Aturan yang dipilih di sini akan berlaku <strong>Khusus untuk Tahun Ajaran {{ $selectedYear->tahun ?? '' }} {{ $selectedYear->semester ?? '' }}</strong>.
                    </p>
                </div>
            </div>

            <form action="{{ route('attendance.rule-allocations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_id" value="{{ $selectedYear->id ?? '' }}">

                <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                    <table class="w-full text-sm text-left rtl:text-right text-body">
                        <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Pegawai</th>
                                <th scope="col" class="px-6 py-3 font-medium">Jabatan</th>
                                <th scope="col" class="px-6 py-3 font-medium">Aturan Master (Default)</th>
                                <th scope="col" class="px-6 py-3 font-medium">Aturan dialokasikan (Tahun Ini)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pegawais as $pegawai)
                                @php
                                    // Find allocation for selected year
                                    $allocation = $pegawai->ruleAllocations->first(); // Since we filtered in controller
                                    $allocatedRuleId = $allocation ? $allocation->attendance_rule_id : null;
                                @endphp
                                <tr class="bg-neutral-primary border-b border-default last:border-0 hover:bg-neutral-secondary-soft/50 transition-colors duration-200">
                                    <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                        {{ $pegawai->name }}
                                        <div class="text-xs text-body-subtle font-normal">{{ $pegawai->nuptk ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $pegawai->jabatan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $pegawai->attendanceRule->name ?? 'Belum Diatur' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="allocations[{{ $pegawai->id }}]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">-- Gunakan Default --</option>
                                            @foreach($rules as $rule)
                                                <option value="{{ $rule->id }}" {{ ($allocatedRuleId == $rule->id) ? 'selected' : '' }}>
                                                    {{ $rule->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                     <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Simpan Alokasi Tahun {{ $selectedYear->tahun ?? '' }} {{ $selectedYear->semester ?? '' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout.layout>
