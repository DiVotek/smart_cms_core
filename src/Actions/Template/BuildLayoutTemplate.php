<?php

namespace SmartCms\Core\Actions\Template;

use Illuminate\Support\Facades\Context;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;

class BuildLayoutTemplate
{
    use AsAction;

    public function handle(array $defaultTemplate = []): array
    {
        $template = [];
        $service = new BuildTemplate;
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
            $entity = Context::get('entity', new Page);
            $template[] = [
                'component' => $sectionComponent,
                'options' => array_merge(
                    $service->getDefaultVariables(),
                    $service->parseSection($section->schema, $value, $entity),
                ),
            ];
        }

        return $template;
    }
}
