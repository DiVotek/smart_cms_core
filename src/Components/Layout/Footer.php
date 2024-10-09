<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildLayoutTemplate;
use SmartCms\Core\Actions\Template\GetLinks;

class Footer extends Component
{
    public array $template;

    public function __construct()
    {
        $headerSections = _settings('footer', []);
        $template = BuildLayoutTemplate::run($headerSections);
        $this->template = $template;
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <footer>
                <x-s::layout.builder :data="$template" />
            </footer>
        blade;
    }
}
