<?php

namespace SmartCms\Core\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Notifications extends Component
{
    public string $viewTemplate;

    public array $notifications;

    public function __construct()
    {
        $this->notifications = session('notifications', []);
        $this->viewTemplate = view()->exists('templates::'.template().'.notifications') ? 'templates::'.template().'.notifications' : '';
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
         <div hx-trigger="new-notification from:body" hx-swap="outerHTML" hx-get="{{route('notifications.list')}}" {{$attributes}}>
            @if($viewTemplate)
               @include($viewTemplate, ['notifications' => $notifications])
            @endif
         </div>
        blade;
    }
}
