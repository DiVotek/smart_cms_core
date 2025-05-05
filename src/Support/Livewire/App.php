<?php

namespace SmartCms\Core\Support\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use SmartCms\Core\Support\Actions\ActionRegistry;
use SmartCms\Core\Traits\HasHooks;

abstract class App extends Component
{
    use HasHooks;

    public array $formData = [];

    public array $data = [];

    protected array $properties = [];
    public function notify(string $message, string $type = 'success')
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function callAction(string $action, array $params = [])
    {
        $action = ActionRegistry::resolve($action, $params, $this);
        if ($action) {
            $action->handle();
        }
    }
    abstract protected function getView(): string;
    protected function prepareData(): array
    {
        return [];
    }
    public function render()
    {
        $view = $this->getView();
        $data = $this->prepareData();
        $this->applyHook('render', $data);
        return view($view, $data);
    }
}
