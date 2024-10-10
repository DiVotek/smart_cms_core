<?php

namespace SmartCms\Core\Admin\Resources\MenuSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\MenuSectionResource;

class ListMenuSections extends ListRecords
{
    protected static string $resource = MenuSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
