<?php

namespace SmartCms\Core\Admin\Resources\TranslationResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use SmartCms\Core\Admin\Base\Pages\BaseManageRecords;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Services\TranslationService;

class ManageTranslations extends BaseManageRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getResourceHeaderActions(): array
    {
        return [
            Action::make('update_translates')
                ->label(_actions('update_translates'))
                ->icon('heroicon-o-arrow-path')
                ->iconic()
                ->action(function () {
                    TranslationService::run();
                    Notification::make()
                        ->title(_actions('updated_translates'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
