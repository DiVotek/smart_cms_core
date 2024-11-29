<?php

namespace SmartCms\Core\Routes;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use SmartCms\Core\Components\Form as ComponentsForm;
use SmartCms\Core\Models\Admin;
use SmartCms\Core\Models\ContactForm;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;

class GetForm
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
        foreach ($form->fields as $field) {
            foreach ($field['fields'] as $f) {
                $f = Field::query()->find($f['field']);
                if ($f->required) {
                    $validation[strtolower($f->html_id)] = 'required';
                }
                if ($f->validation) {
                    $validation[strtolower($f->html_id)] = $f->validation;
                }
            }
        }
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            return Blade::renderComponent((new ComponentsForm($form->id, request()->all(), $errors))->withAttributes($attributes));
        } else {
            $data = [];
            foreach($request->except(['_token', 'form', 'form_attributes']) as $key => $value) {
                $field = Field::query()->where('html_id', $key)->first();
                if ($field) {
                    $data[$field->name] = $value;
                }
            }
            ContactForm::query()->create([
                'form_id' => $form->id,
                'data' => $data,
            ]);
            foreach (Admin::all() as $recipient) {
                $recipient->notifyNow(Notification::make()
                    ->success()
                    ->title(_nav('form').' '.$form->name.' '._actions('was_sent'))->toDatabase());
            }
        }

        return Blade::renderComponent((new ComponentsForm($form->id))->withAttributes($attributes));

        return response()->json([
            'success' => true,
            'data' => $request->input(),
        ]);
    }
}
