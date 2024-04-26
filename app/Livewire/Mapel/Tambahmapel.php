<?php

namespace App\Livewire\Mapel;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use App\Models\mapel;
use App\Models\jurusan;


class Tambahmapel extends Component
{
    public $kode = [];
    public $mapel = [];
    public $i = 0;
    public $kepala_table = false;
    public $jurusan=[];
    public $ket=[];
    public $editmapelindex=null;
    public $data;
    public $editkode;
    public $editmapel;
    public $editjurusan;
    public $editket;
    public $mapel_id=[];

    public function render(){

        return view('livewire.mapel.tambahmapel',[
            'jurusanlist'=>jurusan::all(),
            'mapellist'=>mapel::all()
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
    }
    public function remove($index)
    {
        unset($this->kode[$index]);
        unset($this->mapel[$index]);
        unset($this->jurusan[$index]);
        unset($this->ket[$index]);
        if ($index==1) {
            $this->kepala_table = false;
        }

    }
    public function store()
    {
        $this->validate([
            'kode.*' => 'required',
            'mapel.*' => 'required',
            'jurusan.*' => 'required',
        ]);

        foreach ($this->mapel as $key => $value) {
            mapel::create([
                'mapel' => $value,
                'kode' => $this->kode[$key],
                'jurusan_id' => $this->jurusan[$key],
                'ket' => $this->ket[$key],
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
        $this->mapel = [];
        $this->i = 0;
        $this->kepala_table = false;
        $this->mapel_id=[];
    }
    public function edit($id){
        $this->editmapelindex=$id;
        $data=mapel::find($id);
        $this->editkode=$data->kode;
        $this->editmapel=$data->mapel;
        $this->editjurusan= $data->jurusan_id;
        $this->editket= $data->ket;

    }

    public function update($id){
        $data=mapel::find($id);
        $data->update([
            'kode' => $this->editkode,
            'mapel' => $this->editmapel,
            'jurusan_id' => $this->editjurusan,
            'ket' => $this->editket,
        ]);

        $this->editmapelindex=null;
        session()->flash('success', 'Data Mapel telah berhasil diperbarui');
        $this->dispatch('warning');
    }
}
