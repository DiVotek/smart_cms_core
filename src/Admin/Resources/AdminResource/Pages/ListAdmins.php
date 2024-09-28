<?php

namespace SmartCms\Core\Admin\Resources\AdminResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\AdminResource;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
