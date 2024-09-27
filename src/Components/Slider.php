<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Slider extends Component
{
    public int $slides;

    public bool $pagination;

    public bool $navigation;

    public string $gap;

    public int $autoplay;

    public string $title;

    public function __construct(int $slides = 3, bool $pagination = true, bool $navigation = true, int $gap = 0, int $autoplay = 0, string $title = 'Slider')
    {
        $this->slides = $slides;
        $this->pagination = $pagination;
        $this->navigation = $navigation;
        $this->gap = $gap / 16 . 'rem';
        $this->autoplay = $autoplay;
        $this->title = $title;
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
                <div {{ $attributes->merge(['class' => 'splide', 'role' => 'group', 'aria-label' => $title]) }} data-slides-per-view="{{$slides}}" data-pagination="{{$pagination}}" data-navigation="{{$navigation}}" data-gap="{{$gap}}" data-autoplay="{{$autoplay}}">
                    @isset($arrows)
                        {{ $arrows }}
                    @endisset
                    <div class="splide__track">
                        <ul class="splide__list">
                            {{$slot}}
                        </ul>
                    </div>
                </div>
        blade;
    }
}
