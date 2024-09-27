<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Slide extends Component
{
    public function render(): View|Closure|string
    {
        return <<<'blade'
         <li {{ $attributes->merge(['class' => 'splide__slide']) }} role="{{__('Slide')}}">
            {{$slot}}
        </li>
        blade;
    }
}
