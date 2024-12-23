<?php

namespace SmartCms\Core\Admin\Resources\AdminResource\Pages;

use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\AdminResource;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make(_actions('save_close'))
                ->label('Save & Close')
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function ($record, $data) {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListAdmins::getUrl());
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
}
