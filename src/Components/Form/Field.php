<?php

namespace SmartCms\Core\Components\Form;

use Illuminate\View\Component;

class Field extends Component
{
    public function __construct(public object $field, public string $code, public ?string $model = null) {}

    public function render()
    {
        return <<<'blade'
            @php
                $type = $field->type ?? 'text';
                $name = $field->name ?? '';
                $label = $field->label ?? ucfirst($name);
                $options = $field->options ?? [];
                if($model) {
                    $model = $model . '.' . $field->html_name;
                } else {
                $model = 'formData.' . $code . '.' . $field->html_name;
                }

            @endphp
            @switch($type)
            @case('input')
            @case('email')
            @case('phone')
            @case('text')
            @case('number')
                @if(view()->exists('forms.input'))
                    @include('forms.input')
                @else
                    @include('smart_cms::forms.input')
                @endif
            @break
            @case('select')
                @if(view()->exists('forms.select'))
                    @include('forms.select')
                @else
                    @include('smart_cms::forms.select')
                @endif
            @break
            @case('checkbox')
                @if(view()->exists('forms.checkbox'))
                    @include('forms.checkbox')
                @else
                    @include('smart_cms::forms.checkbox')
                @endif
            @break
            @case('radio')
                @if(view()->exists('forms.radio'))
                    @include('forms.radio')
                @else
                    @include('smart_cms::forms.radio')
                @endif
            @break
            @case('textarea')
                @if(view()->exists('forms.textarea'))
                    @include('forms.textarea')
                @else
                    @include('smart_cms::forms.textarea')
                @endif
            @break
            @default
                @if(view()->exists('forms.input'))
                    @include('forms.input')
                @else
                    @include('smart_cms::forms.input')
                @endif
            @endswitch
            @if(view()->exists('forms.error'))
                @include('forms.error')
            @else
                @include('smart_cms::forms.error')
            @endif

         blade;
    }
}
