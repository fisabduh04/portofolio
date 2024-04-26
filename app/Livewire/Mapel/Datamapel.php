<?php

namespace App\Livewire\Mapel;

use Livewire\Component;
use App\Models\mapel;

class Datamapel extends Component
{
    public $editmapelindex=null;
    public $data;
    public $kode;
    public $editkode;

    public function mount(){
        $this->data=mapel::all();
    }
    public function render()
    {
        return view('livewire.mapel.datamapel',['mapellist'=>mapel::all() ]);
    }
    public function edit($id){
        $this->editmapelindex=$id;
    }

    public function store(){
        $this->editmapelindex=null;
    }
    public function updatedData($value, $id, $field)
{
    // dd($id);
    // $record = Mapel::findOrFail($id);
    // $record->$field = $value;
    // $record->save();
    return 'sehat';
}
public function ubah($id)
{
    dd($this->editkode);
    // $record = Mapel::findOrFail($id);
    // $record->update([
    //     'kode'=>$editkode
    // ]);
}

}
