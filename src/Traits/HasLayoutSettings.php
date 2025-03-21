<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Services\Frontend\LayoutService;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;

trait HasLayoutSettings
{
    public function getLayoutSettingsForm(): array
    {
        if (! $this->layout) {
            return [];
        }
        $fields = [];
        $metadata = LayoutService::make()->getSectionMetadata($this->layout->path);
        if (!$metadata || !is_array($metadata) || !isset($metadata['schema'])) {
            return [];
        }
        foreach ($metadata['schema'] as $field) {
            $fields[] = ArrayToField::make($field, 'layout_settings.');
        }
        $schema = [];
        foreach ($fields as $field) {
            $schema = array_merge($schema, Builder::make($field));
        }

        return $schema;
    }
}
