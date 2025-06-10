<?php

namespace SmartCms\Core\Livewire;

use SmartCms\Core\Extenders\Pages\PageExtender;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\MenuSection;
use SmartCms\Core\Resources\PageEntityResource;
use SmartCms\Core\Support\Livewire\Page as LivewirePage;

class Page extends LivewirePage
{
    public static ?string $extender = PageExtender::class;

    public function setLayout(): void
    {
        $this->pageLayout = Layout::find($this->model->layout_id);
    }

    public function getView(): string
    {
        return 'layouts.' . $this->pageLayout?->path;
    }

    public function getEntity(): object
    {
        return PageEntityResource::make($this->model)->get();
    }

    protected function getTemplate(): array
    {
        $template = $this->model->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
        if (empty($template)) {
            $parent = $this->model->parent;
            if (!$parent) {
                return [];
            }
            $parent_id = $parent->parent_id ?? $parent->id;
            $menuSection = MenuSection::query()->where('parent_id', $parent_id)->first();
            if ($menuSection) {
                if ($parent->parent_id) {
                    return $menuSection->template ?? [];
                } else {
                    return $menuSection->categories_template ?? [];
                }
            }
            return [];
        }
        return $template;
    }
}
