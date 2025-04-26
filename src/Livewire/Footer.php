<?php

namespace SmartCms\Core\Livewire;

use SmartCms\Core\Support\Livewire\App as LivewireApp;

class Footer extends LivewireApp
{
    public function render()
    {
        return <<<blade
        <footer>
            <x-s::layout.footer />
        </footer>
        blade;
    }
}
