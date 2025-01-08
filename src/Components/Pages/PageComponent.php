<?php

namespace SmartCms\Core\Components\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildTemplate;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Repositories\Page\PageEntityDto;

class PageComponent extends Component
{
    public string $title;

    public string $meta_description;

    public string $meta_keywords;

    public array $template = [];

    public array $breadcrumbs = [];

    public array $microdata = [];

    public mixed $entity;

    public string $og_image;

    public ?Layout $layout;

    public DtoInterface $dto;

    public function __construct(Model $entity, string $component, array $microdata = [], array $defaultTemplate = [])
    {
        $titleMod = _settings('title_mod', []);
        $descriptionMod = _settings('description_mod', []);
        $seo = $entity?->seo ?? new Seo;
        $this->title = ($titleMod->prefix ?? '').($seo->title ?? '').($titleMod->suffix ?? '');
        $this->meta_description = ($descriptionMod->prefix ?? '').($seo->description ?? '').($descriptionMod->suffix ?? '');
        $this->meta_keywords = $seo->meta_keywords ?? '';
        $this->breadcrumbs = method_exists($entity, 'getBreadcrumbs') ? $entity->getBreadcrumbs() : [];
        $temp = $entity->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
        if (empty($temp)) {
            $temp = $defaultTemplate;
        }
        $this->template = BuildTemplate::run($temp);
        $this->microdata = $microdata;
        Context::add('entity', $entity);
        if (method_exists($entity, 'view')) {
            $entity->view();
        }
        $layout = Layout::find($entity->layout_id);
        $this->layout = $layout;
        $this->entity = $entity;
        $this->og_image = _settings('og_image', logo());
        Event::dispatch('cms.page.construct', $this);
        if (! isset($this->dto)) {
            $seo = $entity->seo ?? new Seo;
            $this->dto = PageEntityDto::factory($entity->name(), $entity->image ?? logo(), $entity->created_at, $entity->updated_at, $entity->getBreadcrumbs(), $seo->title ?? '', $seo->heading ?? '', $seo->summary ?? '', $seo->content ?? '', $entity->banner ?? '');
        }
        Event::dispatch('cms.page.constructed', [&$this->dto]);
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
                @if($layout)
                @include('template::layouts.'.$layout->path, [...$layout->getVariables(),'entity' => $dto->toObject()])
                @endif
                <x-s::layout.builder :data="$template" />
                @endsection
            </x-s::layout.layout>
        blade;
    }
}
