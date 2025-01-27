<?php

namespace SmartCms\Core\Traits;

use Filament\Forms\Components\Section;
use SmartCms\Core\Services\Config;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Services\Schema\SchemaParser;

trait HasLayoutSettings
{
    public function getLayoutSettingsForm(): array
    {
        if (!$this->layout) {
            return [];
        }
        $path = $this->layout->path;
        $fields = [];
        $layouts = (new Config())->getLayouts();
        foreach($layouts as $layout) {
            if ($layout['path'] !== $path) {
                continue;
            }
            foreach($layout['schema'] as $field) {
                $fields[] =ArrayToField::make($field,'layout_settings.');
            }
            break;
        }
        $schema = [];
        foreach($fields as $field) {
            $schema = array_merge($schema,Builder::make($field));
        }
        return $schema;
    }

    public function parseLayoutSettings(): array
    {
        dd(123);
        if (!$this->layout_settings || !$this->layout) {
            return [];
        }

        $configFile = base_path('scms/templates/' . template() . '/layouts/' . $this->layout->path . '/config.php');
        if (!file_exists($configFile)) {
            return [];
        }

        $schema = include $configFile;
        return SchemaParser::make($schema['fields'] ?? [], $this->layout_settings);
    }
}
