<?php

namespace SmartCms\Core\Components\UI;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Gallery extends Component
{
    public function __construct(public array $images = [])
    {
    }

    public function render(): View|string
    {
        return view('smart_cms::ui.gallery');
    }
}
