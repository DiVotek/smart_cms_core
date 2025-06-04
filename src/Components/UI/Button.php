<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(public string $type = 'button', public string $variant = 'primary', public string $size = 'md') {}

    public function render(): View|string
    {
        return view('smart_cms::ui.button');
    }
}
