<?php

namespace SmartCms\Core\Support\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use SmartCms\Core\Actions\Template\BuildTemplate;
use SmartCms\Core\Microdata\Breadcrumbs;
use SmartCms\Core\Models\Layout;

abstract class Page extends App
{
    public Model $model;

    protected ?Layout $pageLayout;

    public static ?object $entity = null;

    use WithPagination;

    abstract protected function getEntity(): object;

    protected function setMicrodata(): void
    {
        Breadcrumbs::make($this->model->getBreadcrumbs());
    }

    protected function setLayout(): void
    {
        $this->pageLayout = null;
    }

    protected function getSeo(): array
    {
        $seo = $this->model->getSeo();
        $entity = $this->entity();

        return [
            'title' => blank($seo->title) ? $entity->name : $seo->title,
            'description' => $seo->description ?? '',
            'image' => blank($seo->image) ? $this->model->image ?? $entity->image ?? logo() : $seo->image,
        ];
    }

    #[Computed(persist: true)]
    protected function entity()
    {
        $entity = self::$entity;
        if (! $entity) {
            $entity = $this->getEntity();
            $this->applyHook('entity', $entity);
            Context::add('entity', $entity);
            self::$entity = $entity;
        }

        return $entity;
    }

    protected function getTemplate(): array
    {
        return $this->model->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
    }

    public function render()
    {
        $this->setLayout();
        $data = $this->prepareData();
        if ($this->pageLayout) {
            $data = array_merge($data, $this->pageLayout->getVariables($this->model?->layout_settings ?? []));
        }
        $this->applyHook('render', $data);
        $viewName = $this->getView();
        $viewData = [
            'entity' => $this->entity(),
            ...$data,
        ];
        $template = $this->getTemplate();
        $this->setMicrodata();
        app('template')->template(BuildTemplate::run($template));
        $seo = $this->getSeo();
        app('seo')->title($seo['title'] ?? '')->description($seo['description'] ?? '')->keywords($seo['keywords'] ?? '');
        if (view()->exists($viewName)) {
            return view($viewName, $viewData);
        }

        return view('smart_cms::layouts.default-page', $viewData);
    }
}
