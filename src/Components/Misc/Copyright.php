<?php

namespace SmartCms\Core\Components\Misc;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Copyright extends Component
{
    public string $date;

    public string $name;

    public function __construct()
    {
        $this->date = date('Y');
        $this->name = 'SmartCms';
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <meta name="generator" content="{{ $name }}">
       blade;
    }
}
