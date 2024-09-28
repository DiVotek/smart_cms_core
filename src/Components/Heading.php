<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Heading extends Component
{
    public string $title;

    public string $size;

    public string $type;

    public function __construct(array $options = [])
    {
        $title = $options[main_lang()]['title'] ?? '';
        if (isset($options['use_page_heading']) && $options['use_page_heading']) {
            $title = $options['entity']->seo->heading ?? $options['entity']->name();
        }
        if (isset($options['use_page_name']) && $options['use_page_name']) {
            $title = $options['entity']->name();
        }
        $size = $options['heading_size'] ?? 'text-md';
        $type = $options['heading_type'] ?? 'none';
        $this->title = $title;
        $this->size = $size;
        $this->type = $type;
    }

    public function render(): View|Closure|string
    {
        if ($this->type == 'h1') {
            return <<<'blade'
                <h1 {{$attributes->merge(['class' => $size])}} >
                    {{ $title }}
                </h1>
            blade;
        }
        if ($this->type == 'h2') {
            return <<<'blade'
                <h2 {{ $attributes->merge(['class' => $size])}} >
                    {{ $title }}
                </h2>
            blade;
        }
        if ($this->type == 'h3') {
            return <<<'blade'
                <h3 {{ $attributes->merge(['class' => $size])}} >
                    {{ $title }}
                </h3>
            blade;
        }

        return <<<'blade'
            <div {{ $attributes->merge(['class' => $size])}} >
                {{ $title }}
            </div>
        blade;
    }
}
