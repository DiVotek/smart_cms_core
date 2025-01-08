<?php

namespace SmartCms\Core\Services\Schema;

class ArrayToField
{
    public static function make(array $field, string $prefix = ''): FieldSchema
    {
        if (! isset($field['name'])) {
            throw new \Exception('Field name is required');
        }
        if (! isset($field['type'])) {
            $field['type'] = 'text';
        }
        if ($field['type'] == 'array' && ! isset($field['schema'])) {
            throw new \Exception('Field schema is required for array type');
        }
        $fieldSchema = new FieldSchema($field['name'], $field['type'], $field['schema'] ?? []);
        if (isset($field['label'])) {
            $fieldSchema->setLabel($field['label']);
        }
        if (isset($field['required'])) {
            $fieldSchema->setRequired($field['required']);
        }
        if (isset($field['default'])) {
            $fieldSchema->setDefault($field['default']);
        }
        if (isset($field['validation'])) {
            $fieldSchema->setValidation($field['validation']);
        }
        if ($prefix) {
            $fieldSchema->setPrefix($prefix);
        }

        return $fieldSchema;
    }
}
