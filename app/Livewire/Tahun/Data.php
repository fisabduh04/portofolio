<?php

namespace App\Livewire\Tahun;

use App\Models\tahun;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Session;
class Data extends Component
{
    public $form = false;

    public $tanggalmulai;

    public $tanggalakhir;

    public $tahun;

    public $semester;

    public $tahunID;

    public $tombol_simpan = 'Simpan';

    public $isActive = 0;

    public $tahunlist;


    public function mount()
    {
        $this->loadTahunList();
    }

    private function loadTahunList() {
        $this->tahunlist = tahun::all();
    }

    public function toggleIsActive($id)
    {
        $tahun = tahun::find($id);
        if ($tahun) {
            $tahun->isActive = !$tahun->isActive;
            $tahun->save();
        }
        session()->flash('warning', 'Status keaktifan berhasil diubah');
        $this->loadTahunList(); // Refresh the list
    }


    public function render()
    {
        return view('livewire.tahun.data', [
            'tahunlist' => $this->tahunlist,
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
        $this->isActive = $tahun->isActive;
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
            'tanggalakhir' => 'nullable|date|after_or_equal:tanggalmulai',
            'isActive' => 'required|boolean|in:0,1',
        ], [
            'tahun.required' => 'Tahun ajaran harus diisi.',
            'semester.required' => 'Semester harus dipilih.',
            'isActive.required' => 'Status keaktifan harus ditentukan.',
            'tanggalmulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggalakhir.date' => 'Format tanggal akhir tidak valid.',
        ]);

        try{
        tahun::updateOrCreate([
            'id' => $this->tahunID,
        ], [
            'tahun' => $this->tahun,
            'semester' => $this->semester,
            'tanggalmulai' => $this->tanggalmulai,
            'tanggalakhir' => $this->tanggalakhir,
            'isActive' => $this->isActive,
        ]);
        session()->flash('success', 'Data berhasil diinput');

        $this->reset(['tanggalmulai', 'tanggalakhir', 'tahun', 'semester', 'isActive']);
        $this->loadTahunList(); // Refresh the list
    } catch(QueryException $e) {
        if ($e->errorInfo[1] === 1062) { // Cek kode error MySQL untuk duplikasi
            Session::flash('error', 'Data tahun dengan tahun dan semester tersebut sudah ada.');
            // Opsi tambahan:
            $this->addError('tahun', 'Data tahun dengan tahun dan semester tersebut sudah ada.'); // Menampilkan error di bawah input field
        } else {
            // Tangani error lain jika ada
            Session::flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
    $this->form=false;
}


    public function del($id)
    {
        tahun::destroy($id);

        session()->flash('error', 'Gateli Data Berhasil Dihapus');
        $this->loadTahunList(); // Refresh the list
    }

}
