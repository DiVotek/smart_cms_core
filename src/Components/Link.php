<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public string $title;

    public function __construct(string $title = '')
    {
        $this->title = $title;
    }

    public function render(): View|Closure|string
    {
        return function (array $data) {
            if (! isset($data['attributes']['title'])) {
                $data['attributes']['title'] = $this->title;
            }
            if ($data['attributes']['title'] == '') {
                if (isset($data['slot'])) {
                    $data['attributes']['title'] = $data['slot']->__toString();
                }
            }
            if (url()->current() == $data['attributes']['href']) {
                $data['attributes']['aria-current'] = 'page';
                if (! isset($data['attributes']['class'])) {
                    $data['attributes']['class'] = '';
                }
                $data['attributes']['class'] .= ' active';
                unset($data['attributes']['title']);
                unset($data['attributes']['href']);

                return <<<'blade'
                <span {{$attributes}}> {{$slot->isEmpty() ? $title : $slot}} </span>
            blade;
            }

            return <<<'blade'
                <a wire:navigate {{$attributes}}> {{$slot->isEmpty() ? $title : $slot}} </a>
            blade;
        };
    }
}
