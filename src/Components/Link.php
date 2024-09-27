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
            if (! isset($data['attributes']['title'])) {
                if (config('app.env') === 'production') {
                    $data['attributes']['title'] = 'This link does not have a title';
                } else {
                    $data['attributes']['x-data'] = '';
                    $data['attributes']['x-init'] = "console.error('This link does not have a title', \$el)";
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
