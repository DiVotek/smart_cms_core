<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Helper;

class CreateTemplateSection extends CreateRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $schema = Helper::getComponentSchema($data['design']);
        $data['schema'] = $schema;

        return parent::handleRecordCreation($data);
    }
}
