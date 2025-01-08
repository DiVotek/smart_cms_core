<?php

namespace SmartCms\Core\Services\Schema;

use Exception;
use Illuminate\Support\Facades\Event;
use SmartCms\Core\Actions\Template\GetLinks;
use SmartCms\Core\Repositories\Page\PageRepository;

class SchemaParser
{

    public FieldSchema $field;

    public array $fields = [];

    public array $values = [];

    public array $variables = [];

    public static function make(array $schema, array $values): array
    {
        return (new self($schema, $values))->run();
    }

    public function __construct(array $schema, array $values)
    {
        $this->fields = $schema;
        $this->values = $values;
    }

    public function run()
    {
        foreach ($this->fields as $field) {
            $this->field = ArrayToField::make($field);
            try {
                if (in_array($this->field->type, Builder::AVAILABLE_TYPES)) {
                    $this->parse();
                } else {
                    Event::dispatch('cms.admin.schema.parse', [$field, $this]);
                }
            } catch (Exception $e) {
                dd($e->getMessage(), $this->field, $this->values, $e->getTrace());
            }
        }

        return $this->variables;
    }

    public function parse()
    {
        $fieldValue = $this->values[current_lang()][$this->field->name] ?? $this->values[$this->field->name];
        switch ($this->field->type) {
            case 'number':
            case 'bool':
            case 'text':
            case 'form':
                $value = $fieldValue;
                break;
            case 'image':
            case 'file':
                if (! str_contains($fieldValue, 'http')) {
                    $fieldValue = 'storage/' . $fieldValue;
                }
                $value = asset($fieldValue);
                break;
            case 'heading':
                $value = $fieldValue;
                break;
            case 'description':
                $value = $fieldValue;
                break;

            case 'socials':
                $value = array_map(function ($social) {
                    return (object) socials()[$social];
                }, $fieldValue);
                break;
            case 'phone':
                $value = phones()[$fieldValue];
                break;
            case 'phones':
                $value = array_map(function ($phone) {
                    return phones()[$phone];
                }, $fieldValue);
                break;
            case 'email':
                $value = emails()[$fieldValue];
                break;
            case 'emails':
                $value = array_map(function ($email) {
                    return emails()[$email];
                }, $fieldValue);
                break;
            case 'address':
                $value = addresses()[$fieldValue];
                break;
            case 'addresses':
                $value = array_map(function ($address) {
                    return addresses()[$address];
                }, $fieldValue);
                break;
            case 'schedule':
                $value = schedules()[$fieldValue];
                break;
            case 'schedules':
                $value = array_map(function ($schedule) {
                    return schedules()[$schedule];
                }, $fieldValue);
                break;

            case 'menu':
                $value = GetLinks::run($fieldValue);
                break;
            case 'form':
                $value = $fieldValue;
                break;
            case 'page':
                if (! is_array($fieldValue)) {
                    throw new Exception('Page field must be an array');
                }
                if (! isset($fieldValue['parent_id']) || ! isset($fieldValue['id'])) {
                    throw new Exception('Page field must have parent_id and id keys');
                }
                $value = PageRepository::make()->find($fieldValue['id']);
                break;
            case 'pages':
                if (! is_array($fieldValue)) {
                    throw new Exception('Pages field must be an array');
                }
                if (! isset($fieldValue['parent_id'])) {
                    throw new Exception('Pages field must have parent_id key');
                }
                $limit = $fieldValue['limit'] ?? 10;
                $sort = $fieldValue['order_by'] ?? 'sorting';
                $sortOrder = 'desc';
                $where = [
                    'parent_id' => $fieldValue['parent_id'],
                ];
                if (isset($fieldValue['ids'])) {
                    $where['id'] = $fieldValue['ids'];
                }
                $value = PageRepository::make()->filterBy($where, [$sort => $sortOrder], $limit);
                break;
            case 'array':
                $value = [];
                foreach ($fieldValue as $v) {
                    $value[] = (object) self::make($this->field->options, $v);
                }
                break;
            default:
                $value = $fieldValue;
                break;
        }
        if (is_array($value) && empty($value)) {
            $this->variables[$this->field->name] = [];
            return;
        }
        if ($value) {
            $this->variables[$this->field->name] = $value;
        }
    }
}
