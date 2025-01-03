<?php

namespace SmartCms\Core\Components\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildTemplate;
use SmartCms\Core\Models\Seo;

class PageComponent extends Component
{
    public string $title;

    public string $meta_description;

    public string $meta_keywords;

    public array $template = [];

    public array $breadcrumbs = [];

    public array $microdata = [];

    public mixed $entity;

    public array $dataLayer = [];

    public string $og_image;

    public function __construct(Model $entity, string $component, array $microdata = [], array $defaultTemplate = [], array $dataLayer = [])
    {
        $titleMod = _settings('title_mod', []);
        $descriptionMod = _settings('description_mod', []);
        $seo = $entity?->seo ?? new Seo;
        $this->title = ($titleMod->prefix ?? '').($seo->title ?? '').($titleMod->suffix ?? '');
        $this->meta_description = ($descriptionMod->prefix ?? '').($seo->description ?? '').($descriptionMod->suffix ?? '');
        $this->meta_keywords = $seo->meta_keywords ?? '';
        $this->breadcrumbs = method_exists($entity, 'getBreadcrumbs') ? $entity->getBreadcrumbs() : [];
        $this->template = BuildTemplate::run($entity, $component, $defaultTemplate);
        $this->microdata = $microdata;
        Context::add('entity', $entity);
        if (method_exists($entity, 'view')) {
            $entity->view();
        }
        $this->entity = $entity;
        $this->dataLayer = $dataLayer;
        $this->og_image = _settings('og_image', logo());
    }

    public function render()
    {
        return <<<'blade'
            <x-s::layout.layout>
                @section("title", $title)
                @section('description', $meta_description)
                @section('keywords', $meta_keywords)
                @section("content")
                @section('meta-image',asset($entity->image ?? $og_image))
                <x-s::layout.builder :data="$template" />
                @endsection
                @if(count($dataLayer) > 0)
                <script>
                    window.dataLayer = window.dataLayer || [];
                    window.dataLayer.push(@json($dataLayer));
                </script>
                @endif
            </x-s::layout.layout>
        blade;
    }
}
