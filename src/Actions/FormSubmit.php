<?php

namespace SmartCms\Core\Actions;

use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Notifications\NewContactFormNotification;
use SmartCms\Core\Services\AdminNotification;
use SmartCms\Core\Support\Actions\Action;

class FormSubmit extends Action
{
    public function handle(): mixed
    {
        $form = Form::query()->where('code', $this->params['code'])->first();
        if (! $form) {
            return null;
        }
        $this->instance->validate($this->getValidationRules(), [], $this->getAttributeBindings(), $this->instance->formData);
        $this->submitForm($form);
        $this->instance->formData[$form->code] = [];

        return null;
    }

    public function submitForm(Form $form)
    {
        $data = [];
        foreach ($this->instance->formData[$form->code] as $key => $value) {
            $field = Field::query()->where('html_id', $key)->first();
            $data[$field->name()] = $value;
        }
        $contactForm = ContactForm::query()->create([
            'form_id' => $form->id,
            'data' => $data,
        ]);
        $notification = $form->data[current_lang()]['notification'] ?? $form->data['notification'] ?? '';
        if ($notification) {
            $this->instance->notify($notification, 'success');
            $this->instance->dispatch($form->code.'-submitted');
        }
        AdminNotification::make()->title(_nav('form').' '.$form->name.' '._actions('was_sent'))->success()->sendToAll(new NewContactFormNotification($contactForm));
    }

    public function getValidationRules(): array
    {
        $form = Form::query()->where('code', $this->params['code'])->first();
        if (! $form) {
            return [];
        }
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldModel = Field::query()->where('id', $field['field'] ?? 0)->first();
            if ($fieldModel) {
                if ($field['is_required']) {
                    $rules['formData.'.$form->code.'.'.$fieldModel->html_id] = 'required';
                }
            }
        }

        return $rules;
    }

    public function getAttributeBindings(): array
    {
        $form = Form::query()->where('code', $this->params['code'])->first();
        if (! $form) {
            return [];
        }
        $attributes = [];
        foreach ($form->fields as $field) {
            $fieldModel = Field::query()->where('id', $field['field'] ?? 0)->first();
            $attributes['formData.'.$form->code.'.'.$fieldModel->html_id] = $fieldModel->name();
        }

        return $attributes;
    }
}
