<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\GetLinks;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\VariableTypes;

class Builder extends Component
{
    public array $template;

    public function __construct(array $data = [])
    {
        $this->template = $data;
    }

    public function render(): View|Closure|string
    {
        return <<<'blade'
            <div class="builder">
                @foreach ($template as $key => $field)
                    @include($field['component'], $field['options'])
                @endforeach
            </div>
        blade;
    }

    public function parse_options(array $field): array
    {
        $options = $field['options'];
        $host = app('_page')->first();
        $reference = [
            'logo' => asset('/storage'.logo()),
            'host' => $host->route() ?? '',
            'hostname' => $host->name() ?? '',
            'company_name' => company_name(),
            'entity' => $options['entity'] ?? new Page,
            'breadcrumbs' => $options['breadcrumbs'] ?? [],
        ];
        if (isset($field['schema'])) {
            $reference = array_merge($reference, $this->parseSchema($field['schema'], $options, $reference['entity']));
        }

        return $reference;
    }

    public function parseSchema(array $schema, array $options, mixed $entity): array
    {
        if (empty($schema)) {
            return [];
        }
        $variables = [];
        foreach ($schema as $field) {
            if (! isset($field['type']) || ! isset($field['name'])) {
                throw new Exception('Field type or name is missing in schema');
            }
            switch ($field['type']) {
                case VariableTypes::PHONE->value:
                    $variables[$field['name']] = phones()[$options[current_lang()][$field['name']]];
                    break;
                case VariableTypes::PHONES->value:
                    $phones = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = array_map(function ($phone) {
                        return phones()[$phone];
                    }, $phones);
                    break;
                case VariableTypes::EMAIL->value:
                    $variables[$field['name']] = emails()[$options[current_lang()][$field['name']]];
                    break;
                case VariableTypes::ADDRESS->value:
                    $variables[$field['name']] = addresses()[$options[current_lang()][$field['name']]] ?? '';
                    break;
                case VariableTypes::ADDRESSES->value:
                    $addresses = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = array_map(function ($address) {
                        return addresses()[$address];
                    }, $addresses);
                    break;
                case VariableTypes::SCHEDULE->value:  // ref
                    $variables[$field['name']] = schedules()[$options[current_lang()][$field['name']]];
                    break;
                case VariableTypes::SCHEDULES->value:
                    $schedules = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = array_map(function ($schedule) {
                        return schedules()[$schedule];
                    }, $schedules);
                    break;
                case VariableTypes::SOCIALS->value:
                    $socials = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = array_map(function ($social) {
                        return socials()[$social];
                    }, $socials);
                    break;
                case VariableTypes::HEADING->value:
                    $variables[$field['name']] = [
                        'use_page_heading' => $options['use_page_heading'] ?? false,
                        'use_page_name' => $options['use_page_name'] ?? false,
                        'heading_type' => $options['heading_type'] ?? 'none',
                        'title' => $options[current_lang()]['title'],
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
                    $menu = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = GetLinks::run($menu);
                    break;
                case VariableTypes::FORM->value:
                    $variables[$field['name']] = $options[current_lang()][$field['name']];
                    break;
                case VariableTypes::PAGES->value:
                    $pages = $options[current_lang()][$field['name']] ?? [];
                    if (! is_array($pages)) {
                        $variables[$field['name']] = [];
                    }
                    $order = $pages['order'] ?? 'created_at';
                    $query = config('shared.page_model')::query();
                    // $query = app('_page')->all();
                    if (isset($pages['all_children']) && $pages['all_children']) {
                        $query = $query->where('parent_id', $entity->id);
                    } elseif (isset($pages['parent']) && $pages['parent']) {
                        $query = $query->where('parent_id', $pages['parent']);
                    } else {
                        $query = $query->whereIn('id', $pages['ids']);
                    }
                    if ($order == 'default') {
                    } elseif ($order == 'random') {
                        $query = $query->inRandomOrder();
                    } else {
                        $query = $query->orderByDesc($order);
                        // $query = $query->sortByDesc($order);
                    }
                    $result = $query->paginate($pages['limit'] ?? 5);
                    // $result = $query->slice(0, $pages['limit'] ?? 5);
                    $variables[$field['name']] = $result;
                    break;
                case VariableTypes::PAGE->value:
                    $variables[$field['name']] = app('_page')->get($options[current_lang()][$field['name']]);
                    break;
                case VariableTypes::ARRAY->value:
                    $array = $options[$field['name']];
                    $vars = array_map(function ($item) {
                        return $item[current_lang()] ?? [];
                    }, $array);
                    foreach ($vars as &$var) {
                        if (isset($var['page'])) {
                            $var['page'] = app('_page')->get($var['page']);
                        }
                    }
                    $variables[$field['name']] = $vars;
                    break;
                case VariableTypes::PRODUCTS->value:
                    $variables[$field['name']] = \SmartCms\Store\Models\Product::find($options[current_lang()][$field['name']]);
                    break;
                default:
                    $variables[$field['name']] = $options[current_lang()][$field['name']];
                    break;
            }
        }

        return $variables;
    }
}
