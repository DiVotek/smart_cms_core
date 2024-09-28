<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;

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
