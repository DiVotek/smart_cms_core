<?php

namespace SmartCms\Core\Components;

use Illuminate\View\Component;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form as ModelsForm;

class Form extends Component
{
    public ?ModelsForm $form;

    public array $fields;

    public string $button;

    public function __construct($form, array $values = [], array $errors = [])
    {
        $form = ModelsForm::find($form);
        $fields = $form->fields;
        $newFields = [];
        foreach ($fields as &$group) {
            $newGroup = ['class' => $group['class'] ?? '', 'fields' => []];
            foreach ($group['fields'] as &$field) {
                $field = Field::find($field['field']);
                if (! $field) {
                    continue;
                }
                $name = strtolower($field->name);
                $field->value = $values[$name] ?? null;
                $field->error = $errors[$name] ?? null;
                $newGroup['fields'][] = $field;
            }
            $newFields[] = $newGroup;
        }
        $this->form = $form;
        $this->fields = $newFields;
        $this->button = $form->button[current_lang()] ?? 'Submit';
    }

    public function render()
    {
        return <<<'blade'
            <form id="{{$form->html_id ?? $form->code}}" name="{{$form->code}}" hx-get="{{route('smartcms.form.submit')}}" hx-target="#{{$form->html_id ?? $form->code}}" hx-swap="outerHTML" hx-trigger="submit" hx-on="htmx:afterRequest: document.dispatchEvent(new CustomEvent('{{$form->id}}-success'))" {{$attributes}}>
               <input type="hidden" name="form" value="{{$form->code}}" />
                <input type="hidden" name="form_attributes" value="{{ json_encode($attributes) }}">
                @foreach($fields as $group)
                    <x-s::form.group class="form-group {{$group['class'] ?? ''}}">
                        @foreach($group['fields'] as $field)
                            <x-s::form.field :field="$field" :name="strtolower($field->name)" value="{{$field->value ?? ''}}"  />
                        @endforeach
                    </x-s::form.group>
                @endforeach
                <button type="submit" class="btn btn-primary">{{$button}}</button>
            </form>
         blade;
    }
}
