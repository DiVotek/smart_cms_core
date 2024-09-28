<?php

namespace SmartCms\Core\Admin\Resources\SeoResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SmartCms\Core\Admin\Resources\SeoResource;

class ListSeos extends ListRecords
{
    protected static string $resource = SeoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
