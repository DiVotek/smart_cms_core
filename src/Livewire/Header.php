<?php

namespace SmartCms\Core\Livewire;

use SmartCms\Core\Support\Livewire\App as LivewireApp;

class Header extends LivewireApp
{
    public function render()
    {
        return <<<blade
        <header>
            <x-s::layout.header />
        </header>
        blade;
    }
}
