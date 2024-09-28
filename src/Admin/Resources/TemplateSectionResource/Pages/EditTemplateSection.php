<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\edit;
use Filament\Resources\Pages\EditRecord;

class EditTemplateSection extends EditRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
