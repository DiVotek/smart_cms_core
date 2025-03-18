<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\Pages\BaseCreateRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Helper;

class CreateTemplateSection extends BaseCreateRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $schema = Helper::getComponentSchema($data['design'] ?? '');
        $data['schema'] = $schema;
        $data['template'] = template();

        return parent::handleRecordCreation($data);
    }
}
