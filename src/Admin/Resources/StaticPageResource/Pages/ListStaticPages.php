<?php

namespace SmartCms\Core\Admin\Resources\StaticPageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\StaticPageResource;

class ListStaticPages extends ListRecords
{
    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
