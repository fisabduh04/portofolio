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
        session()->flash('success','data berhasil di simpan');
        // $this->dispatch('notification', ['success', 'Operasi berhasil!']);
        $this->dispatch('success');

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
        // $this->dispatch('error',['message'=>'Data jurusan berhasil ditambahkan'] );
        $this->dispatch('error');

        session()->flash('delete', 'Data Jurusan behasil dihapus.');
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
            'deskripsi'=>$this->deskripsi
        ]);

        $this->editStudentIndex = null;

        $this->dispatch('warning');
        session()->flash('success','Data berhasi diupdate');
    }
}

