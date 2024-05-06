<?php

namespace App\Livewire\Kelas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;
use App\Models\Jurusan;

class Data extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

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

        $this->resetFields();
        session()->flash('success', 'Data berhasil disimpan');
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
        session()->flash('success', 'Data kelas berhasil diperbarui');
    }

    public function del()
    {
        Kelas::destroy($this->kelas_selected_id);
        session()->flash('success', 'Data berhasil dihapus');
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

}
