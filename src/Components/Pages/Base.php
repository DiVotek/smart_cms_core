<?php

namespace SmartCms\Core\Components\Pages;

use Illuminate\View\Component;

class Base extends Component
{

    public function __construct(public string $dynamicComponent, public array $dynamicComponentData = []) {}

    public function render()
    {
        return <<<'blade'
            <x-s::layout.layout>
                @section("content")
                @livewire($dynamicComponent, $dynamicComponentData)
                <x-s::layout.builder />
                @endsection
            </x-s::layout.layout>
        blade;
    }
}
