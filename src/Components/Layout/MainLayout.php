<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Models\Layout;

class MainLayout extends Component
{
    public Layout $layout;

    public function __construct()
    {
        $layout = Layout::query()->where('path', 'main')->where('template', template())->first();
        if (! $layout) {
            throw new Exception('Main layout not found, configure it first');
        }
        $this->layout = $layout;
    }

    public function render(): View|Closure|string
    {
        return view('template::layouts.main', $this->layout->getVariables());
    }
}
