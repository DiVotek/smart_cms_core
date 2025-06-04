<?php

namespace SmartCms\Core\Actions\Template;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Context;
use Lorisleiva\Actions\Concerns\AsAction;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\TemplateSection;
use SmartCms\Core\Services\VariableTypes;

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
        $defaultVariables = $this->getDefaultVariables();
        foreach ($entityTemplate as $d) {
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
            try {
                $vars = $this->parseSection($section->schema, $value, $entity);
            } catch (Exception $e) {
                if (config('app.debug')) {
                    dd($e->getMessage(), $section->schema, $value, $entity, $e->getTrace());
                }

                continue;
            }
            $template[] = [
                'component' => $sectionComponent,
                'options' => array_merge($vars, $defaultVariables, ['entity' => $entity]),
            ];
        }

        return $template;
    }

    public function getDefaultVariables(): array
    {
        $host = app('_page')->first();
        $entity = Context::get('entity', new Page);

        return [
            'logo' => asset('/storage'.logo()),
            'host' => $host?->route() ?? '',
            'hostname' => $host?->name() ?? '',
            'company_name' => company_name(),
            'language' => current_lang(),
            'entity' => $entity,
        ];
    }

    public function parseSection(array $schema, array $options, mixed $entity): array
    {
        if (empty($schema)) {
            return [];
        }
        $variables = [];
        foreach ($schema as $field) {
            if (! isset($field['type']) || ! isset($field['name'])) {
                throw new Exception('Field type or name is missing in schema');
            }
            try {
                $val = $options[current_lang()][$field['name']] ?? $options[main_lang()][$field['name']] ?? '';
            } catch (Exception $e) {
                dd($e->getMessage(), $schema, $options, $entity);
            }
            switch ($field['type']) {
                case VariableTypes::PHONE->value:
                    $variables[$field['name']] = phones()[$val];
                    break;
                case VariableTypes::PHONES->value:
                    $variables[$field['name']] = array_map(function ($phone) {
                        return phones()[$phone];
                    }, $val);
                    break;
                case VariableTypes::EMAIL->value:
                    $variables[$field['name']] = emails()[$val];
                    break;
                case VariableTypes::ADDRESS->value:
                    $variables[$field['name']] = addresses()[$val];
                    break;
                case VariableTypes::ADDRESSES->value:
                    $variables[$field['name']] = array_map(function ($address) {
                        return addresses()[$address];
                    }, $val);
                    break;
                case VariableTypes::SCHEDULE->value:
                    $variables[$field['name']] = schedules()[$val];
                    break;
                case VariableTypes::SCHEDULES->value:
                    $variables[$field['name']] = array_map(function ($schedule) {
                        return schedules()[$schedule];
                    }, $val);
                    break;
                case VariableTypes::SOCIALS->value:
                    $variables[$field['name']] = array_map(function ($social) {
                        return socials()[$social];
                    }, $val);
                    break;
                case VariableTypes::HEADING->value:
                    $variables[$field['name']] = [
                        'use_page_heading' => $options['use_page_heading'] ?? false,
                        'use_page_name' => $options['use_page_name'] ?? false,
                        'heading_type' => $options['heading_type'] ?? 'none',
                        'title' => $options[current_lang()]['title'] ?? $options[main_lang()]['title'],
                        'entity' => $options['entity'] ?? new Page,
                    ];
                    break;
                case VariableTypes::DESCRIPTION->value:
                    $variables[$field['name']] = [
                        'use_page_description' => $options['use_page_description'] ?? false,
                        'use_page_summary' => $options['use_page_summary'] ?? false,
                        'description' => $options[current_lang()]['description'],
                        'entity' => $options['entity'] ?? new Page,
                    ];
                    break;
                case VariableTypes::LINKS->value:
                    $variables[$field['name']] = GetLinks::run($val);
                    break;
                case VariableTypes::FORM->value:
                    $variables[$field['name']] = $val;
                    break;
                case VariableTypes::PAGES->value:
                    if (! is_array($val)) {
                        $variables[$field['name']] = [];
                    }
                    $pages = $val;
                    $order = $pages['order'] ?? 'created_at';
                    $query = config('shared.page_model')::query();
                    if (isset($pages['all_children']) && $pages['all_children']) {
                        $query = $query->where('parent_id', $entity->id);
                    } elseif (isset($pages['parent']) && $pages['parent']) {
                        $query = $query->where('parent_id', $pages['parent']);
                    } else {
                        if (! isset($pages['ids'])) {
                            $pages['ids'] = [];
                        }
                        $query = $query->whereIn('id', $pages['ids']);
                    }
                    if ($order == 'default') {
                    } elseif ($order == 'random') {
                        $query = $query->inRandomOrder();
                    } else {
                        $query = $query->orderByDesc($order);
                    }
                    $result = $query->paginate($pages['limit'] ?? 5);
                    $variables[$field['name']] = $result;
                    break;
                case VariableTypes::PAGE->value:
                    $variables[$field['name']] = app('_page')->get($val);
                    break;
                case VariableTypes::ARRAY->value:
                    $array = $options[$field['name']];
                    $vars = array_map(function ($item) {
                        $rep = $item[current_lang()] ?? [];
                        foreach ($item[main_lang()] as $key => $value) {
                            if (strlen($key) == 2) {
                                continue;
                            }
                            if (! isset($rep[$key])) {
                                $rep[$key] = $value;
                            }
                        }

                        return $rep;
                    }, $array);
                    foreach ($vars as &$var) {
                        if (isset($var['page'])) {
                            $var['page'] = app('_page')->get($var['page']);
                        }
                        if (isset($var['category'])) {
                            $var['category'] = \SmartCms\Store\Models\Category::find($var['category']);
                        }
                    }
                    $variables[$field['name']] = $vars;
                    break;
                case VariableTypes::PRODUCTS->value:
                    $variables[$field['name']] = \SmartCms\Store\Models\Product::find($val);
                    break;
                case VariableTypes::CATEGORY->value:
                    $variables[$field['name']] = \SmartCms\Store\Models\Category::find($val);
                    break;
                case VariableTypes::CATEGORIES->value:
                    if (! is_array($val)) {
                        $val = [];
                    }
                    $ids = [];
                    foreach ($val as $v) {
                        if (! is_numeric($v)) {
                            continue;
                        }
                        $ids[] = $v;
                    }
                    $variables[$field['name']] = \SmartCms\Store\Models\Category::query()->whereIn('id', $ids)->get();
                    break;
                default:
                    $variables[$field['name']] = $val;
                    break;
            }
        }

        return $variables;
    }
}
