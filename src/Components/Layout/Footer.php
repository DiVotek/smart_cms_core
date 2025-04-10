<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Models\Layout;

class Footer extends Component
{
    public Layout $layout;

    public function __construct()
    {
        $layout = Layout::query()->where('id', _settings('layouts.footer'))->first();
        if (! $layout) {
            throw new Exception('Footer layout not found, configure it first');
        }
        $this->layout = $layout;
    }

    public function render(): View|Closure|string
    {
        return view('layouts.' . $this->layout->path, $this->layout->getVariables());
    }
}
