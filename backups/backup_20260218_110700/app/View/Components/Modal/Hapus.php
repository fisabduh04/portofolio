<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Hapus extends Component
{
    /**
     * Create a new component instance.
     */
    public $id;
    public $action;
    public function __construct($id, $action = null)
    {
        $this->id = $id;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.hapus');
    }
}
