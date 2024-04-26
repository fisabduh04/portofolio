<?php

namespace App\Livewire;

use Livewire\Component;

class Belajar extends Component
{
    public $nama = 'abdullah';
    public $angka = 0;
    public function render()
    {
        return view('livewire.belajar');
    }

    public function plus()
    {
        $this->angka++;
    }
    public function minus()
    {
        $this->angka--;
    }
}
