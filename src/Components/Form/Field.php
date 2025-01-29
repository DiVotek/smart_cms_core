<?php

namespace SmartCms\Core\Components\Form;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use SmartCms\Core\Models\Field as ModelsField;

class Field extends Component
{
    public $field;

    public string $id;

    public ?string $placeholder;

    public string $label;

    public ?string $description;

    public array $options = [];

    public bool $required;

    public $fieldAttributes = [];

    public function __construct(ModelsField $field)
    {
        $this->field = $field;
        $this->id = $field->html_id ?? $field->id;
        $this->placeholder = $field->placeholder[current_lang()] ?? null;
        $this->label = $field->label[current_lang()] ?? '';
        $this->description = $field->description[current_lang()] ?? null;
        $this->options = $field->options[current_lang()] ?? [];
        if (! empty($field->options)) {
            $this->options = array_map(function ($option) {
                return $option[current_lang()] ?? $option[main_lang()] ?? '';
            }, $field->options);
        }
        $this->required = $field->required;
        $type = $field->type;
        $attributes = ['class' => 'field', 'required' => (bool) $field->required, 'value' => $field->value ?? '', 'name' => $field->html_id, 'placeholder' => $this->placeholder, 'id' => $this->id];
        if ($type == 'checkbox' || $type == 'radio') {
            // dd($field);
            $attributes['checked'] = (bool) $field->value;
        }
        if ($type == 'checkbox') {
            $attributes['value'] = 1;
        }
        if ($type != 'select' && $type != 'textarea') {
            $attributes['type'] = $type;
        }
        if ($type == 'textarea') {
            unset($attributes['type'], $attributes['value']);
        }
        if ($field->mask && in_array($type, ['text', 'email', 'tel', 'number', 'url'])) {
            $mask = $field->mask[current_lang()] ?? null;
            if ($mask) {
                $attributes['x-mask'] = $mask;
                $attributes['x-data'] = '';
            }
        }
        $this->fieldAttributes = $attributes;
        // $this->attributes = $this->attributes->merge($attributes);
    }

    public function render()
    {
        // return Cache::rememberForever('scms_form_field_component', function () {
        if (view()->exists('templates::' . template() . '.form.field')) {
            return view('templates::' . template() . '.form.field');
        }

        return <<<'blade'
            <div class="form-group">
                @if(isset($slot) && !$slot->isEmpty())
                {{ $slot }}
                @else
                <div class="form-top">
                    <label for="{{ $id }}">{{ $label }}</label>
                    <div class="form-input">
                    @if ($field->type == 'textarea')
                        <textarea {{ $attributes->merge($fieldAttributes) }} >@if($field->value){{ trim($field->value) }}@endif</textarea>
                    @elseif($field->type == 'select')
                        <select {{ $attributes->merge($fieldAttributes) }}>
                            @foreach ($options as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    @else
                        <input {{ $attributes->merge($fieldAttributes) }} >
                    @endif
                    @if($field->image)
                        <img src="{{ $field->image }}" alt="{{ $label }}" />
                    @endif
                    </div>
                </div>
                @isset($field->error)
                  <span id="{{ $id }}-error" class="form-error">{{ $field->error[0] ?? '' }}</span>
                @endisset
                <p class="form-description"></p>
                @endif
            </div>
         blade;
        // });
    }
}
