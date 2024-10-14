<?php

namespace SmartCms\Core\Components;

use Illuminate\View\Component;
use SmartCms\Core\Models\Form as ModelsForm;

class Form extends Component
{
    public ?ModelsForm $form;

    public string $button;

    public function __construct($form)
    {
        $this->form = ModelsForm::find($form);
        $this->button = 'templates::'.template().'.forms.'.$this->form->style.'.button';
    }

    public function render()
    {
        return <<<'blade'
            <form id="{{$form->html_id ?? $form->code}}" name="{{$form->code}}" hx-get="{{route('smartcms.form.submit')}}" hx-target="#{{$form->html_id ?? $form->code}}" hx-swap="outerHTML" hx-trigger="submit" hx-on="htmx:afterRequest: document.dispatchEvent(new CustomEvent('{{$form->id}}-success'))" {{$attributes}}>
               <input type="hidden" name="form" value="{{$form->code}}" />
                <input type="hidden" name="form_attributes" value="{{ json_encode($attributes) }}">
               @foreach ($form->fields as $field)
                  <x-s::field wire:model="formData.{{$field['name']}}" :style="$form->style" :field="$field" name="{{$field['name']}}"/>
               @endforeach
               @include($button)
            </form>

         blade;
    }
}
