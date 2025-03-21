<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Admin\Base\Pages\BaseCreateRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Frontend\SectionService;

class CreateTemplateSection extends BaseCreateRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $components = SectionService::make()->getAllSections();
        $schema = [];
        foreach ($components as $name => $component) {
            if ($name == $data['design']) {
                $schema = $component['schema'];
                break;
            }
        }
        $data['schema'] = $schema;
        $data['template'] = template();

        return parent::handleRecordCreation($data);
    }
}
