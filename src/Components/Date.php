<?php

namespace SmartCms\Core\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Date extends Component
{
    public mixed $date;

    public function __construct(mixed $date = null, string $format = 'd/m/Y')
    {
        $this->date = Carbon::parse($date)->format($format);
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <time {{$attributes}}> {{$date}}</time>
        blade;
    }
}
