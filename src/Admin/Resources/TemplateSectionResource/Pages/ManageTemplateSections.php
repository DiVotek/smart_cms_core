<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;

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
