<?php

namespace SmartCms\Core\Livewire;

use SmartCms\Core\Models\Layout;
use SmartCms\Core\Support\Livewire\App as LivewireApp;

class Footer extends LivewireApp
{
    public ?Layout $layout =  null;

    public function mount()
    {
        $this->layout = Layout::query()->where('id', _settings('layouts.footer'))->first();
    }

    protected function prepareData(): array
    {
        if ($this->layout) {
            return $this->layout->getVariables();
        }
        return [];
    }

    protected function getView(): string
    {
        if ($this->layout) {
            return 'layouts.' . $this->layout->path;
        }
        return 'smart_cms::layouts.footer';
    }
}
