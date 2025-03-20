<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Context;
use Illuminate\View\Component;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;

class Heading extends Component
{
    public string $title;

    public string $size;

    public string $type;

    public function __construct(array $options = [])
    {
        $title = $options['heading'] ?? '';
        if (isset($options['use_page_heading']) && $options['use_page_heading']) {
            $entity = Context::get('entity');
            if ($entity) {
                $title = $entity->heading ?? $entity->name ?? '';
            }
        }
        if (isset($options['use_page_name']) && $options['use_page_name']) {
            $entity = Context::get('entity');
            if ($entity) {
                $title = $entity->name();
            }
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
