<?php

namespace App\Livewire\Jurusan;

use Livewire\Component;
use App\Models\jurusan;

class Data extends Component
{
    public $semuajurusan;
    public $kode = [];
    public $jurusan = [];
    public $i = 0;
    public $kepala_table = false;
    public $editStudentIndex = null;
    public $editkode;
    public $editjurusan;

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
        $this->semuajurusan=jurusan::all();
        return view('livewire.jurusan.data');
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
        // session()->flash('success','data berhasil di simpan'); // Optional backup
        $this->dispatch('showToast', message: 'Data berhasil disimpan', type: 'success');

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

        // session()->flash('error', 'Data Jurusan behasil dihapus.');
        $this->dispatch('showToast', message: 'Data Jurusan berhasil dihapus.', type: 'success');
    }

    public function editStudent($id)
    {
        $this->editStudentIndex = $id;
        $data=jurusan::find($id);
        $this->editkode=$data->kode;
        $this->editjurusan=$data->jurusan;
    }

    public function update($id){
        $data=jurusan::find($id);
        $this->validate([
            'editkode'=>'required',
            'editjurusan'=>'required'
        ]);
        $data->update([
            'kode'=>$this->editkode,
            'jurusan'=>$this->editjurusan,
        ]);

        $this->editStudentIndex = null;

        // session()->flash('success','Data berhasi diupdate');
        $this->dispatch('showToast', message: 'Data berhasil diupdate', type: 'success');
    }
}

