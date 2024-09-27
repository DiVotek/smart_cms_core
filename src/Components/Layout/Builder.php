<?php

namespace SmartCms\Core\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SmartCms\Core\Actions\Template\GetDescription;
use SmartCms\Core\Actions\Template\GetTitle;

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
                @if(!str_contains($field['component'], 'template'))
                    @livewire($field['component'], ['entity' => $field['options']['entity'],'breadcrumbs' => $field['options']['breadcrumbs']],key(rand()))
                @else
                    @include($field['component'], $parse_options($field['options']))
                @endif
                @endforeach
            </div>
        blade;
    }

    public function parse_options(array $options): array
    {
        $reference = [];
        foreach ($options as $key => $option) {
            if (strlen($key) == 2) {
                if ($key == current_lang()) {
                    $reference = array_merge($reference, $option);
                }
            } else if (is_array($option)) {
                $newFields = [];
                foreach ($option as $k => $v) {
                    $item = [];
                    if (!is_array($v)) {
                        $newFields[$k] = $v;
                        continue;
                    }
                    foreach ($v as $module_key => $module_value) {
                        if (str_contains($module_key, 'image')) {
                            $module_value = '/storage' . $module_value;
                        }
                        if (str_contains($module_key, '_')) {
                            if (str_contains($module_key, current_lang())) {
                                $module_key = str_replace(current_lang() . '_', '', $module_key);
                            }
                        }
                        $item[$module_key] = $module_value;
                    }
                    $newFields[$k] = $item;
                }
                $reference = array_merge($reference, [$key => $newFields]);
            } else if (str_contains($key, 'image')) {
                $reference[$key] = '/storage' . $option;
            } else {
                $reference[$key] = $option;
            }
        }
        $title = GetTitle::run($reference);
        $description = GetDescription::run($reference);
        return array_merge($reference, ['options' => $options, 'title' => $title, 'description' => $description]);
    }
}
