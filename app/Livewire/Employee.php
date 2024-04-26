<?php

namespace App\Livewire;

use App\Models\Employee as ModelsEmployee;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithPagination;


class Employee extends Component
{
    use WithPagination;
    public $nama;
    public $email;
    public $alamat;
    public $dataEmployees;
    public $updateData = false;
    public $employee_id;
    public $nama1;
    public $katakunci;
    public $employee_selected_id = [];
    public $id_a = [];
    public $id1;
    public $sortColumn = 'nama';
    public $sortDirection = 'asc';



    public function store()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
        ];
        $pesan = [
            'nama.required' => 'Nama Wajib diisi',
            'email.required' => 'Email Wajib diisi',
            'email.email' => 'Email harus sesuai dengan format email contoh admin@gmail.com',
            'alamat.required' => 'Alamat Wajib diisi'
        ];
        $validated = $this->validate($rules, $pesan);

        ModelsEmployee::create($validated);
        // return resetField();
        session()->flash('success', 'Data Berhasil Disimpan');
        $this->clear();
    }

    public function edit($id)
    {
        $data = ModelsEmployee::find($id);
        $this->nama = $data->nama;
        $this->alamat = $data->alamat;
        $this->email = $data->email;

        $this->updateData = true;
        $this->employee_id = $id;
    }

    public function update()
    {
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
        ];
        $pesan = [
            'nama.required' => 'Nama Wajib diisi',
            'email.required' => 'Email Wajib diisi',
            'email.email' => 'Email harus sesuai dengan format email contoh admin@gmail.com',
            'alamat.required' => 'Alamat Wajib diisi'
        ];
        $validated = $this->validate($rules, $pesan);
        $data = ModelsEmployee::find($this->employee_id);
        $data->update($validated);
        session()->flash('success', 'Data Berhasil diupdate');
        $this->clear();
    }

    public function clear()
    {
        $this->nama = '';
        $this->email = '';
        $this->alamat = '';

        $this->updateData = false;
        $this->employee_id = '';
        $this->employee_selected_id = [];
    }

    public function delete_confirmation($id)
    {
        if ($id != null) {
            $this->employee_id = $id;
            $data = ModelsEmployee::find($id);
        } else {
        }
        $data = ModelsEmployee::find($id);
        // $this->id1 = $data->id;
        // @dd($data);
    }

    public function delete()
    {
        if ($this->employee_id != null) {
            $id = $this->employee_id;
            ModelsEmployee::find($id)->delete();
        }
        if (count($this->employee_selected_id)) {
            for ($x = 0; $x < count($this->employee_selected_id); $x++) {
                ModelsEmployee::find($this->employee_selected_id[$x])->delete();
            }
        }
        session()->flash('success', 'Data berhasil dihapus');
        $this->clear();
    }

    public function sort($sortName)
    {
        $this->sortColumn = $sortName;
        $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
    }

    public function render()
    {
        if ($this->katakunci != null) {
            # code...
            $data = ModelsEmployee::where('nama', 'like', '%' . $this->katakunci . '%')
                ->orWhere('email', 'like', '%' . $this->katakunci . '%')
                ->orWhere('alamat', 'like', '%' . $this->katakunci . '%')
                ->orderBy($this->sortColumn, $this->sortDirection)->paginate(2);
        } else {
            $data = ModelsEmployee::orderBy($this->sortColumn, $this->sortDirection)->paginate(2);
        }
        return view('livewire.employee', compact('data'));
    }
}
