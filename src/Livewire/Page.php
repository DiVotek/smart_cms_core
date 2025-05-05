<?php

namespace SmartCms\Core\Livewire;

use SmartCms\Core\Models\Layout;
use SmartCms\Core\Resources\PageEntityResource;
use SmartCms\Core\Support\Livewire\Page as LivewirePage;

class Page extends LivewirePage
{
    public function setLayout(): void
    {
        $this->pageLayout = Layout::find($this->model->layout_id);
    }

    public function getView(): string
    {
        return 'layouts.'.$this->pageLayout?->path;
    }

    public function getEntity(): object
    {
        return PageEntityResource::make($this->model)->get();
    }
}
