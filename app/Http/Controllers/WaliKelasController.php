<?php

namespace App\Http\Controllers;

use App\Exports\WaliKelasExport;
use App\Http\Requests\ImportWaliKelasRequest;
use App\Http\Requests\StoreWaliKelasRequest;
use App\Http\Requests\UpdateWaliKelasRequest;
use App\Imports\WaliKelasImport;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\Tahun;
use App\Models\WaliKelas;
use App\Services\SaveWaliKelasAssignments;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WaliKelasController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $tahun = Tahun::orderByDesc('tanggalmulai')->orderByDesc('id')
            ->get(['id', 'tahun', 'semester', 'isActive']);
        $selectedTahun = $tahun->firstWhere('id', $filters['tahun_id'] ?? $request->old('tahun_id'))
            ?? $tahun->firstWhere('isActive', 1) ?? $tahun->first();
        $kelas = Kelas::orderBy('kelas')->get(['id', 'kelas']);
        $jurusan = Jurusan::orderBy('jurusan')->get(['id', 'jurusan']);
        $pegawai = Pegawai::orderBy('name')->get(['id', 'name', 'nuptk']);
        $oldRows = $request->old('penugasans', []);
        $inputRows = is_array($oldRows) ? array_values(array_filter($oldRows, 'is_array')) : [];
        $inputRows = $inputRows ?: [[]];
        $editing = isset($filters['edit'])
            ? WaliKelas::with(['kelas:id,kelas', 'pegawai:id,name'])
                ->where('tahun_id', $selectedTahun?->id)->findOrFail($filters['edit'])
            : null;

        $penugasans = $this->assignments($filters, $selectedTahun?->id)
            ->paginate((int) ($filters['per_page'] ?? 10))
            ->appends($request->only(['kelas_id', 'jurusan_id', 'status', 'search', 'per_page', 'sort', 'direction']))
            ->appends(['tahun_id' => $selectedTahun?->id]);

        return view('walikelas.index', compact('tahun', 'selectedTahun', 'kelas', 'jurusan', 'pegawai', 'penugasans', 'editing', 'inputRows'));
    }

    public function store(StoreWaliKelasRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        app(SaveWaliKelasAssignments::class)->save((int) $data['tahun_id'], $data['penugasans']);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Data berhasil disimpan.']);
        }

        return to_route('walikelas.index', ['tahun_id' => $data['tahun_id']])
            ->with('success', 'Penugasan wali kelas berhasil disimpan.');
    }

    public function update(UpdateWaliKelasRequest $request, WaliKelas $walikelas): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        $assignment = DB::transaction(function () use ($walikelas, $data): WaliKelas {
            Tahun::orderBy('id')->lockForUpdate()->get(['id']);
            $assignment = WaliKelas::whereKey($walikelas->id)->lockForUpdate()->firstOrFail();
            $tahunId = $data['tahun_id'] ?? $assignment->tahun_id;
            $kelasId = $data['kelas_id'] ?? $assignment->kelas_id;
            $pegawaiId = $data['pegawai_id'] ?? $assignment->pegawai_id;

            if (WaliKelas::where('tahun_id', $tahunId)->where('kelas_id', $kelasId)
                ->where('pegawai_id', $pegawaiId)->whereKeyNot($assignment->id)->exists()) {
                throw ValidationException::withMessages([
                    'pegawai_id' => 'Penugasan pegawai pada kelas dan periode ini sudah ada.',
                ]);
            }

            if ((bool) $data['is_active']) {
                WaliKelas::where('tahun_id', $tahunId)->where('kelas_id', $kelasId)
                    ->whereKeyNot($assignment->id)->where('is_active', true)->update(['is_active' => false]);
            }

            $assignment->update($data);

            return $assignment;
        }, 3);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Update berhasil',
                'data' => $assignment,
                'statuses' => WaliKelas::where('tahun_id', $assignment->tahun_id)
                    ->where('kelas_id', $assignment->kelas_id)->get(['id', 'is_active']),
            ]);
        }

        return to_route('walikelas.index', ['tahun_id' => $assignment->tahun_id])
            ->with('success', 'Penugasan wali kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, ?WaliKelas $walikelas = null): RedirectResponse
    {
        if ($walikelas) {
            DB::transaction(function () use ($walikelas): void {
                Tahun::whereKey($walikelas->tahun_id)->lockForUpdate()->firstOrFail();
                $walikelas->delete();
            }, 3);

            return to_route('walikelas.index', ['tahun_id' => $walikelas->tahun_id])
                ->with('success', 'Penugasan wali kelas berhasil dihapus.');
        }

        $data = $request->validate([
            'tahun_id' => ['required', 'integer', 'exists:tahuns,id'],
            'id' => ['required', 'array', 'min:1', 'max:100'],
            'id.*' => ['required', 'integer', 'distinct', 'exists:wali_kelas,id'],
        ]);

        DB::transaction(function () use ($data): void {
            Tahun::whereKey($data['tahun_id'])->lockForUpdate()->firstOrFail();
            $assignments = WaliKelas::whereIn('id', $data['id'])->where('tahun_id', $data['tahun_id']);
            if ((clone $assignments)->count() !== count($data['id'])) {
                throw ValidationException::withMessages(['id' => 'Pilih penugasan dari periode yang sedang ditampilkan.']);
            }
            $assignments->delete();
        }, 3);

        return to_route('walikelas.index', ['tahun_id' => $data['tahun_id']])
            ->with('success', 'Penugasan wali kelas berhasil dihapus.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $this->filters($request);
        $data = $request->validate([
            'tahun_id' => ['required', 'integer', 'exists:tahuns,id'],
            'ids' => ['nullable', 'array', 'max:100'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:wali_kelas,id'],
        ]);
        $query = $this->assignments($filters, (int) $data['tahun_id']);
        if (! empty($data['ids'])) {
            $query->whereIn('wali_kelas.id', $data['ids']);
            if ((clone $query)->count() !== count($data['ids'])) {
                throw ValidationException::withMessages(['ids' => 'Pilihan ekspor harus sesuai periode dan filter yang ditampilkan.']);
            }
        }

        return Excel::download(new WaliKelasExport($query), 'WaliKelas.xlsx');
    }

    public function import(ImportWaliKelasRequest $request): RedirectResponse
    {
        $period = Tahun::findOrFail($request->integer('tahun_id'));
        $import = new WaliKelasImport($period);
        try {
            Excel::import($import, $request->file('file'));
        } catch (SpreadsheetReaderException $exception) {
            throw ValidationException::withMessages(['file' => 'File tidak dapat dibaca. Gunakan file Excel atau CSV yang valid.']);
        }
        if ($import->rows === []) {
            throw ValidationException::withMessages(['file' => 'File tidak berisi data penugasan.']);
        }
        app(SaveWaliKelasAssignments::class)->save($period->id, $import->rows);

        return to_route('walikelas.index', ['tahun_id' => $period->id])
            ->with('success', count($import->rows).' penugasan wali kelas berhasil diimpor.');
    }

    /** @return array<string, mixed> */
    private function filters(Request $request): array
    {
        return $request->validate([
            'tahun_id' => ['nullable', 'integer', 'exists:tahuns,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'jurusan_id' => ['nullable', 'integer', 'exists:jurusans,id'],
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif'])],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', Rule::in([10, 25, 50, 100])],
            'sort' => ['nullable', Rule::in(['id', 'nama', 'nuptk', 'kelas', 'status', 'keterangan'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'edit' => ['nullable', 'integer'],
        ]);
    }

    /** @param array<string, mixed> $filters */
    private function assignments(array $filters, ?int $tahunId): Builder
    {
        $query = WaliKelas::with(['kelas:id,kelas', 'pegawai:id,name,nuptk'])->where('tahun_id', $tahunId)
            ->when($filters['kelas_id'] ?? null, fn (Builder $query, int|string $id): Builder => $query->where('kelas_id', $id))
            ->when($filters['jurusan_id'] ?? null, fn (Builder $query, int|string $id): Builder => $query->whereHas('kelas', fn (Builder $kelas): Builder => $kelas->where('jurusan_id', $id)))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status): Builder => $query->where('is_active', $status === 'aktif'));
        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search): void {
                $query->whereHas('pegawai', fn (Builder $pegawai) => $pegawai->where('name', 'like', "%{$search}%")->orWhere('nuptk', 'like', "%{$search}%"))
                    ->orWhereHas('kelas', fn (Builder $kelas) => $kelas->where('kelas', 'like', "%{$search}%"))
                    ->orWhere('keterangan', 'like', "%{$search}%");
                if (in_array(strtolower($search), ['aktif', 'nonaktif'], true)) {
                    $query->orWhere('is_active', strtolower($search) === 'aktif');
                }
            });
        }
        $direction = $filters['direction'] ?? 'asc';
        $sort = $filters['sort'] ?? null;
        if (in_array($sort, ['nama', 'nuptk'], true)) {
            $query->orderBy(Pegawai::select($sort === 'nama' ? 'name' : 'nuptk')->whereColumn('pegawais.id', 'wali_kelas.pegawai_id'), $direction);
        } elseif ($sort === 'kelas') {
            $query->orderBy(Kelas::select('kelas')->whereColumn('kelas.id', 'wali_kelas.kelas_id'), $direction);
        } elseif ($sort) {
            $query->orderBy($sort === 'status' ? 'is_active' : $sort, $direction);
        } else {
            $query->orderByDesc('is_active');
        }

        return $query->orderByDesc('wali_kelas.id');
    }
}
