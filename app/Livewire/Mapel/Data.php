<?php

namespace App\Livewire\Mapel;

use App\Exports\MapelExport;
use App\Imports\ImportMapel;
use App\Models\Jadwal;
use App\Models\Jurusan;
use App\Models\Mapel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Data extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $kode = [];

    public $mapel = [];

    public $i = 0;

    public $kepala_table = false;

    public $jurusan = [];

    public $ket = [];

    public $editmapelindex = null;

    public $data;

    public $editkode;

    public $editmapel;

    public $editjurusan;

    public $editket;

    public $mapel_id = [];

    public $mapel_selected_id = [];

    public $selectAll = false; // Gunakan camelCase

    public $perPage = 10;

    public $search = '';

    public $tombol_simpan = false;

    public $file; // Property untuk file upload

    public $sortField = 'mapel'; // Default sorting column (lowercase to match DB)

    public $sortDirection = 'asc'; // Use string 'asc' or 'desc' to match view expectations

    protected $paginationTheme = 'tailwind'; // Jika Anda menggunakan Tailwind untuk paginasi

    public function updatedPerPage($value)
    {
        $this->perPage = ($value == '-1') ? 10000 : $value;
        $this->resetPage(); // Reset ke halaman 1 ketika perPage berubah
    }

    public function search()
    {
        $query = Mapel::query()->with('jurusan'); // Eager load jurusan to fix N+1

        if ($this->search) {
            $query->where('mapel', 'like', '%'.$this->search.'%')
                ->orWhere('kode', 'like', '%'.$this->search.'%')
                ->orWhereHas('jurusan', function ($q) {
                    $q->where('jurusan', 'like', '%'.$this->search.'%');
                });
        }

        // Terapkan sorting
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            // Default sorting jika tidak ada sortField yang ditentukan
            $query->orderBy('jurusan_id', 'asc');
        }

        return $query->paginate($this->perPage);
    }

    public function render(): View
    {
        $data = $this->search();
        $mapelIdsOnPage = $data->pluck('id')->toArray();

        // Pastikan checkbox "Select All" tidak dicentang jika ada ID yang tidak terpilih di halaman saat ini
        // dan semua ID di halaman saat ini sudah dipilih
        $this->selectAll = count(array_intersect($this->mapel_selected_id, $mapelIdsOnPage)) === count($mapelIdsOnPage) && count($mapelIdsOnPage) > 0;

        return view('livewire.mapel.data', [
            'jurusanlist' => Jurusan::all(),
            'mapellist' => $data,
        ]);
    }

    public function add()
    {
        $this->kepala_table = true;
        $this->i++;
        $this->kode[$this->i] = '';
        $this->mapel[$this->i] = '';
        $this->jurusan[$this->i] = '';
        $this->ket[$this->i] = '';
        $this->tombol_simpan = true;
    }

    public function remove($index)
    {
        unset($this->kode[$index]);
        unset($this->mapel[$index]);
        unset($this->jurusan[$index]);
        unset($this->ket[$index]);
        $this->tombol_simpan = false;
    }

    public function store()
    {
        $this->validate([
            'kode.*' => 'required',
            'mapel.*' => 'required',
            'jurusan.*' => 'required',
        ]);

        try {
            foreach ($this->mapel as $key => $value) {
                Mapel::create([
                    'mapel' => $value,
                    'kode' => $this->kode[$key],
                    'jurusan_id' => $this->jurusan[$key],
                    'ket' => $this->ket[$key],
                ]);
            }

            $this->resetFields();
            $this->dispatch('showToast', message: 'Data berhasil disimpan!', type: 'success');
        } catch (\Exception $e) {
            dd($e->getMessage()); // DEBUG di sini
        }
    }

    private function resetFields()
    {
        $this->kode = [];
        $this->mapel = [];
        $this->i = 0;
        $this->kepala_table = false;
        $this->mapel_id = [];
        $this->mapel_selected_id = [];
        $this->selectAll = false;
        $this->tombol_simpan = false;
        $this->resetPage(); // Reset ke halaman 1 setelah reset
    }

    public function edit($id)
    {
        $this->editmapelindex = $id;
        $data = Mapel::find($id); // Gunakan Mapel
        $this->editkode = $data->kode;
        $this->editmapel = $data->mapel;
        $this->editjurusan = $data->jurusan_id;
        $this->editket = $data->ket;
    }

    public function update($id)
    {
        $data = Mapel::find($id); // Gunakan Mapel
        $data->update([
            'kode' => $this->editkode,
            'mapel' => $this->editmapel,
            'jurusan_id' => $this->editjurusan,
            'ket' => $this->editket,
        ]);

        $this->editmapelindex = null;
        $this->dispatch('showToast', message: 'Data berhasil Diperbarui!', type: 'success');
        $this->resetPage(); // Reset ke halaman 1 setelah update
    }

    public function del(): void
    {
        Gate::authorize('manage-data-master');
        abort_unless(auth()->user()->is_active, 403);
        $this->validate([
            'mapel_selected_id' => ['array'],
            'mapel_selected_id.*' => ['required', 'integer', 'distinct', 'exists:mapels,id'],
        ]);

        if ($this->mapel_selected_id === []) {
            $this->dispatch('showToast', message: 'Pilih mata pelajaran yang akan dihapus.', type: 'warning');

            return;
        }

        try {
            $deleted = DB::transaction(function (): bool {
                $mapels = Mapel::whereKey($this->mapel_selected_id)->orderBy('id')->lockForUpdate()->get();

                if (Jadwal::whereIn('mapel_id', $mapels->modelKeys())->exists()) {
                    return false;
                }

                foreach ($mapels as $mapel) {
                    $mapel->delete();
                }

                return true;
            });
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451 || str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')) {
                $this->dispatch('showToast', message: 'Mapel tidak dapat dihapus karena masih digunakan dalam jadwal atau jurnal mengajar.', type: 'warning');
            } else {
                report($exception);
                $this->dispatch('showToast', message: 'Data mata pelajaran gagal dihapus. Silakan coba kembali.', type: 'error');
            }

            return;
        }

        if (! $deleted) {
            $this->dispatch('showToast', message: 'Mapel tidak dapat dihapus karena masih digunakan dalam jadwal atau jurnal mengajar.', type: 'warning');

            return;
        }

        $this->dispatch('showToast', message: 'Data berhasil dihapus!', type: 'success');
        $this->resetFields();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Hanya pilih ID dari halaman saat ini
            $this->mapel_selected_id = $this->search()->pluck('id')->toArray();
        } else {
            $this->mapel_selected_id = [];
        }
    }

    // Lifecycle hook: otomatis dipanggil Livewire saat property $file berubah (upload selesai)
    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls', // Validasi file
        ]);

        try {
            // Simpan ke variable agar bisa akses $skippedRows setelah import
            $importer = new ImportMapel;
            Excel::import($importer, $this->file);

            $jumlahSkip = count($importer->skippedRows);

            if ($jumlahSkip > 0) {
                // Ada baris yang dilewati — beri tahu user secara spesifik
                $detail = collect($importer->skippedRows)
                    ->map(fn ($r) => "• [{$r['data']}]: {$r['alasan']}")
                    ->join(' | ');
                $this->dispatch('showToast',
                    message: "{$jumlahSkip} baris dilewati: {$detail}",
                    type: 'warning'
                );
            } else {
                $this->dispatch('showToast', message: 'Semua data berhasil diimport!', type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Import Gagal: '.$e->getMessage(), type: 'error');
        }

        $this->reset('file'); // Reset setelah import
        $this->resetPage(); // Reset ke halaman 1 setelah import
    }

    public function export()
    {
        $ids = $this->mapel_selected_id ?? [];

        return Excel::download(new MapelExport($ids), 'MataPelajaran.xlsx');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatedMapelSelectedId()
    {
        // Jika user mencentang/membatalkan centang satu per satu, sesuaikan state SelectAll
        $mapelIdsOnPage = $this->search()->pluck('id')->toArray();
        $this->selectAll = count(array_intersect($this->mapel_selected_id, $mapelIdsOnPage)) === count($mapelIdsOnPage) && count($mapelIdsOnPage) > 0;
    }
}
