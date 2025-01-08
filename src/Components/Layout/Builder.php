<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Builder extends Component
{
    public array $template;

    public function __construct(array $data = [])
    {
        $this->template = $data;
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <div class="builder">
                @foreach ($template as $key => $field)
                    @include($field['component'], $field['options'])
                @endforeach
            </div>
        blade;
    }
}
