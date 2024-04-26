<?php

namespace App\Livewire;

use App\Models\jurusan;
use Livewire\Component;

class Editjurusan extends Component
{
    public $kode = [];
    public $jurusan = [];
    public $i = 0;
    public $kepala_table = false;
    public $editStudentIndex = null;

    public function add()
    {
        $this->kepala_table = true;
        $this->i++;
        $this->kode[$this->i] = '';
        $this->jurusan[$this->i] = '';
    }
    public function remove($index)
    {
        unset($this->kode[$index]);
        unset($this->jurusan[$index]);
    }


    public function render()
    {
        return view('livewire.jurusan', [
            'Listjurusan' => jurusan::all()
        ]);
    }

    public function store()
    {
        $this->validate([
            'kode.*' => 'required',
            'jurusan.*' => 'required',
        ]);

        foreach ($this->jurusan as $key => $value) {
            jurusan::create([
                'jurusan' => $value,
                'kode' => $this->kode[$key],
            ]);
        }

        $this->resetFields();
    }

    private function resetFields()
    {
        $this->kode = [];
        $this->jurusan = [];
        $this->i = 0;
        $this->kepala_table = false;
    }
    public function del($id)
    {
        jurusan::destroy($id);
        session()->flash('delete', 'Data Jurusan behasil dihapus.');
    }
    public function editStudent($StudentIndex)
    {
        $this->editStudentIndex = $StudentIndex;
    }
}
