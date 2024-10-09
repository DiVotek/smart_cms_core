<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildLayoutTemplate;

class Header extends Component
{
    public array $template;

    public function __construct()
    {
        $headerSections = _settings('header', []);
        $template = BuildLayoutTemplate::run($headerSections);
        $this->template = $template;
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <header>
                <x-s::layout.builder :data="$template" />
            </header>
        blade;
    }
}
