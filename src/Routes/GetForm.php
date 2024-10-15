<?php

namespace SmartCms\Core\Routes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use SmartCms\Core\Components\Form as ComponentsForm;
use SmartCms\Core\Models\ContactForm;
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
        foreach ($form->fields as $field) {
            foreach ($field['fields'] as $f) {
                if ($f['required']) {
                    $validation[strtolower($f['name'])] = 'required';
                }
            }
        }
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            dd($validator->errors(), $request->all());

            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        } else {
            ContactForm::query()->create([
                'form_id' => $form->id,
                'data' => $request->all(),
            ]);
        }
        $attributes = json_decode($request->input('form_attributes'), true);

        return Blade::renderComponent((new ComponentsForm($form->id))->withAttributes($attributes));

        return response()->json([
            'success' => true,
            'data' => $request->input(),
        ]);
    }
}
