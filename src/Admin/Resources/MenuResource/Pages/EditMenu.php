<?php

namespace SmartCms\Core\Admin\Resources\MenuResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\MenuResource;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make()
                ->label(_actions('delete'))
                ->icon('heroicon-o-x-circle'),
            \Filament\Actions\Action::make('save_close')
                ->label(_actions('save_close'))
                ->icon('heroicon-o-check-badge')
                ->formId('form')
                ->action(function ($record, $data) {
                    $this->save(true, true);
                    $this->record->touch();

                    return redirect()->to(ListMenus::getUrl());
                }),
            $this->getSaveFormAction()
                ->label(_actions('save'))
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->touch();
                })
                ->formId('form'),
        ];
    }
}
