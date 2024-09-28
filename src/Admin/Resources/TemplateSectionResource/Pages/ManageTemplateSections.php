<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTemplateSections extends ManageRecords
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
