<?php

namespace App\Livewire\Mapel;

use Livewire\Component;
use App\Models\mapel;
use App\Models\jurusan;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;

class Data extends Component
{
    use WithPagination;

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
    public $mapel_selected_id=[];
    public $SelectAll=false;
    public $perPage = 10;
    public $search="";
    public $tombol_simpan=false;
    public $file=false;


    public function updatedPerPage($value)
    {
    // Jika opsi yang dipilih adalah "All", atur $perPage ke nilai yang besar
    // agar tidak ada pembatasan dalam jumlah data yang ditampilkan
    if ($value == '-1') {
        $this->perPage = 10000; // Nilai besar, misalnya 9999
    } else {
        $this->perPage = $value;
    }

    // Reset halaman kembali ke halaman pertama
    // $this->resetPage();
    }
    public function search(){
        // $this->resetPage(); // Set halaman kembali ke halaman pertama saat melakukan pencarian
    // Query pencarian data berdasarkan kondisi tertentu, misalnya nama atau kode mapel

    $data = mapel::orderBy('jurusan_id', 'asc')
        ->where('mapel', 'like', '%' . $this->search . '%')
        ->orWhere('kode', 'like', '%' . $this->search . '%')
        ->orWhereHas('jurusan', function ($query) {
            $query->where('jurusan', 'like', '%' . $this->search . '%');
        })
        ->paginate($this->perPage);
    return $data;
    }

    public function render(){

        $data=$this->search();
        $mapelIdsOnPage = $data->pluck('id')->toArray();
        $this->mapel_selected_id = array_intersect($this->mapel_selected_id, $mapelIdsOnPage);
        // Atur kembali SelectAll ke false saat berpindah halaman\
        $this->SelectAll=session('SelectAll',false);
        return view('livewire.mapel.data',[
            'jurusanlist'=>jurusan::all(),
            'mapellist'=>$data
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
        $this->tombol_simpan=true;
    }
    public function remove($index)
    {
        unset($this->kode[$index]);
        unset($this->mapel[$index]);
        unset($this->jurusan[$index]);
        unset($this->ket[$index]);
        $this->tombol_simpan=false;

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
        $this->dispatch('showToast', message: 'Data berhasil disimpan!', type: 'success');


    }

    private function resetFields()
    {
        $this->kode = [];
        $this->mapel = [];
        $this->i = 0;
        $this->kepala_table = false;
        $this->mapel_id=[];
        $this->mapel_selected_id=[];
        $this->SelectAll=false;
        $this->tombol_simpan=false;
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
        $this->dispatch('showToast', message: 'Data berhasil Diperbarui!', type: 'success');

    }

    public function del(){
        mapel::destroy($this->mapel_selected_id);
        // session()->flash('success','Data Berhasil dihapus');
        $this->dispatch('showToast', message: 'Data berhasil dihapus!', type: 'error');

        $this->resetFields();

    }

    public function updatedSelectAll($value){
        if($value){
            $this->mapel_selected_id = mapel::orderBy('jurusan_id', 'asc')->paginate($this->perPage)->pluck('id')->toArray();
        }else{
            $this->mapel_selected_id=[];
        }
        // dd($this->mapel_selected_id);
        if ($value) {
            session()->flash('SelectAll', true);
        }
    }
    public function import()
    {
        // Pastikan file ada dan valid
        if ($this->file) {
            // Menggunakan Maatwebsite Excel untuk mengimpor file
            Excel::import(new KelasImport, $this->file);
            // Excel::import(new KelasImport, $this->file->getRealPath());


            // Mengirimkan pesan sukses

        $this->dispatch('showToast', message: 'Data berhasil diimport!', type: 'success');

        } else {
            // Menampilkan pesan error jika tidak ada file
        $this->dispatch('showToast', message: 'Data gagal diimport!', type: 'error');

        }
        $this->reset('file');
    }
}
