<?php

namespace App\Livewire\Mapel;

use Livewire\Component;
use App\Models\Mapel;
use App\Models\Jurusan;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\ImportMapel;
use App\Exports\MapelExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class Data extends Component
{
    use WithPagination;
    use WithFileUploads;

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
    public $search = "";
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
        $query->where('mapel', 'like', '%' . $this->search . '%')
              ->orWhere('kode', 'like', '%' . $this->search . '%')
              ->orWhereHas('jurusan', function ($q) {
                  $q->where('jurusan', 'like', '%' . $this->search . '%');
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

    public function del()
    {
        Mapel::destroy($this->mapel_selected_id); // Gunakan Mapel
        $this->dispatch('showToast', message: 'Data berhasil dihapus!', type: 'error');
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

   
    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls', // Validasi file
        ]);

        try {
            Excel::import(new ImportMapel, $this->file); // Gunakan $this->file
            $this->dispatch('showToast', message: 'Data berhasil diimport!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Import Gagal: ' . $e->getMessage(), type: 'error');
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
