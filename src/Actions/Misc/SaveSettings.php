<?php

namespace SmartCms\Core\Actions\Misc;

use Filament\Notifications\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveSettings
{
    use AsAction;

    public function handle()
    {
        Notification::make()
            ->title(_actions('saved_settings'))
            ->success()
            ->send();
    }
}
