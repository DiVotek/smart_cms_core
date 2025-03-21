<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Http\Request;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Resources\FieldResource;
use SmartCms\Core\Services\ScmsResponse;

class FormFieldsHandler
{
    public function __invoke(Request $request)
    {
        $form = $request->input('form');
        $form = Form::query()->where('code', $form)->first();
        if (! $form) {
            return new ScmsResponse(false);
        }
        $fields = [];
        foreach ($form->fields as $field) {
            $fieldModel = Field::query()->where('id', $field['field'] ?? 0)->first();
            if ($fieldModel) {
                $fieldModel->required = $field['is_required'] ?? false;
                $fields[] = FieldResource::make($fieldModel)->get();
            }
        }
        $button = $form->data[current_lang()]['button'] ?? $form->data['button'] ?? '';
        $fields = [
            'name' => $form->name(),
            'fields' => $fields,
            'button' => $button,
        ];

        return new ScmsResponse(true, $fields);
    }
}
