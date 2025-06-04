<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tooltip extends Component
{
    public function __construct(public string $text = '', public string $position = 'top') {}

    public function render(): View|string
    {
        return view('smart_cms::ui.tooltip');
    }
}
