<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public $color;
    public $text;

    /**
     * Create a new component instance.
     */
    public function __construct($color = 'blue', $text = 'Default')
    {
        $this->color = $color;
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.badge');
    }
}
