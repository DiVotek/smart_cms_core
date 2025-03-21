<?php

namespace SmartCms\Core\Admin\Resources\TemplateSectionResource\Pages;

use SmartCms\Core\Admin\Base\Pages\BaseEditRecord;
use SmartCms\Core\Admin\Resources\TemplateSectionResource;
use SmartCms\Core\Services\Frontend\SectionService;

class EditTemplateSection extends BaseEditRecord
{
    protected static string $resource = TemplateSectionResource::class;

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $components = SectionService::make()->getAllSections();
    //     $schema = [];
    //     foreach ($components as $name => $component) {
    //         if ($name == $data['design']) {
    //             $schema = $component['schema'];
    //             break;
    //         }
    //     }
    //     $data['schema'] = $schema;
    //     $data['template'] = template();

    //     return $data;
    // }
}
