<?php

namespace SmartCms\Core\Actions\Template;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\Schema\SchemaParser;

class BuildTemplate
{
    use AsAction;

    public array $result = [];

    public function handle(array $template): array
    {
        foreach ($template as $d) {
            $section = TemplateSection::query()->where('id', $d['template_section_id'])->first();
            if (! $section) {
                continue;
            }
            $sectionComponent = 'sections.' . $section->design;
            if (! empty($d['value'])) {
                $section->value = $d['value'];
            }
            if (! is_array($section->value)) {
                $section->value = [$section->value];
            }
            try {
                $variables = SchemaParser::make($section->schema, $section->value);
                $this->result[] = [
                    'component' => $sectionComponent,
                    'options' => $variables,
                ];
            } catch (Exception $e) {
                if (config('app.debug')) {
                    // dd($e->getMessage(), $e->getTrace(), $section, $d, $template);
                }

                continue;
            }
        }

        return $this->result;
    }
}
