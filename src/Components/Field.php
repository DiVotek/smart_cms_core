<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Field extends Component
{
    public $field;

    public string $style;

    public function __construct($field, string $style = 'default')
    {
        $this->field = (object) $field;
        $this->style = $style;
    }

    public function render(): View|Closure|string
    {
        $id = $this->field->type . '_' . now()->timestamp;
        $view = 'templates::' . template() . '.forms.' . $this->style . '.' . $this->field->type;
        return view($view, ['field' => $this->field, 'id' => $id]);
    }
}
