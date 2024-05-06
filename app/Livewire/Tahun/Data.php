<?php

namespace App\Livewire\Tahun;
use App\Models\tahun;

use Livewire\Component;

class Data extends Component
{
    public $form = false;
    public $tanggalmulai;
    public $tanggalakhir;
    public $tahun;
    public $semester;
    public $tahunID;
    public $tombol_simpan = "Simpan";
    public $isActive;


    public function render()
    {
        return view('livewire.tahun.data',[
            'tahunlist'=>tahun::all()
        ]);
    }
    public function tambah()
    {
        $this->form = true;
    }

    public function edit($id)
    {
        // dd($id);
        $tahun = tahun::find($id);
        $this->tahunID = $tahun->id;
        $this->tanggalmulai = $tahun->tanggalmulai;
        $this->tanggalakhir = $tahun->tanggalakhir;
        $this->tahun = $tahun->tahun;
        $this->semester = $tahun->semester;
        $this->tombol_simpan = 'Update';
        $this->form = true;
    }
    public function close()
    {
        $this->form = false;
    }
    public function store()
    {
        $this->validate([
            'tahun' => 'required|string',
            'semester' => 'required|string',
            'tanggalmulai' => 'nullable|date',
            'tanggalakhir' => 'nullable|date',
            'isActive' => 'required'
        ],[
            'tahun.required' => 'Tahun ajaran harus diisi.',
            'semester.required' => 'Semester harus dipilih.',
            'isActive.required' => 'Status keaktifan harus ditentukan.',
            'tanggalmulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggalakhir.date' => 'Format tanggal akhir tidak valid.'
        ]);

        $isActiveBoolean = $this->isActive === '1' ? true : false;

        tahun::updateOrCreate([
            'id' => $this->tahunID
        ], [
            'tahun' => $this->tahun,
            'semester' => $this->semester,
            'tanggalmulai' => $this->tanggalmulai,
            'tanggalakhir' => $this->tanggalakhir,
            'isActive'=>$isActiveBoolean,
        ]);
        session()->flash('success', 'Data berhasil diinput');
        $this->resetField();
    }
    public function resetField()
    {
        $this->tanggalmulai = '';
        $this->tanggalakhir = '';
        $this->tahun = '';
        $this->semester = '';
        $this->form = false;
        $this->tombol_simpan = 'Simpan';
    }

    public function del($id)
    {
        tahun::destroy($id);
        session()->flash('error','Data Berhasil Dihapus');
    }
}
