<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class input extends Component
{
    public $id;
    public $name;
    public $label;
    public $type;
    public $placeholder;
    public $value;
    public $required;

    public function __construct(
        $name,
        $id = null,
        $label = null,
        $type = 'text',
        $placeholder = null,
        $value = '',
        $required = true,
    )
    {
        $this->id = $id ?? $name;
        $this->name = $name;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $name));
        $this->type = $type;
        $this->placeholder = $placeholder ?? $label;
        $this->value = $value;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
