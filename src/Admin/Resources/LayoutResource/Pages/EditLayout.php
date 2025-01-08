<?php

namespace SmartCms\Core\Admin\Resources\LayoutResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\LayoutResource;
use SmartCms\Core\Services\Config;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make('update_schema')->label('Update Schema')->icon('heroicon-o-arrow-path')->action(function () {
                $config = new Config;
                $config->initLayout($this->record->path);
                Notification::make()->title(_actions('success'))->success()->send();
            }),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function () {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListLayouts::getUrl());
                }),
            $this->getSaveFormAction()
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->save();
                    $this->record->touch();
                })
                ->formId('form'),
        ];
    }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     dd($data);
    // }
}
