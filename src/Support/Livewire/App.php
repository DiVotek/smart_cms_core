<?php

namespace SmartCms\Core\Support\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use SmartCms\Core\Support\Actions\ActionRegistry;

abstract class App extends Component
{
    use WithPagination;

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
}
