<?php

namespace SmartCms\Core\Components\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\BuildTemplate;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Resources\PageEntityResource;
use SmartCms\Core\Traits\HasHooks;

class PageComponent extends Component
{
    use HasHooks;
    public string $title;

    public string $meta_description;

    public string $meta_keywords;

    public array $template = [];

    public string $og_image;

    public ?Layout $layout;

    public $resource;

    public array $breadcrumbs;

    public function __construct(Model $entity)
    {
        $resource = null;
        $this->applyHook('cms.page.construct', $resource, $entity);
        if (!$resource) {
            $resource = PageEntityResource::make($entity)->get();
        }
        $this->resource = $resource;
        $breadcrumbs = $this->resource->breadcrumbs ?? [];
        $this->breadcrumbs = array_map(fn($breadcrumb) => (array) $breadcrumb, $breadcrumbs);
        $this->title = $this->resource->title ?? $this->resource->name;
        $this->meta_description = $this->resource->meta_description ?? '';
        $this->meta_keywords = $seo->meta_keywords ?? '';
        $temp = $entity->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
        $this->template = BuildTemplate::run($temp);
        Context::add('entity', $this->resource);
        if (method_exists($entity, 'view')) {
            $entity->view();
        }
        $layout = Layout::find($entity->layout_id);
        $this->layout = $layout;
        if ($this->resource->image) {
            $og_image = $this->resource->image;
        } else {
            $og_image = _settings('og_image', logo());
        }
        $this->og_image = validateImage($og_image);
    }

    public function render()
    {
        return <<<'blade'
            <x-s::layout.layout>
                @section("title", $title)
                @section('description', $meta_description)
                @section('keywords', $meta_keywords)
                @section("content")
                @section('meta-image',$og_image)
                @section('microdata')
                    @isset($resource->breadcrumbs)
                        @if(count($resource->breadcrumbs) > 1)
                        <x-s::microdata.breadcrumbs :data="$resource->breadcrumbs" />
                        @endif
                        @if($resource->parent)
                            @if($resource->parent->parent)
                            <x-s::microdata.blog-article :entity="$resource" />
                            @else
                            <x-s::microdata.news-article :entity="$resource" />
                            @endif
                        @endif
                    @endisset
                @endsection
                @if($layout)
                @include('template::layouts.'.$layout->path, [...$layout->getVariables($entity->layout_settings ?? []),'entity' => $resource])
                @endif
                <x-s::layout.builder :data="$template" />
                @endsection
            </x-s::layout.layout>
        blade;
    }
}
