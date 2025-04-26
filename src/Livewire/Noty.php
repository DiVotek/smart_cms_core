<?php

namespace SmartCms\Core\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Noty extends Component
{
    public array $notifications = [];

    #[On('notify')]
    public function notify(string $message, string $type = 'success')
    {
        $id = uniqid();
        $this->notifications[] = [
            'id' => $id,
            'message' => $message,
            'type' => $type,
        ];

        $this->dispatch('notification-added', ['id' => $id]);
    }

    public function dismiss(string $id)
    {
        $this->notifications = array_filter($this->notifications, fn ($n) => $n['id'] !== $id);
    }

    public function render()
    {
        if (view()->exists('livewire.noty')) {
            return view('livewire.noty');
        }

        return view('smart_cms::livewire.noty');
    }
}
