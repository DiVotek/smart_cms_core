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
      $this->id  = $field->html_id ?? $field->id;
      $this->placeholder = $field->placeholder[current_lang()] ?? null;
      $this->label = $field->label[current_lang()] ?? '';
      $this->description = $field->description[current_lang()] ?? null;
      $this->options = $field->options[current_lang()] ?? [];
      if(!empty($field->options)){
         $this->options = array_map(function($option){
            return $option[current_lang()];
         }, $field->options);
      }
      $this->required = $field->required;
   }

   public function render()
   {
      // return Cache::rememberForever('scms_form_field_component', function () {
      if (view()->exists('templates::' . template() . '.form.field')) {
         return view('templates::' . template() . '.form.field');
      }
      return <<<'blade'
            <div class="flex flex-col mb-4">
               <label for="{{ $id }}">{{ $label }}</label>
               @if ($field->type == 'textarea')
                  <textarea placeholder="{{ $field->placeholder }}" id="{{ $id }}" class="px-3 py-2"
                        {{ $attributes }} />
               @elseif($field->type == 'select')
                  <select id="{{ $id }}" class="px-3 py-2" {{ $attributes }}>
                        @foreach ($options as $option)
                           <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                  </select>
               @else
                  <input id="{{ $id }}" type="{{ $field->type }}" class="px-3 py-2 border" {{ $attributes }}>
               @endif
               @isset($field->error)
                  <span id="{{ $id }}-error" class="mt-2 text-xs text-red-500">{{ $field->error[0] ?? '' }}</span>
               @endisset
            </div>
         blade;
      // });
   }
}
