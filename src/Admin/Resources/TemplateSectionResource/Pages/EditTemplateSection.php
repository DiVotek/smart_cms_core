<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Helper;

class EditTemplateSection extends EditRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $schema = Helper::getComponentSchema($data['design']);
        $data['schema'] = $schema;
        // dd($data);
        return $data;
    }
}
