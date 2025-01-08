<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Mauricius\LaravelHtmx\Http\HtmxResponse;
use SmartCms\Core\Components\Form as ComponentsForm;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Services\AdminNotification;
use SmartCms\Core\Services\UserNotification;

class FormHandler
{
    public function __invoke(Request $request)
    {
        $form = $request->input('form');
        $form = Form::query()->where('code', $form)->first();
        if (! $form) {
            return abort(404);
        }
        $validation = [];
        $attributes = json_decode($request->input('form_attributes'), true);
        $customAttributes = [];
        foreach ($form->fields as $field) {
            foreach ($field['fields'] as $f) {
                $f = Field::query()->find($f['field']);
                if ($f->required) {
                    $validation[strtolower($f->html_id)] = 'required';
                }
                if ($f->validation) {
                    $validation[strtolower($f->html_id)] = $f->validation;
                }
                $customAttributes[strtolower($f->html_id)] = $f->label[current_lang()];
            }
        }
        $validator = Validator::make($request->all(), $validation);
        $validator->setAttributeNames($customAttributes);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            return with(new HtmxResponse(Blade::renderComponent((new ComponentsForm($form->id))->withAttributes($attributes))))
                ->addTrigger('form-error');
        } else {
            $data = [];
            foreach ($request->except(['_token', 'form', 'form_attributes']) as $key => $value) {
                $field = Field::query()->where('html_id', $key)->first();
                if ($field) {
                    $data[$field->name] = $value;
                }
            }
            ContactForm::query()->create([
                'form_id' => $form->id,
                'data' => $data,
            ]);
            $notifications = $form->notification ?? [];
            $notification = $notifications[current_lang()] ?? '';
            if ($notification) {
                UserNotification::make()
                    ->title($notification)
                    ->success()
                    ->send();
            }
            AdminNotification::make()->title(_nav('form').' '.$form->name.' '._actions('was_sent'))->success()->sendToAll();
        }

        return with(new HtmxResponse(Blade::renderComponent((new ComponentsForm($form->id))->withAttributes($attributes))))
            ->addTrigger('close-modal')
            ->addTrigger('new-notification')
            ->addTrigger('form-success');
    }
}
