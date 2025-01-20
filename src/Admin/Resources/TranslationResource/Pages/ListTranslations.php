<?php

namespace SmartCms\Core\Admin\Resources\TranslationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Models\Translation;
use SmartCms\Core\Services\Config;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make(_hints('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalDescription(_actions('translations_help'))
                ->modalFooterActions([]),
            Actions\Action::make(_actions('clear_cache'))
                ->icon('heroicon-m-arrow-path')
                ->label(_actions('clear_cache'))
                ->action(function () {
                    cache()->forget('translations');
                    Notification::make()
                        ->title(_actions('cleared_tranlations'))
                        ->success()
                        ->send();
                }),
            Actions\Action::make('create_from_tpl')
                ->icon('heroicon-o-plus-circle')
                ->label(_actions('create_from_tpl'))->action(function () {
                    $config = new Config;
                    $config->initTranslates();
                    Notification::make()->title(_actions('success'))->success()->send();
                }),
            Actions\Action::make('reset')
                ->icon('heroicon-o-x-circle')
                ->label(_actions('reset'))->action(function () {
                    $translations = Translation::query()->get();
                    foreach ($translations as $translation) {
                        $translation->update(['value' => $translation->key]);
                    }
                    Notification::make()->title(_actions('success'))->success()->send();
                }),
        ];
    }
}
