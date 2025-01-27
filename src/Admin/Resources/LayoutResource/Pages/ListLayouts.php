<?php

namespace SmartCms\Core\Admin\Resources\LayoutResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\LayoutResource;

class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('help')
                ->label(_actions('help'))
                ->iconButton()
                ->icon('heroicon-o-question-mark-circle')
                ->modalHeading(_fields('layout_help'))
                ->modalDescription(_hints('help.layout'))
                ->modalFooterActions([
                    Actions\Action::make('close')
                        ->label(_actions('close'))
                        ->modalSubmitAction(false),
                ]),
            Actions\CreateAction::make()
                ->label(_actions('create')),
        ];
    }
}
