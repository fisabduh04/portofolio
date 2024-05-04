<?php

namespace App\Livewire\Coba;

use Livewire\Component;
use App\Models\User;

class Cobaaja extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.coba.cobaaja', [
            'users' => User::search($this->search)->get(),
        ]);
    }
}
