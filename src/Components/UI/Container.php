<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Container extends Component
{
    public function __construct(public string $maxWidth = '7xl') {}

    public function render(): View|string
    {
        return view('smart_cms::ui.container');
    }
}
