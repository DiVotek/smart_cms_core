<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Context;
use Illuminate\View\Component;
use SmartCms\Core\Models\Page;

class Description extends Component
{
    public string $description;

    public string $tag;

    public function __construct(array $options = [], $tag = 'div')
    {
        $this->tag = $tag;
        $isCustom = $options['is_custom'] ?? false;
        if ($isCustom) {
            $this->description = $options['description'] ?? '';
            return;
        }
        $entity = Context::get('entity');
        if (!$entity) {
            $entity = new Page();
        }
        $seo = $entity?->seo;
        $isDescription = $options['is_description'] ?? false;
        if ($isDescription) {
            $this->description = $seo->content ?? '';
            return;
        }
        $this->description = $seo->summary ?? '';
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
