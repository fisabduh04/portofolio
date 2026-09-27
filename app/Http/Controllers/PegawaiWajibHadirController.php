<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiWajibHadirRequest;
use App\Models\Jadwal;
use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\PegawaiWajibHadir;
use App\Models\Tahun;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PegawaiWajibHadirController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $activeYear = Tahun::aktif()->orderBy('id')->first();
        if (! $activeYear) {
            return back()->with('type', 'error')->with('message', 'Tahun ajaran aktif tidak ditemukan.');
        }

        return view('attendance.wajib-hadir.index', $this->scheduleState($activeYear) + compact('activeYear'));
    }

    public function store(StorePegawaiWajibHadirRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $activeYear = Tahun::aktif()->orderBy('id')->lockForUpdate()->first();
            if (! $activeYear || $activeYear->id !== (int) $validated['tahun_id']) {
                throw ValidationException::withMessages(['tahun_id' => 'Tahun ajaran aktif berubah. Muat ulang jadwal sebelum menyimpan.']);
            }

            $state = $this->scheduleState($activeYear);
            if (! hash_equals($state['version'], $validated['version'])) {
                throw ValidationException::withMessages(['version' => 'Jadwal atau daftar pegawai telah berubah. Muat ulang halaman agar perubahan terbaru tidak tertimpa.']);
            }

            $manualDays = $validated['manual_days'] ?? [];
            if (array_diff(array_keys($manualDays), $state['pegawais']->modelKeys())) {
                throw ValidationException::withMessages(['manual_days' => 'Daftar pegawai tidak valid. Muat ulang jadwal sebelum menyimpan.']);
            }

            foreach ($state['pegawais'] as $pegawai) {
                $days = $manualDays[$pegawai->id] ?? [];
                foreach (PegawaiWajibHadir::DAYS as $day) {
                    if (isset($state['autoSchedules'][$pegawai->id.'-'.$day])) {
                        $days[] = $day;
                    }
                }
                $days = array_unique($days);

                PegawaiWajibHadir::where('tahun_id', $activeYear->id)->where('pegawai_id', $pegawai->id)
                    ->whereNotIn('hari', $days)->delete();
                foreach ($days as $day) {
                    PegawaiWajibHadir::firstOrCreate([
                        'pegawai_id' => $pegawai->id, 'tahun_id' => $activeYear->id, 'hari' => $day,
                    ]);
                }
            }
        });

        return redirect()->route('attendance.wajib-hadir.index')
            ->with('type', 'success')->with('message', 'Jadwal wajib hadir berhasil disimpan.');
    }

    /** @return array{pegawais: Collection<int, Pegawai>, autoSchedules: array<string, bool>, version: string} */
    private function scheduleState(Tahun $activeYear): array
    {
        $pegawais = Pegawai::whereRaw('LOWER(TRIM(aktif)) = ?', ['aktif'])->orderBy('name')->orderBy('id')
            ->with(['wajibHadirs' => fn ($query) => $query->where('tahun_id', $activeYear->id)->orderBy('id')])->get();
        $autoSchedules = [];
        foreach ([Jadwal::class, JadwalPiket::class] as $model) {
            foreach ($model::where('tahun_id', $activeYear->id)->orderBy('id')->get(['pegawai_id', 'hari']) as $schedule) {
                $autoSchedules[$schedule->pegawai_id.'-'.$schedule->hari] = true;
            }
        }
        ksort($autoSchedules);
        $savedDays = $pegawais->map(fn (Pegawai $pegawai): array => [
            $pegawai->id, $pegawai->wajibHadirs->map(fn (PegawaiWajibHadir $day): array => [$day->id, $day->hari])->all(),
        ])->all();
        $version = hash('sha256', json_encode([$activeYear->id, $savedDays, $autoSchedules], JSON_THROW_ON_ERROR));

        return compact('pegawais', 'autoSchedules', 'version');
    }
}
