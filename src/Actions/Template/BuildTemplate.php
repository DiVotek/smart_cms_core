<?php

namespace SmartCms\Core\Actions\Template;

use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\TemplateSection;

class BuildTemplate
{
    use AsAction;

    public function handle(Model $entity, string $component, array $defaultTemplate = []): array
    {
        $template = [];
        $entityTemplate = $entity->template()->select([
            'template_section_id',
            'value',
        ])->get()->toArray();
        if (empty($entityTemplate)) {
            $entityTemplate = $defaultTemplate;
        }
        foreach ($entityTemplate as $d) {
            $section = TemplateSection::query()->where('id', $d['template_section_id'])->first();
            if (! $section) {
                continue;
            }
            if ($section->name != 'Page content') {
                $sectionComponent = 'templates::modules';
                $design = $section->design;
                $design = explode('\\', $design);
                $design = $design[count($design) - 1];
                $design = preg_replace('/([a-z])([A-Z])/', '$1-$2', $design);
                $sectionComponent .= '.'.strtolower($design);
            } else {
                $sectionComponent = $component;
            }
            $value = empty($d['value']) ? $section->value : $d['value'];
            if (! is_array($value)) {
                $value = [$value];
            }
            $template[] = [
                'is_mobile' => true,
                'is_desktop' => true,
                'component' => $sectionComponent,
                'options' => [...$value, ...[
                    'entity' => $entity,
                    'breadcrumbs' => $entity->getBreadcrumbs(),
                ]],
            ];
        }

        return $template;
    }
}
