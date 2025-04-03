<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Builder extends Component
{
    public array $template;

    public static $isRendered = false;

    public function __construct(array $data = [])
    {
        $this->template = self::$methodCache['template'] ?? [];
        // $this->template = $data;
    }

    public function render(): View|Closure|string
    {
        if (self::$isRendered) {
            return '';
        }
        self::$isRendered = true;

        return <<<'blade'
            <div class="builder">
                @foreach ($template as $key => $field)
                    @include($field['component'], $field['options'])
                @endforeach
            </div>
        blade;
    }
}
