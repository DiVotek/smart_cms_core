<?php

namespace SmartCms\Core\Actions\Template;

use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\TemplateSection;

class BuildLayoutTemplate
{
    use AsAction;

    public function handle(array $defaultTemplate = []): array
    {
        $template = [];
        foreach ($defaultTemplate as $d) {
            $section = TemplateSection::query()->where('id', $d['template_section_id'])->first();
            if (! $section) {
                continue;
            }
            $sectionComponent = 'templates::'.template().'.sections';
            $design = $section->design;
            $design = explode('\\', $design);
            $design = $design[count($design) - 1];
            $design = preg_replace('/([a-z])([A-Z])/', '$1-$2', $design);
            $sectionComponent .= '.'.strtolower($design);
            $value = empty($d['value']) ? $section->value : $d['value'];
            if (! is_array($value)) {
                $value = [$value];
            }
            $template[] = [
                'component' => $sectionComponent,
                'schema' => $section->schema ?? [],
                'file' => $section->design,
                'options' => $value,
            ];
        }

        return $template;
    }
}
