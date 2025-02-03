<?php

namespace SmartCms\Core\Routes\Handlers;

use Illuminate\Http\Request;
use SmartCms\Core\Models\Field;
use SmartCms\Core\Models\Form;
use SmartCms\Core\Repositories\Field\FieldRepository;
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
         $group = ['class' => $field['class'] ?? '', 'fields' => []];
         if (! isset($field['fields'])) {
            continue;
         }
         foreach ($field['fields'] as $f) {
            $fieldModel =  Field::query()->where('id', $f['field'] ?? 0)->first();
            if ($fieldModel) {
               $group['fields'][] = FieldRepository::make()->find($fieldModel->id);
            }
         }
         $fields[] = $group;
      }
      return new ScmsResponse(true, $fields);
   }
}
