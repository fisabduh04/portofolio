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
    public $tombol_tambah = false;
    public $file;
    public $sortField = 'kelas'; // Default sorting column
    public $sortAsc = true; // Default sorting order

    public function updatedPerPage($value)
    {
        if ($value == '-1') {
            $this->perPage = 10000;
        } else {
            $this->perPage = $value;
        }
        $this->resetPage();
    }

    public function search()
    {
        $query = Kelas::query();

        if (!empty($this->search)) {
            $query->where('kelas', 'like', '%' . $this->search . '%')
                  ->orWhereHas('jurusan', function ($subQuery) {
                      $subQuery->where('jurusan', 'like', '%' . $this->search . '%');
                  });
        }

        // Proper sorting handling
        if ($this->sortField === 'jurusan') {
            $query->join('jurusans', 'kelas.jurusan_id', '=', 'jurusans.id')
                  ->select('kelas.*', 'jurusans.jurusan as jurusan_name'); // Assuming 'jurusan' is the column name in 'jurusans' table
        }

        return $query->orderBy($this->sortField === 'jurusan' ? 'jurusan_name' : $this->sortField, $this->sortAsc ? 'asc' : 'desc')
                     ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.kelas.data', [
            'jurusanlist' => Jurusan::all(),
            'kelaslist' => $this->search()
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
        $this->editkelas = $kelas->kelas;
        $this->editjurusan = $kelas->jurusan_id;
        $this->editket = $kelas->ket;
    }

    public function update()
    {
        $data = Kelas::find($this->editkelasindex);
        $data->update([
            'kelas' => $this->editkelas,
            'jurusan_id' => $this->editjurusan,
            'ket' => $this->editket,
        ]);

        $this->editkelasindex = null;


        $this->dispatch('showToast', message: 'Data berhasil diupdate!', type: 'warning');




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
            $this->kelas_selected_id = Kelas::orderBy('jurusan_id', 'asc')->paginate($this->perPage)->pluck('id')->toArray();
        } else {
            $this->kelas_selected_id = [];
        }
    }

    public function sortBy($field)
    {
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
        // Pastikan file ada dan valid
        if ($this->file) {
            // Menggunakan Maatwebsite Excel untuk mengimpor file
            Excel::import(new KelasImport, $this->file);
            // Excel::import(new KelasImport, $this->file->getRealPath());


            // Mengirimkan pesan sukses

        $this->dispatch('showToast', message: 'Data berhasil diimport!', type: 'success');

        } else {
            // Menampilkan pesan error jika tidak ada file
        $this->dispatch('showToast', message: 'Data gagal diimport!', type: 'error');

        }
        $this->reset('file');
    }

}
