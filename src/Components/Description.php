<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Context;
use Illuminate\View\Component;

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
        if (! $entity) {
            $this->description = '';
        } else {
            if (isset($options['is_description']) && $options['is_description']) {
                $this->description = $entity->content ?? '';
            } else {
                $this->description = $entity->summary ?? '';
            }
        }
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
