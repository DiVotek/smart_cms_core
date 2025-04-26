<?php

namespace SmartCms\Core\Components;

use Illuminate\View\Component;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Resources\FieldResource;

class Form extends Component
{
    public ?ModelsForm $form;

    public array $fields;

    public string $button;

    public function __construct(public string $code, public string $class = '')
    {
        $this->form = ModelsForm::query()->where('code', $code)->first();
        if (! $this->form) {
            $this->fields = [];
            $this->button = 'Submit';
        }
        $fields = [];
        foreach ($this->form->fields as $field) {
            $fieldModel = Field::query()->where('id', $field['field'] ?? 0)->first();
            if ($fieldModel) {
                $fieldModel->required = $field['is_required'] ?? false;
                $fields[] = FieldResource::make($fieldModel)->get();
            }
        }
        $this->fields = $fields;
        $this->button = $this->form->data[current_lang()]['button'] ?? $this->form->data['button'] ?? '';
    }

    public function render()
    {
        return <<<'blade'
            <form id="{{$form->html_id ?? $form->code}}"
                name="{{$form->code}}"
                wire:submit.prevent="callAction('form_submit',{{ json_encode(['code' => $form->code]) }})"
                action="{{route('smartcms.form.submit')}}"
                {{$attributes->merge(['class' => $class])}} method="POST">
                @csrf
                <input type="hidden" name="form" value="{{$form->code}}">
                @foreach($fields as $field)
                <x-s::form.field :field="$field" :code="$form->code" />
                @endforeach
                @if(view()->exists('forms.button'))
                    @include('forms.button')
                @else
                    @include('smart_cms::forms.button')
                @endif
            </form>
         blade;
    }
}
