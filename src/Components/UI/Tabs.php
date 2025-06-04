<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tabs extends Component
{
    public function __construct(public array $tabs = [])
    {
    }

    public function render(): View|string
    {
        return view('smart_cms::ui.tabs');
    }
}
