<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Tutorial extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['resetmySelected' => 'resetSelected'];

    public $mySelected = [];
    public $selectAll = false;
    public $firstId = NULL;
    public $paginate = 5;
    public $value;



    public function render()
    {
        //     $users = User::latest()->paginate(5);
        //     $firstUser = $users->first();
        //     if ($firstUser) {
        //         $this->firstId = $firstUser->id; // Mendapatkan ID dari model pertama jika ada
        //     }
        //     return view('livewire.tutorial', [
        //         'users' => $users
        //     ])->extends('layouts.master')->section('content');
        // }

        // public function updatedMySelected($value)
        // { {
        //         if (is_array($value)) {
        //             $jumlah = count($value);
        //             if ($jumlah == 5) {
        //                 $this->selectAll = true;
        //             } else {
        //                 $this->selectAll = false;
        //             }
        //         }
        //     }
        // }


        // public function resetSelected()
        // {
        //     $this->mySelected = [];
        //     $this->selectAll = false;
        // }
    }
}
