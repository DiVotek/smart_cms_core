<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public function __construct(public string $label = 'Menu', public array $items = []) {}

    public function render(): View|string
    {
        return view('smart_cms::ui.dropdown');
    }
}
