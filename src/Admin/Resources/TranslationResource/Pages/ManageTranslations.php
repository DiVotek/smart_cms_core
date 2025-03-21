<?php

namespace SmartCms\Core\Admin\Resources\TranslationResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use SmartCms\Core\Admin\Base\Pages\BaseManageRecords;
use SmartCms\Core\Admin\Resources\TranslationResource;
use SmartCms\Core\Models\Translation;

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
                    $translates = config('translates', []);
                    if (! is_array($translates)) {
                        $translates = [];
                    }
                    foreach ($translates as $key => $value) {
                        if (! is_string($value)) {
                            $value = '';
                        }
                        foreach (get_active_languages() as $lang) {
                            if (! Translation::query()->where('language_id', $lang->id)->where('key', $key)->exists()) {
                                Translation::query()->create([
                                    'language_id' => $lang->id,
                                    'key' => $key,
                                    'value' => $value,
                                ]);
                            }
                        }
                    }
                    Notification::make()
                        ->title(_actions('updated_translates'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
