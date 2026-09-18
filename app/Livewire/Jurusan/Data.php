<?php

namespace App\Livewire\Jurusan;

use App\Models\jurusan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

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
        $this->semuajurusan = jurusan::all();

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

    public function del(int $id): void
    {
        Gate::authorize('manage-data-master');
        abort_unless(auth()->user()->is_active, 403);
        $jurusan = jurusan::find($id);

        if (! $jurusan) {
            $this->dispatch('showToast', message: 'Jurusan tidak ditemukan atau sudah dihapus.', type: 'warning');

            return;
        }

        try {
            DB::transaction(fn () => $jurusan->delete());
        } catch (QueryException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451 || str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')) {
                $this->dispatch('showToast', message: 'Jurusan tidak dapat dihapus karena masih memiliki kelas.', type: 'warning');
            } else {
                report($exception);
                $this->dispatch('showToast', message: 'Data jurusan gagal dihapus. Silakan coba kembali.', type: 'error');
            }

            return;
        }

        $this->dispatch('showToast', message: 'Data Jurusan berhasil dihapus.', type: 'success');
    }

    public function editStudent($id)
    {
        $this->editStudentIndex = $id;
        $data = jurusan::find($id);
        $this->editkode = $data->kode;
        $this->editjurusan = $data->jurusan;
    }

    public function update($id)
    {
        $data = jurusan::find($id);
        $this->validate([
            'editkode' => 'required',
            'editjurusan' => 'required',
        ]);
        $data->update([
            'kode' => $this->editkode,
            'jurusan' => $this->editjurusan,
        ]);

        $this->editStudentIndex = null;

        // session()->flash('success','Data berhasi diupdate');
        $this->dispatch('showToast', message: 'Data berhasil diupdate', type: 'success');
    }
}
