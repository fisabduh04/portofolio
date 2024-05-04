<?php

namespace App\Livewire\Tahun;

use App\Models\tahun_ajaran;


use Livewire\Component;

class Datatahun extends Component
{
    public $form = false;
    public $tanggalmulai;
    public $tanggalakhir;
    public $tahun;
    public $semester;
    public $tahunID;
    public $tombol_simpan = "Simpan";


    public function render()
    {
        return view('livewire.tahun.datatahun', [
            'tahunlist' => tahun_ajaran::all()
        ]);
    }
    public function tambah()
    {
        $this->form = true;
    }

    public function edit($id)
    {
        // dd($id);
        $tahun = tahun_ajaran::find($id);
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
        tahun_ajaran::updateOrCreate([
            'id' => $this->tahunID
        ], [
            'tahun' => $this->tahun,
            'semester' => $this->semester,
            'tanggalmulai' => $this->tanggalmulai,
            'tanggalakhir' => $this->tanggalakhir
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
}
