<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Description extends Component
{
    public string $description;

    public string $tag;

    public function __construct(array $options = [], $tag = 'div')
    {
        $description = $options[current_lang()]['description'] ?? '';
        if (isset($options['use_page_description']) && $options['use_page_description']) {
            $description = $options['entity']->seo->content ?? '';
        }
        if (isset($options['use_page_summary']) && $options['use_page_summary']) {
            $description = $options['entity']->seo->summary ?? '';
        }
        $this->description = $description;
        $this->tag = $tag;
    }

    public function render(): View|Closure|string
    {
        if ($this->tag == 'p') {
            return <<<'blade'
                <p {{ $attributes}} >
                    {!! $description !!}
                </p>
            blade;
        } elseif ($this->tag == 'span') {
            return <<<'blade'
                <span {{ $attributes}} >
                    {!! $description !!}
                </span>
            blade;
        } else {
            return <<<'blade'
            <div {{ $attributes}} >
                {!! $description !!}
            </div>
        blade;
        }
    }
}
