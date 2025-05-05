<?php

namespace SmartCms\Core\Livewire;

use Livewire\WithPagination;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Support\Livewire\App as LivewireApp;

class Header extends LivewireApp
{
    use WithPagination;
    public ?Layout $layout =  null;

    public function mount()
    {
        $this->layout = Layout::query()->where('id', _settings('layouts.header'))->first();
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
        return 'smart_cms::layouts.header';
    }

    protected function getListeners()
    {
        return [
            'refresh'
        ];
    }
}
