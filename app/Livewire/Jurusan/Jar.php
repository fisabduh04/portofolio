<?php

namespace App\Livewire\Jurusan;

use Livewire\Component;
use App\Models\jurusan;

class Jar extends Component
{
    public $editStudentIndex=null;
    public $jurusan1;
    public $jurusan;
    public $kode;

    public function render()
    {
        $this->jurusan=jurusan::all();
        return view('livewire.jurusan.jar');
    }
    public function del($id){
        jurusan::destroy($id);
    }
    public function editStudent($id)
    {
        $data=jurusan::find($id);
        $this->editStudentIndex = $id;
        $this->kode=$data->kode;
        $this->jurusan1=$data->jurusan;
    }
    public function update($id) {
        $data=jurusan::find($id);
        $data->update([
            'kode'=>$this->kode,
            'jurusan'=>$this->jurusan1
        ]);

        $this->editStudentIndex = null;
    }



}
