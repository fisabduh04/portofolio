<?php

namespace App\Livewire;

use App\Models\Kelas as ModelKelas;
use App\Models\jurusan;
use Livewire\Component;

class Kelas extends Component
{
    public $kelas = [];
    public $jurusan = [];
    public $i = 0;
    public $kepala_table = false;
    public $editkelasindex=null;
    public $editkelas;
    public $editjurusan;

    public function add()
    {
        $this->kepala_table = true;
        $this->i++;
        $this->kelas[$this->i] = '';
        $this->jurusan[$this->i] = '';
    }

    public function remove($index)
    {
        unset($this->kelas[$index]);
        unset($this->jurusan[$index]);
    }

    public function render()
    {

        return view('livewire.kelas', [
            'kelasList' => ModelKelas::all(),
            'jurusanlist' => jurusan::all()
        ]);
    }

    public function store()
    {
        $this->validate([
            'kelas.*' => 'required',
            'jurusan.*' => 'required',
        ]);
        foreach ($this->kelas as $key => $value) {
            ModelKelas::create([
                'kelas' => $value,
                'jurusan_id' => $this->jurusan[$key],
            ]);
        }

        $this->resetFields();
        session()->flash('success', 'Data Kelas Telah Berhasil Diinput');
        $this->dispatch('success' );

    }

    private function resetFields()
    {
        $this->kelas = [];
        $this->jurusan = [];
        $this->i = 0;
        $this->kepala_table = false;
    }
    public function del($id)
    {
        ModelKelas::destroy($id);
        session()->flash('delete', 'Data Kelas Telah Berhasil dihapus');
        $this->dispatch('error' );

    }
    public function edit($id){
        $this->editkelasindex=$id;
        $data=ModelKelas::find($id);
        if ($data) {
            $this->editkelas=$data->kelas;
            $this->editjurusan=$data->jurusan_id;
        }
    }
    public function update($id){
        $data=ModelKelas::find($id);
        $data->update([
            'kelas' => $this->editkelas,
            'jurusan_id' => $this->editjurusan,
        ]);

        $this->editkelasindex=null;
        session()->flash('success', 'Data Kelas telah berhasil diperbarui');
        $this->dispatch('warning');

    }

}
