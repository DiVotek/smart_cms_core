<?php

namespace SmartCms\Core\Traits;

use SmartCms\Core\Services\Config;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;

trait HasLayoutSettings
{
    public function getLayoutSettingsForm(): array
    {
        if (! $this->layout) {
            return [];
        }
        $path = $this->layout->path;
        $fields = [];
        $layouts = (new Config)->getLayouts();
        foreach ($layouts as $layout) {
            if ($layout['path'] !== $path) {
                continue;
            }
            foreach ($layout['schema'] as $field) {
                $fields[] = ArrayToField::make($field, 'layout_settings.');
            }
            break;
        }
        $schema = [];
        foreach ($fields as $field) {
            $schema = array_merge($schema, Builder::make($field));
        }

        return $schema;
    }
}
