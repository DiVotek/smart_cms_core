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
                return $option[current_lang()];
            }, $field->options);
        }
        $this->required = $field->required;
    }

    public function render()
    {
        // return Cache::rememberForever('scms_form_field_component', function () {
        if (view()->exists('templates::'.template().'.form.field')) {
            return view('templates::'.template().'.form.field');
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
                        <textarea {{ $attributes->merge(['class' => 'field', 'required' => !!$field->required,'value' => $field->value ?? '','name' => $field->name,'placeholder' => $placeholder,'id' => $id,]) }} ></textarea>
                    @elseif($field->type == 'select')
                        <select {{ $attributes->merge(['class' => 'field', 'required' => !!$field->required,'value' => $field->value ?? '','name' => $field->name,'placeholder' => $placeholder,'id' => $id,]) }}>
                                @foreach ($options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                        </select>
                    @else
                        <input {{ $attributes->merge(['class' => 'field', 'required' => !!$field->required,'value' => $field->value ?? '','name' => $field->name,'placeholder' => $placeholder,'id' => $id,'type' => $field->type]) }} >
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
