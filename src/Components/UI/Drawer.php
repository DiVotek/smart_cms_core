<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Drawer extends Component
{
    public function __construct(
        public bool $open = false,
        public string $maxWidth = 'max-w-md',
        public ?string $title = null
    ) {}

    public function render(): View|string
    {
        return view('smart_cms::ui.drawer');
    }
}
