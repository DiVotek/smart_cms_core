<?php

namespace SmartCms\Core\Livewire;

use Livewire\Component;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Form;

class FormBuilder extends Component
{
    public $formData = [];

    public $form;

    public function mount($form)
    {
        $this->form = Form::find($form[0] ?? 0);

        foreach ($this->form->fields as $field) {
            $this->formData[$field['name']] = '';
        }
    }

    public function render()
    {
        return view('livewire.form-builder', ['formFields' => $this->form->fields]);
    }

    public function submit()
    {
        $validation = [];
        foreach ($this->form->fields as $field) {
            $fieldValidation = '';
            if ($field['required']) {
                $fieldValidation .= 'required';
            }
            if ($field['type'] == 'email') {
                $fieldValidation .= '|email';
            }
            if ($field['type'] == 'number') {
                $fieldValidation .= '|numeric';
            }
            if ($field['type'] == 'tel') {
                $fieldValidation .= '|regex:/^([0-9\s\-\+\(\)]*)$/';
            }
            if (strlen($fieldValidation) > 0) {
                $validation['formData.'.$field['name']] = $fieldValidation;
            }
        }
        if (! empty($validation)) {
            $this->validate($validation);
        }
        ContactForm::query()->create(['data' => $this->formData, 'form_id' => $this->form->id]);
        foreach ($this->formData as $key => $value) {
            $formData[$key] = '';
        }
        $this->dispatch('successfully-send');
        // if (setting(config('settings.mailer.host'))) {
        //     if (setting(config('settings.send_form_mail')) && setting(config('settings.admin_mails'))) {
        //         $adminMails = explode(',', setting(config('settings.admin_mails')));
        //         foreach ($adminMails as $mail) {
        //             Mail::to($mail)->send(new \App\Mail\AdminFormEmail($form));
        //         }
        //     }
        // }
    }
}
