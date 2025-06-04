<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Grid extends Component
{
    public function __construct(public int $cols = 1, public int $gap = 4) {}

    public function render(): View|string
    {
        return view('smart_cms::ui.grid');
    }
}
