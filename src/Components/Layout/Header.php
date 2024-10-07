<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\GetLinks;

class Header extends Component
{
    public array $links;

    public function __construct()
    {
        $this->links = GetLinks::run();
    }

    public function render(): View|Closure|string
    {
        $view = _settings('design.header', 'default-header');
        return view('templates::' . template() . '.'  . 'layout.' . strtolower($view));
    }
}
