<?php

namespace App\Livewire\Kelas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;
use App\Models\Jurusan;
use Livewire\WithFileUploads;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;


class Data extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $kelas = [];
    public $i = 0;
    public $jurusan = [];
    public $ket = [];
    public $editkelasindex = null;
    public $editkelas;
    public $editjurusan;
    public $editket;
    public $kelas_id = [];
    public $kelas_selected_id = [];
    public $SelectAll = false;
    public $perPage = 10;
    public $search = "";
    // Maximum number of items considered safe to render 'All' in the browser
    protected int $maxDisplayAll = 20000;
    // Allowed fields for sorting to prevent accidental SQL injection / invalid columns
    protected array $allowedSortFields = ['id', 'kelas', 'jurusan', 'ket'];
    public $tombol_tambah = false;
    public $file;
    public $sortField = 'kelas'; // Default sorting column
    public $sortAsc = true; // Default sorting order

    public function updatedPerPage($value)
    {
        // treat -1 (All) as null sentinel so we can decide server-side how to handle it
        if ((string) $value === '-1' || (string) $value === 'all') {
            $this->perPage = null;
        } else {
            $this->perPage = (int) $value;
        }

        $this->resetPage();
    }

    // Reset pagination when user types a new search term
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Build the base query for kelas with search, eager loading and sorting.
     * Returns an Eloquent builder so caller can decide paginate()/pluck() etc.
     */
    protected function buildQuery()
    {
        $query = Kelas::query()->with('jurusan')->select('kelas.*');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('kelas', 'like', '%' . $this->search . '%')
                  ->orWhereHas('jurusan', function ($subQuery) {
                      $subQuery->where('jurusan', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // If sorting by jurusan, join so we can order by jurusan column.
        if ($this->sortField === 'jurusan') {
            // join is fine here because we still select kelas.* and eager load 'jurusan'
            $query->join('jurusans', 'kelas.jurusan_id', '=', 'jurusans.id')
                  ->select('kelas.*', 'jurusans.jurusan as jurusan_name');
            $orderBy = 'jurusan_name';
        } else {
            $orderBy = $this->sortField;
        }

        return $query->orderBy($orderBy, $this->sortAsc ? 'asc' : 'desc');
    }

    public function render()
    {
        $query = $this->buildQuery();

        // If perPage is null -> user selected "All". Decide if safe to return all rows.
        if (is_null($this->perPage)) {
            $total = $query->count();

            if ($total <= $this->maxDisplayAll) {
                // paginate with total so Blade pagination helpers still work
                $kelaslist = $query->paginate($total ?: 1);
            } else {
                // too large to render all in browser; limit to safe count and notify
                $kelaslist = $query->paginate($this->maxDisplayAll);
                $this->dispatch('showToast', message: "Data terlalu besar untuk ditampilkan sekaligus. Menampilkan {$this->maxDisplayAll} item. Gunakan Export untuk unduh semua.", type: 'warning');
            }
        } else {
            $kelaslist = $query->paginate(max(1, (int) $this->perPage));
        }

        return view('livewire.kelas.data', [
            'jurusanlist' => Jurusan::select('id', 'jurusan')->get(),
            'kelaslist' => $kelaslist,
        ]);
    }

    public function add()
    {
        $this->i++;
        $this->kelas[$this->i] = '';
        $this->jurusan[$this->i] = '';
        $this->ket[$this->i] = '';
        $this->tombol_tambah = true;
    }

    public function remove($index)
    {
        unset($this->kelas[$index]);
        unset($this->jurusan[$index]);
        unset($this->ket[$index]);
        $this->tombol_tambah = false;
    }

    public function store()
    {
        $this->validate([
            'kelas.*' => 'required',
            'jurusan.*' => 'required',
        ]);

        foreach ($this->kelas as $key => $value) {
            Kelas::create([
                'kelas' => $value,
                'jurusan_id' => $this->jurusan[$key],
                'ket' => $this->ket[$key],
            ]);
        }

    //
        $this->dispatch('showToast', message: 'Data berhasil disimpan!', type: 'success');


       $this->resetFields();
    }

    private function resetFields()
    {
        $this->kelas = [];
        $this->i = 0;
        $this->kelas_id = [];
        $this->kelas_selected_id = [];
        $this->SelectAll = false;
        $this->tombol_tambah = false;
    }

    public function edit($id)
    {
        // @dd($id);
        $this->editkelasindex = $id;
        $kelas = Kelas::find($id);
        if (! $kelas) {
            $this->dispatch('showToast', message: 'Data tidak ditemukan', type: 'error');
            $this->editkelasindex = null;
            return;
        }
        $this->editkelas = $kelas->kelas;
        $this->editjurusan = $kelas->jurusan_id;
        $this->editket = $kelas->ket;
    }

    public function update($id = null)
    {
        $useId = $id ?? $this->editkelasindex;
        $data = Kelas::find($useId);
        if (! $data) {
            $this->dispatch('showToast', message: 'Data tidak ditemukan', type: 'error');
            return;
        }
        $data->update([
            'kelas' => $this->editkelas,
            'jurusan_id' => $this->editjurusan,
            'ket' => $this->editket,
        ]);

        $this->editkelasindex = null;


        $this->dispatch('showToast', message: 'Data berhasil diupdate!', type: 'warning');


    }

    public function cancelEdit()
    {
        $this->editkelasindex = null;
        $this->editkelas = null;
        $this->editjurusan = null;
        $this->editket = null;
    }

    public function del()
    {
        Kelas::destroy($this->kelas_selected_id);

        $this->dispatch('showToast', message: 'Data berhasil dihapus!', type: 'error');

        $this->resetFields();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // select the IDs visible on the current page (respecting search/sort)
            $this->kelas_selected_id = $this->getCurrentPageIds();
        } else {
            $this->kelas_selected_id = [];
        }
    }

    /**
     * Return array of IDs visible on the current page, handling perPage=null (All) safely.
     */
    protected function getCurrentPageIds(): array
    {
        $query = $this->buildQuery();

        if (is_null($this->perPage)) {
            $total = $query->count();

            if ($total <= $this->maxDisplayAll) {
                $page = $query->paginate($total ?: 1);
            } else {
                $page = $query->paginate($this->maxDisplayAll);
            }
        } else {
            $page = $query->paginate(max(1, (int) $this->perPage));
        }

        return $page->pluck('id')->toArray();
    }

    public function sortBy($field)
    {
        // validate allowed sort fields
        if (!in_array($field, $this->allowedSortFields)) {
            return; // ignore unknown fields
        }

        $this->sortField = $field;
        $this->sortAsc = !$this->sortAsc;
        $this->resetPage();
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

    }
    public function import()
    {
        if (!$this->file) {
            $this->dispatch('showToast', message: 'Tidak ada file yang dipilih!', type: 'error');
            return;
        }

        try {
            // Simpan ke variable agar bisa akses $skippedRows setelah import
            $importer = new KelasImport;
            Excel::import($importer, $this->file);

            $jumlahSkip = count($importer->skippedRows);

            if ($jumlahSkip > 0) {
                // Ada baris yang dilewati — beri tahu user secara spesifik
                $detail = collect($importer->skippedRows)
                    ->map(fn($r) => "• [{$r['data']}]: {$r['alasan']}")
                    ->join(' | ');
                $this->dispatch('showToast',
                    message: "{$jumlahSkip} baris dilewati: {$detail}",
                    type: 'warning'
                );
            } else {
                $this->dispatch('showToast', message: 'Semua data berhasil diimport!', type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Import Gagal: ' . $e->getMessage(), type: 'error');
        }

        $this->reset('file');
    }

}
