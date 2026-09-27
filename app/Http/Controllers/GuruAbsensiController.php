<?php

namespace App\Http\Controllers;

use App\Exports\GuruAbsensiExport;
use App\Http\Requests\StoreGuruAbsensiRequest;
use App\Models\Pegawai;
use App\Models\PegawaiAbsensi;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuruAbsensiController extends Controller
{
    public function create(Request $request): View
    {
        $validated = $request->validate(['date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today']]);
        $date = $validated['date'] ?? now()->toDateString();
        $pegawais = Pegawai::guruAktif()->wajibHadirPada($date)->orderBy('name')->orderBy('id')->get();
        $attendance = PegawaiAbsensi::whereDate('tanggal', $date)->whereIn('pegawai_id', $pegawais->modelKeys())->get()->keyBy('pegawai_id');

        return view('attendance.create', [
            'date' => $date,
            'pegawais' => $pegawais,
            'attendance' => $attendance,
            'statuses' => PegawaiAbsensi::MANUAL_STATUSES,
        ]);
    }

    public function store(StoreGuruAbsensiRequest $request, AttendanceService $service): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request, $service): void {
            $pegawais = Pegawai::guruAktif()->wajibHadirPada($validated['tanggal'])->whereIn('id', array_column($validated['attendance'], 'pegawai_id'))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            if ($pegawais->count() !== count($validated['attendance'])) {
                throw ValidationException::withMessages(['attendance' => 'Jadwal wajib hadir berubah. Muat ulang daftar guru pada tanggal ini.']);
            }

            foreach ($validated['attendance'] as $row) {
                $service->recordManualAttendance($pegawais->get($row['pegawai_id']), $validated['tanggal'], $row['status'], $row['keterangan'] ?? null, $request->user()->id);
            }
        });

        return redirect()->route('attendance.create', ['date' => $validated['tanggal']])
            ->with('type', 'success')->with('message', 'Presensi manual guru berhasil disimpan.');
    }

    public function report(Request $request): View
    {
        return view('attendance.rekap_guru', $this->reportData($request));
    }

    public function export(Request $request): View|BinaryFileResponse
    {
        $data = $this->reportData($request);

        if (($data['filters']['format'] ?? 'excel') === 'pdf') {
            return view('attendance.print_rekap_guru', $data);
        }

        return Excel::download(new GuruAbsensiExport($data), 'rekap_guru_'.$data['mode'].'_'.$data['startDate'].'.xlsx');
    }

    /** @return array<string, mixed> */
    private function reportData(Request $request): array
    {
        $filters = $request->validate([
            'mode' => ['nullable', Rule::in(['harian', 'bulanan', 'tahunan', 'periode'])],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'start_date' => ['required_if:mode,periode', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:mode,periode', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'pegawai_id' => ['nullable', 'integer', 'exists:pegawais,id'],
            'view_mode' => ['nullable', Rule::in(['sederhana', 'detail'])],
            'format' => ['nullable', Rule::in(['excel', 'pdf'])],
        ]);

        $mode = $filters['mode'] ?? 'bulanan';
        $month = (int) ($filters['month'] ?? now()->month);
        $year = (int) ($filters['year'] ?? now()->year);
        $date = $filters['date'] ?? now()->toDateString();
        [$start, $end] = match ($mode) {
            'harian' => [Carbon::parse($date), Carbon::parse($date)],
            'tahunan' => [Carbon::create($year, 1, 1), Carbon::create($year, 12, 31)],
            'periode' => [Carbon::parse($filters['start_date']), Carbon::parse($filters['end_date'])],
            default => [Carbon::create($year, $month, 1), Carbon::create($year, $month, 1)->endOfMonth()],
        };
        if ($start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) > 365) {
            throw ValidationException::withMessages(['end_date' => 'Rentang rekap maksimal 366 hari.']);
        }

        $startDate = $start->toDateString();
        $endDate = $end->toDateString();
        $afterEndDate = $end->copy()->addDay()->toDateString();
        $inPeriod = fn (Builder|Relation $query): Builder|Relation => $query->where('tanggal', '>=', $startDate)->where('tanggal', '<', $afterEndDate);
        $pegawais = Pegawai::guru()->where(fn (Builder $query): Builder => $query->whereRaw('LOWER(TRIM(aktif)) = ?', ['aktif'])->orWhereHas('pegawaiAbsensis', $inPeriod))
            ->orderBy('name')->orderBy('id')->get(['id', 'name']);

        $teachers = Pegawai::whereIn('id', $pegawais->modelKeys())
            ->when($filters['pegawai_id'] ?? null, fn (Builder $query, int $id): Builder => $query->whereKey($id))
            ->with(['pegawaiAbsensis' => $inPeriod])->orderBy('name')->orderBy('id')->get(['id', 'name']);

        $statuses = PegawaiAbsensi::MANUAL_STATUSES;
        $summary = array_fill_keys(array_keys($statuses), 0);
        $rows = $teachers->map(function (Pegawai $pegawai) use (&$summary, $statuses): array {
            $counts = array_fill_keys(array_keys($statuses), 0);
            foreach ($pegawai->pegawaiAbsensis as $attendance) {
                $status = $attendance->presensiStatus();
                if ($status !== null) {
                    $counts[$status]++;
                    $summary[$status]++;
                }
            }

            return [
                'pegawai' => $pegawai,
                'attendance' => $pegawai->pegawaiAbsensis->keyBy(fn (PegawaiAbsensi $attendance): string => $attendance->tanggal->toDateString()),
                'counts' => $counts,
                'total' => array_sum($counts),
            ];
        });
        $dates = collect(CarbonPeriod::create($start->startOfDay(), $end->startOfDay()))->map(fn (Carbon $day): string => $day->toDateString());

        return compact('filters', 'mode', 'month', 'year', 'date', 'startDate', 'endDate', 'pegawais', 'statuses', 'summary', 'rows', 'dates') + [
            'viewMode' => $filters['view_mode'] ?? 'sederhana',
        ];
    }
}
