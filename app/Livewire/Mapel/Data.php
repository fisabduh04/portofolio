<?php

namespace App\Livewire\Mapel;

use Livewire\Component;
use App\Models\Mapel; // Pastikan menggunakan Model yang benar (Mapel, bukan mapel)
use App\Models\Jurusan;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // Tambahkan trait ini
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View; // Import untuk tipe data View
use Illuminate\Support\Facades\Session;

class Data extends Component
{
    use WithPagination;
    use WithFileUploads; // Gunakan trait WithFileUploads

    public $kode = [];
    public $mapel = [];
    public $i = 0;
    public $kepala_table = false;
    public $jurusan = [];
    public $ket = [];
    public $editmapelindex = null;
    public $data;
    public $editkode;
    public $editmapel;
    public $editjurusan;
    public $editket;
    public $mapel_id = [];
    public $mapel_selected_id = [];
    public $selectAll = false; // Gunakan camelCase
    public $perPage = 10;
    public $search = "";
    public $tombol_simpan = false;
    public $file; // Property untuk file upload

    protected $paginationTheme = 'tailwind'; // Jika Anda menggunakan Tailwind untuk paginasi

    public function updatedPerPage($value)
    {
        $this->perPage = ($value == '-1') ? 10000 : $value;
        $this->resetPage(); // Reset ke halaman 1 ketika perPage berubah
    }

    public function search()
    {
        $data = Mapel::orderBy('jurusan_id', 'asc') // Gunakan Mapel
            ->where('mapel', 'like', '%' . $this->search . '%')
            ->orWhere('kode', 'like', '%' . $this->search . '%')
            ->orWhereHas('jurusan', function ($query) {
                $query->where('jurusan', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);
        return $data;
    }

    public function render(): View // Tambahkan return type View
    {
        $data = $this->search();
        $mapelIdsOnPage = $data->pluck('id')->toArray();
        $this->mapel_selected_id = array_intersect($this->mapel_selected_id, $mapelIdsOnPage);
        $this->selectAll = Session::get('selectAll', false); // Mengambil dari session
        return view('livewire.mapel.data', [
            'jurusanlist' => Jurusan::all(),
            'mapellist' => $data,
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
        $this->tombol_simpan = true;
    }

    public function remove($index)
    {
        unset($this->kode[$index]);
        unset($this->mapel[$index]);
        unset($this->jurusan[$index]);
        unset($this->ket[$index]);
        $this->tombol_simpan = false;
    }

    public function store()
{
    $this->validate([
        'kode.*' => 'required',
        'mapel.*' => 'required',
        'jurusan.*' => 'required',
    ]);

    try {
        foreach ($this->mapel as $key => $value) {
            Mapel::create([
                'mapel' => $value,
                'kode' => $this->kode[$key],
                'jurusan_id' => $this->jurusan[$key],
                'ket' => $this->ket[$key],
            ]);
        }

        $this->resetFields();
        $this->dispatch('showToast', message: 'Data berhasil disimpan!', type: 'success');
    } catch (\Exception $e) {
        dd($e->getMessage()); // DEBUG di sini
    }
}


    private function resetFields()
    {
        $this->kode = [];
        $this->mapel = [];
        $this->i = 0;
        $this->kepala_table = false;
        $this->mapel_id = [];
        $this->mapel_selected_id = [];
        $this->selectAll = false;
        $this->tombol_simpan = false;
        $this->resetPage(); // Reset ke halaman 1 setelah reset
    }


    public function edit($id)
    {
        $this->editmapelindex = $id;
        $data = Mapel::find($id); // Gunakan Mapel
        $this->editkode = $data->kode;
        $this->editmapel = $data->mapel;
        $this->editjurusan = $data->jurusan_id;
        $this->editket = $data->ket;
    }

    public function update($id)
    {
        $data = Mapel::find($id); // Gunakan Mapel
        $data->update([
            'kode' => $this->editkode,
            'mapel' => $this->editmapel,
            'jurusan_id' => $this->editjurusan,
            'ket' => $this->editket,
        ]);

        $this->editmapelindex = null;
        $this->dispatch('showToast', message: 'Data berhasil Diperbarui!', type: 'success');
        $this->resetPage(); // Reset ke halaman 1 setelah update
    }

    public function del()
    {
        Mapel::destroy($this->mapel_selected_id); // Gunakan Mapel
        $this->dispatch('showToast', message: 'Data berhasil dihapus!', type: 'error');
        $this->resetFields();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            
            $this->mapel_selected_id = Mapel::orderBy('jurusan_id', 'asc')->paginate($this->perPage)->pluck('id')->toArray(); // Gunakan Mapel
        } else {
            $this->mapel_selected_id = [];
        }
        Session::put('selectAll', $value); // Menyimpan ke session
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls', // Validasi file
        ]);

        try {
            Excel::import(new KelasImport, $this->file); // Gunakan $this->file
            $this->dispatch('showToast', message: 'Data berhasil diimport!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('showToast', message: 'Import Gagal: ' . $e->getMessage(), type: 'error');
        }

        $this->reset('file'); // Reset setelah import
        $this->resetPage(); // Reset ke halaman 1 setelah import

    }
}
