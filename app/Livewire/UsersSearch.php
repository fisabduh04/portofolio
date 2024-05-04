<?php

namespace App\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\User;

class UsersSearch extends Component
{
    public $search = '';
    public function render()
    {
        return view('livewire.users-search', [
            'users' => User::search($this->search)->get(),
        ]);
    }
}
