<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\GetDescription;
use SmartCms\Core\Actions\Template\GetLinks;
use SmartCms\Core\Actions\Template\GetTitle;
use SmartCms\Core\Models\Menu;
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
                    @include($field['component'], $parse_options($field))
                @endforeach
            </div>
        blade;
    }

    public function parse_options(array $field): array
    {
        $options = $field['options'];
        $host = Page::first();
        $reference = [
            'logo' => asset('/storage'.logo()),
            'host' => $host->route() ?? '',
            'hostname' => $host->name() ?? '',
            'company_name' => company_name(),
            'entity' => $options['entity'],
            'breadcrumbs' => $options['breadcrumbs'],
        ];
        if (isset($field['schema'])) {
            $reference = array_merge($reference, $this->parseSchema($field['schema'], $options));
        }

        return $reference;
    }
    // public function parse($options)
    // {
    //     $reference = [];
    //     foreach ($options as $key => $option) {
    //         if (strlen($key) == 2) {
    //             if ($key == current_lang()) {
    //                 $reference = array_merge($reference, $option);
    //             }
    //         } elseif ($key == 'breadcrumbs') {
    //             $reference = array_merge($reference, $option);
    //         } elseif ($key == 'entity') {
    //             $reference['entity'] = $option;
    //         } elseif (is_array($option)) {
    //             $newFields = [];
    //             foreach ($option as $k => $v) {
    //                 $newFields = array_merge($newFields, [$v[current_lang()] ?? []]);

    //                 continue;
    //                 if (strlen($k) == 2) {
    //                     if ($k == current_lang()) {
    //                         $newFields = array_merge($newFields, $v);
    //                     }
    //                 } else {
    //                     continue;
    //                 }
    //                 $item = [];
    //                 if (! is_array($v)) {
    //                     $newFields[$k] = $v;

    //                     continue;
    //                 }
    //                 foreach ($v as $module_key => $module_value) {
    //                     if (str_contains($module_key, 'image')) {
    //                         $module_value = '/storage' . $module_value;
    //                     }
    //                     if (str_contains($module_key, '_')) {
    //                         if (str_contains($module_key, current_lang())) {
    //                             $module_key = str_replace(current_lang() . '_', '', $module_key);
    //                         }
    //                     }
    //                     $item[$module_key] = $module_value;
    //                 }
    //                 $newFields[$k] = $item;
    //             }
    //             $reference = array_merge($reference, [$key => $newFields]);
    //         } elseif (str_contains($key, 'image')) {
    //             $reference[$key] = '/storage' . $option;
    //         } else {
    //             $reference[$key] = $option;
    //         }
    //     }
    //     $title = GetTitle::run($reference);
    //     $description = GetDescription::run($reference);
    //     $host = Page::first();

    //     return array_merge($reference, [
    //         'options' => $options,
    //         'title' => $title,
    //         'description' => $description,
    //         'logo' => asset('/storage' . logo()),
    //         'host' => $host->route() ?? '',
    //         'hostname' => $host->name() ?? '',
    //         'company_name' => company_name(),
    //     ]);
    // }

    public function parseSchema(array $schema, array $options): array
    {
        if (empty($schema)) {
            return [];
        }
        $variables = [];
        foreach ($schema as $field) {
            if (! isset($field['type']) || ! isset($field['name'])) {
                continue;
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
                    $variables[$field['name']] = addresses()[$options[current_lang()][$field['name']]];
                    break;
                case VariableTypes::ADDRESSES->value:
                    $addresses = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = array_map(function ($address) {
                        return addresses()[$address];
                    }, $addresses);
                    break;
                case VariableTypes::SCHEDULE->value:
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
                    $variables[$field['name']] = GetTitle::run($options);
                    break;
                case VariableTypes::DESCRIPTION->value:
                    $variables[$field['name']] = GetDescription::run($options);
                    break;
                case VariableTypes::LINKS->value:
                    $menu = $options[current_lang()][$field['name']];
                    $menu = Menu::query()->find($menu);
                    $variables[$field['name']] = GetLinks::run($menu->value ?? []);
                    break;
                case VariableTypes::FORM->value:
                    $variables[$field['name']] = $options[current_lang()][$field['name']];
                    break;
                case VariableTypes::PAGES->value:
                    $pages = $options[current_lang()][$field['name']];
                    $variables[$field['name']] = Page::query()->whereIn('id', $pages)->get();
                    break;
                case VariableTypes::PAGE->value:
                    $variables[$field['name']] = Page::find($options[current_lang()][$field['name']]);
                    break;
                case VariableTypes::ARRAY->value:
                    $array = $options[$field['name']];
                    $variables[$field['name']] = array_map(function ($item) {
                        return $item[current_lang()] ?? [];
                    }, $array);
                    break;
                default:
                    $variables[$field['name']] = $options[current_lang()][$field['name']];
                    break;
            }
        }

        return $variables;
        dd($schema, $options, $variables);
        $reference = [];
        foreach ($options as $key => $option) {
            if (strlen($key) == 2) {
                if ($key == current_lang()) {
                    $reference = array_merge($reference, $option);
                }
            } elseif ($key == 'breadcrumbs') {
                $reference = array_merge($reference, $option);
            } elseif ($key == 'entity') {
                $reference['entity'] = $option;
            } elseif (is_array($option)) {
                $newFields = [];
                foreach ($option as $k => $v) {
                    $newFields = array_merge($newFields, [$v[current_lang()] ?? []]);

                    continue;
                    if (strlen($k) == 2) {
                        if ($k == current_lang()) {
                            $newFields = array_merge($newFields, $v);
                        }
                    } else {
                        continue;
                    }
                    $item = [];
                    if (! is_array($v)) {
                        $newFields[$k] = $v;

                        continue;
                    }
                    foreach ($v as $module_key => $module_value) {
                        if (str_contains($module_key, 'image')) {
                            $module_value = '/storage'.$module_value;
                        }
                        if (str_contains($module_key, '_')) {
                            if (str_contains($module_key, current_lang())) {
                                $module_key = str_replace(current_lang().'_', '', $module_key);
                            }
                        }
                        $item[$module_key] = $module_value;
                    }
                    $newFields[$k] = $item;
                }
                $reference = array_merge($reference, [$key => $newFields]);
            } elseif (str_contains($key, 'image')) {
                $reference[$key] = '/storage'.$option;
            } else {
                $reference[$key] = $option;
            }
        }
        $title = GetTitle::run($reference);
        $description = GetDescription::run($reference);
        $host = Page::first();

        return array_merge($reference, [
            'options' => $options,
            'title' => $title,
            'description' => $description,
            'logo' => asset('/storage'.logo()),
            'host' => $host->route() ?? '',
            'hostname' => $host->name() ?? '',
        ]);
    }
}
