<?php

namespace SmartCms\Core\Components\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildTemplate;

class PageComponent extends Component
{
    public string $title;

    public string $meta_description;

    public string $meta_keywords;

    public array $template = [];

    public array $breadcrumbs = [];

    public array $microdata = [];

    public mixed $entity;

    public function __construct(Model $entity, string $component, array $microdata = [], array $defaultTemplate = [])
    {
        $titleMod = setting(config('settings.title_mod'), []);
        $descriptionMod = setting(config('settings.description_mod'), []);
        $this->title = ($titleMod->prefix ?? '') . ($entity?->seo->title ?? '') . ($titleMod->suffix ?? '');
        $this->meta_description = ($descriptionMod->prefix ?? '') . ($entity?->seo->description ?? '') . ($descriptionMod->suffix ?? '');
        $this->meta_keywords = $entity?->seo->meta_keywords ?? '';
        $this->breadcrumbs = method_exists($entity, 'getBreadcrumbs') ? $entity->getBreadcrumbs() : [];
        $this->template = BuildTemplate::run($entity, $component, $defaultTemplate);
        $this->microdata = $microdata;
        if (method_exists($entity, 'view')) {
            $entity->view();
        }
        $this->entity = $entity;
    }

    public function render()
    {
        return <<<'blade'
            <x-layout>
                @section("title", $title)
                @section('description', $meta_description)
                @section('keywords', $meta_keywords)
                @section("content")
                @section('meta-image',asset($entity->image ?? logo()))
                <x-core.builder :data="$template" />
                @if (module_enabled('Order'))
                <livewire:order::cart-component />
                @endif
                @livewire('notifications')
                @livewire('feedback-component')
                @endsection
            </x-layout>
        blade;
    }
}
