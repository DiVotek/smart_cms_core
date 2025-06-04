<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(public bool $open = false, public ?string $title = null, public string $maxWidth = '32rem')
    {
    }

    public function render(): View|string
    {
        return view('smart_cms::ui.modal');
    }
}
