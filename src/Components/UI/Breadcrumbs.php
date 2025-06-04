<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public function __construct(public array $items = []) {}

    public function render(): View|string
    {
        return view('smart_cms::ui.breadcrumbs');
    }
}
