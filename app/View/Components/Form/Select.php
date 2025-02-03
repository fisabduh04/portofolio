<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{

    public $id;
    public $name;
    public $label;
    public $options;
    public $placeholder;
    public $selected;

    public function __construct(
        $name,
        $id = null,
        $label=null,
        $options = [],
        $placeholder = 'Pilih',
        $selected = null
    )
    {
        $this->id = $id ?? $name;
        $this->name = $name;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $name));
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->selected = $selected;
    }


    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
