<?php

namespace SmartCms\Core\Components;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;
use SmartCms\Core\Models\Form as ModelsForm;

class Form extends Component
{
    public ?ModelsForm $form;

    public string $button;

    public $errors;

    public function __construct($form, ?ViewErrorBag $errors = null)
    {
        $this->form = ModelsForm::find($form);
        $this->button = 'templates::'.template().'.forms.'.$this->form->style.'.button';
        $this->errors = $errors ?? new ViewErrorBag([]);
    }

    public function render()
    {
        return <<<'blade'
            <form id="{{$form->html_id ?? $form->code}}" name="{{$form->code}}" hx-get="{{route('smartcms.form.submit')}}" hx-target="#{{$form->html_id ?? $form->code}}" hx-swap="outerHTML" hx-trigger="submit" hx-on="htmx:afterRequest: document.dispatchEvent(new CustomEvent('{{$form->id}}-success'))" {{$attributes}}>
               <input type="hidden" name="form" value="{{$form->code}}" />
                <input type="hidden" name="form_attributes" value="{{ json_encode($attributes) }}">
               @foreach ($form->fields as $f)
                <div class="form-group {{$f['class'] ?? ''}}">
                    @foreach ($f['fields'] as $field)
                        <x-s::field :style="$form->style" :field="$field" name="{{$field['name']}}"/>
                    @endforeach
                </div>
               @endforeach
               @include($button)
            </form>

         blade;
    }
}
