<?php

namespace SmartCms\Core\Services\Frontend;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SmartCms\Core\Actions\ExtractSchemaDirective;
use SmartCms\Core\Actions\ParseSchemaDirective;
use SmartCms\Core\Models\Layout;

class LayoutService
{
    public static function make(): self
    {
        return new self;
    }

    public function getAll(): array
    {
        $sections = [];
        $bladeViews = collect(File::allFiles(resource_path('views/layouts')))
            ->filter(fn($file) => \Illuminate\Support\Str::endsWith($file->getFilename(), '.blade.php'))
            ->map(function ($file) {
                $relativePath = \Illuminate\Support\Str::after($file->getRealPath(), resource_path('views/layouts' . DIRECTORY_SEPARATOR));
                $bladePath = str_replace(['/', '\\'], '.', $relativePath);

                return \Illuminate\Support\Str::replaceLast('.blade.php', '', $bladePath);
            })
            ->values()
            ->toArray();
        foreach ($bladeViews as $file) {
            $metadata = $this->getSectionMetadata($file);

            if ($metadata) {
                $metadata['path'] = $file;

                $sections[$file] = $metadata;
            }
        }

        return $sections;
    }

    public function getSectionMetadata(string $sectionName): ?array
    {
        $sectionName = str_replace('.', '/', $sectionName);
        $metaFile = resource_path("views/layouts/meta/{$sectionName}.php");
        if (File::exists($metaFile)) {
            return require $metaFile;
        }

        $sectionFile = resource_path("views/layouts/{$sectionName}.blade.php");
        if (File::exists($sectionFile)) {
            $content = File::get($sectionFile);
            $section = $this->parseMetadataFromBladeComments($content);
            if ($section == null) {
                $section = [
                    'name' => $sectionName,
                    'schema' => [],
                ];
            }
            if (!isset($section['schema']) || blank($section['schema'])) {
                $section['schema'] = ExtractSchemaDirective::run($sectionFile);
            }
            return $section;
            // return $this->parseMetadataFromBladeComments($content);
        }

        return null;
    }

    protected function parseMetadataFromBladeComments(string $content): ?array
    {
        if (preg_match('/@section_meta\s*(.*?)\s*@endsection_meta/s', $content, $matches)) {
            $jsonData = trim($matches[1]);

            try {
                $metadata = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);
                return $metadata;
            } catch (\JsonException $e) {
                Log::error('Failed to parse section metadata: ' . $e->getMessage());
            }
        }

        return null;
    }

    public function getSectionFields(string $sectionName): array
    {
        $metadata = $this->getSectionMetadata($sectionName);

        if ($metadata && isset($metadata['fields'])) {
            return $metadata['fields'];
        }

        return [];
    }

    public function getSectionDefaults(string $sectionName): array
    {
        $fields = $this->getSectionFields($sectionName);
        $defaults = [];

        foreach ($fields as $field) {
            if (isset($field['default'])) {
                $defaults[$field['name']] = $field['default'];
            } else {
                switch ($field['type']) {
                    case 'text':
                    case 'textarea':
                    case 'wysiwyg':
                        $defaults[$field['name']] = '';
                        break;
                    case 'image':
                        $defaults[$field['name']] = null;
                        break;
                    case 'select':
                    case 'checkbox':
                        $defaults[$field['name']] = isset($field['options'][0]['value'])
                            ? $field['options'][0]['value']
                            : null;
                        break;
                    default:
                        $defaults[$field['name']] = null;
                }
            }
        }

        return $defaults;
    }

    public function validateSectionData(string $sectionName, array $data): array
    {
        $fields = $this->getSectionFields($sectionName);
        $errors = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            if (($field['required'] ?? false) && empty($data[$fieldName])) {
                $errors[$fieldName] = "Поле '{$field['label']}' обязательно для заполнения";
            }
        }

        return $errors;
    }

    public function renderSection(string $sectionName, array $data = []): string
    {
        $defaults = $this->getSectionDefaults($sectionName);

        $mergedData = array_merge($defaults, $data);

        return view("sections.{$sectionName}", $mergedData)->render();
    }

    public function init(): void
    {
        $sections = $this->getAll();
        foreach ($sections as $section) {
            $sectionModel = Layout::query()->withoutGlobalScopes()->where('path', $section['path'])->first();
            if ($sectionModel) {
                $sectionModel->update([
                    'schema' => $section['schema'] ?? [],
                ]);
            } else {
                Layout::query()->create([
                    'name' => ucfirst($section['name']),
                    'path' => $section['path'],
                    'schema' => $section['schema'] ?? [],
                    'value' => [],
                ]);
            }
        }
    }
}
