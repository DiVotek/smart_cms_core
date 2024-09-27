<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Field extends Component
{
    public  $field;
    public string $style;

    public function __construct($field, string $style='demo')
    {
        $this->field = (object)$field;
        $this->style = $style;
    }

    public function render(): View|Closure|string
    {
        $id = $this->field->type . '_' . now()->timestamp;
        return view('templates::forms.' . $this->style . '.' . $this->field->type, ['field' => $this->field, 'id' => $id]);
    }
}
