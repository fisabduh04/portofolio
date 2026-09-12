<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaliKelasRequest;
use App\Http\Requests\UpdateWaliKelasRequest;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\WaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WaliKelasController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'tahun_id' => ['nullable', 'integer', 'exists:tahuns,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif'])],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', Rule::in([10, 25, 50, 100])],
            'edit' => ['nullable', 'integer'],
        ]);

        $tahun = Tahun::orderByDesc('tanggalmulai')->orderByDesc('id')
            ->get(['id', 'tahun', 'semester', 'isActive']);
        $selectedTahun = $tahun->firstWhere('id', $filters['tahun_id'] ?? $request->old('tahun_id'))
            ?? $tahun->firstWhere('isActive', 1) ?? $tahun->first();
        $kelas = Kelas::orderBy('kelas')->get(['id', 'kelas']);
        $pegawai = Pegawai::orderBy('name')->get(['id', 'name', 'nuptk']);
        $oldRows = $request->old('penugasans', []);
        $inputRows = is_array($oldRows) ? array_values(array_filter($oldRows, 'is_array')) : [];
        $inputRows = $inputRows ?: [[]];
        $editing = isset($filters['edit'])
            ? WaliKelas::with(['kelas:id,kelas', 'pegawai:id,name'])
                ->where('tahun_id', $selectedTahun?->id)->findOrFail($filters['edit'])
            : null;

        $penugasans = WaliKelas::with(['kelas:id,kelas', 'pegawai:id,name,nuptk'])
            ->where('tahun_id', $selectedTahun?->id)
            ->when($filters['kelas_id'] ?? null, fn (Builder $query, int|string $id): Builder => $query->where('kelas_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('is_active', $status === 'aktif'))
            ->when($request->filled('search'), function (Builder $query) use ($filters): void {
                $search = $filters['search'];
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereHas('pegawai', fn (Builder $pegawaiQuery) => $pegawaiQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('kelas', fn (Builder $kelasQuery) => $kelasQuery->where('kelas', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('is_active')->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 10))
            ->appends($request->only(['kelas_id', 'status', 'search', 'per_page']))
            ->appends(['tahun_id' => $selectedTahun?->id]);

        return view('walikelas.index', compact('tahun', 'selectedTahun', 'kelas', 'pegawai', 'penugasans', 'editing', 'inputRows'));
    }

    public function store(StoreWaliKelasRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data): void {
            Tahun::whereKey($data['tahun_id'])->lockForUpdate()->firstOrFail();

            $existing = WaliKelas::where('tahun_id', $data['tahun_id'])
                ->whereIn('kelas_id', array_column($data['penugasans'], 'kelas_id'))
                ->get()->keyBy(fn (WaliKelas $assignment) => $assignment->kelas_id.':'.$assignment->pegawai_id);

            foreach ($data['penugasans'] as $index => $row) {
                if ($existing->has($row['kelas_id'].':'.$row['pegawai_id'])) {
                    throw ValidationException::withMessages([
                        "penugasans.{$index}.pegawai_id" => 'Penugasan ini sudah ada pada periode terpilih. Gunakan Edit untuk mengubah status atau keterangan.',
                    ]);
                }
            }

            $activeClassIds = collect($data['penugasans'])->where('is_active', true)->pluck('kelas_id');
            WaliKelas::where('tahun_id', $data['tahun_id'])->whereIn('kelas_id', $activeClassIds)
                ->where('is_active', true)->update(['is_active' => false]);

            foreach ($data['penugasans'] as $row) {
                WaliKelas::create(['tahun_id' => $data['tahun_id'], ...$row]);
            }
        }, 3);

        return to_route('walikelas.index', ['tahun_id' => $data['tahun_id']])
            ->with('success', 'Penugasan wali kelas berhasil disimpan.');
    }

    public function update(UpdateWaliKelasRequest $request, WaliKelas $walikelas): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($walikelas, $data): void {
            Tahun::whereKey($walikelas->tahun_id)->lockForUpdate()->firstOrFail();
            $assignment = WaliKelas::whereKey($walikelas->id)->lockForUpdate()->firstOrFail();

            if ((bool) $data['is_active']) {
                WaliKelas::where('tahun_id', $assignment->tahun_id)
                    ->where('kelas_id', $assignment->kelas_id)->whereKeyNot($assignment->id)
                    ->where('is_active', true)->update(['is_active' => false]);
            }

            $assignment->update($data);
        }, 3);

        return to_route('walikelas.index', ['tahun_id' => $walikelas->tahun_id])
            ->with('success', 'Penugasan wali kelas berhasil diperbarui.');
    }

    public function destroy(WaliKelas $walikelas): RedirectResponse
    {
        DB::transaction(function () use ($walikelas): void {
            Tahun::whereKey($walikelas->tahun_id)->lockForUpdate()->firstOrFail();
            $walikelas->delete();
        }, 3);

        return to_route('walikelas.index', ['tahun_id' => $walikelas->tahun_id])
            ->with('success', 'Penugasan wali kelas berhasil dihapus.');
    }
}
