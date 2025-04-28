<?php

namespace SmartCms\Core\Livewire;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Event;
use SmartCms\Core\Actions\Template\BuildTemplate;
use SmartCms\Core\Microdata\BlogArticle;
use SmartCms\Core\Microdata\Breadcrumbs;
use SmartCms\Core\Microdata\NewsArticle;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Page as ModelsPage;
use SmartCms\Core\Resources\PageEntityResource;
use SmartCms\Core\Support\Livewire\App;

class Page extends App
{
    public ModelsPage $page;

    public ?Layout $pageLayout;

    public function mount(ModelsPage $page)
    {
        $this->page = $page;
        $this->pageLayout = Layout::find($page->layout_id);
        $this->setSeo();
        $this->setTemplate();
    }

    public function render()
    {
        $resource = null;
        Event::dispatch('cms.page.construct', [&$resource, $this->page]);
        if (! $resource) {
            $resource = PageEntityResource::make($this->page)->get();
        }
        $this->setMicrodata($resource);
        Context::add('entity', $resource);
        $temp = $this->page->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
        app('seo')->title($resource->title)->description($resource->meta_description);
        app('template')->template(BuildTemplate::run($temp));
        if (! $this->pageLayout) {
            return '<div></div>';
        }

        return view('layouts.' . $this->pageLayout->path, [
            ...$this->pageLayout->getVariables($this->page->layout_settings ?? []),
            'entity' => $resource,
        ]);
    }

    private function setMicrodata($resource)
    {
        Breadcrumbs::make($this->page->getBreadcrumbs());
        if ($resource->parent) {
            if ($resource->parent->parent) {
                BlogArticle::make((array) $resource);
            } else {
                NewsArticle::make((array) $resource);
            }
        }
    }

    private function setSeo() {}

    private function setTemplate() {}
}
