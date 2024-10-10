<?php

namespace SmartCms\Core\Routes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use SmartCms\Core\Components\Form as ComponentsForm;
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
            if ($field['required']) {
                $validation[strtolower($field['name'])] = 'required';
            }
        }
        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            dd($validator->errors(), $request->all());

            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return Blade::renderComponent(new ComponentsForm($form->id));

        return response()->json([
            'success' => true,
            'data' => $request->input(),
        ]);
    }
}
