<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Core\Services\Schema;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make(_actions('save_as_nav'))->requiresConfirmation()->hidden(function (): bool {
                return $this->record->is_nav;
            })->action(function (Model $record) {
                $record->update(['is_nav' => true]);
            }),
            Action::make(_actions('save_as_not_nav'))->requiresConfirmation()->hidden(function (): bool {
                return ! $this->record->is_nav;
            })->action(function (Model $record) {
                $record->update(['is_nav' => false]);
            })->color('danger'),
            Action::make(_actions('nav_settings'))->form(function ($form) {
                return $form->schema([
                    Schema::getTemplateBuilder('nav_settings.template'),
                    Schema::getRepeater('nav_settings.fields')->schema([
                        TextInput::make('name')->label(_fields('name'))->required(),
                    ]),
                ]);
            })->fillForm(fn ($record): array => [
                'nav_settings' => $record->nav_settings ?? [],
            ])->modal()->action(function (Model $record, $data) {
                $record->update($data);
            })->hidden(function (): bool {
                return ! $this->record->is_nav;
            }),
        ];
    }
}
