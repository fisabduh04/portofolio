<?php

namespace App\Livewire;

use Livewire\Component;

class Toast extends Component
{
    public $message;
    public $type;
    public $show = false;

    protected $listeners = ['showToast' => 'displayToast'];

    public function mount()
    {
        if (session()->has('message')) {
            $this->displayToast(
                session('message'),
                session('type', 'success')
            );
        }
    }

    public function displayToast($message, $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;

        $this->js("setTimeout(() => {
            \$wire.hideToast()
        }, 3000)");
    }

    public function hideToast()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.toast');
    }
}
