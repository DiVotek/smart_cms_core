<?php

namespace SmartCms\Core\Services\Schema;

use Exception;
use Illuminate\Support\Facades\Event;
use SmartCms\Core\Actions\Template\GetLinks;
use SmartCms\Core\Models\Form as ModelsForm;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Repositories\Page\PageRepository;
use SmartCms\Core\Traits\HasHooks;

class SchemaParser
{
    use HasHooks;

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
                    $this->applyHook('parse', $this);
                }
            } catch (Exception $e) {
                if (config('app.debug')) {
                    // Log::error($e->getMessage(), $this->field, $this->values, $e->getTrace());
                    dd($e->getMessage(), $this->field, $this->values, $e->getTrace(), get_defined_vars(), $this->fields, $this->values);
                }
            }
        }

        return $this->variables;
    }

    public function parse()
    {
        $fieldValue = $this->values[current_lang()][$this->field->name] ?? $this->values[$this->field->name] ?? null;
        switch ($this->field->type) {
            case 'number':
                $value = (int) $fieldValue ?? 0;
                break;
            case 'bool':
                $value = (bool) $fieldValue;
                break;
            case 'text':
                if (! is_string($fieldValue)) {
                    $value = '';
                    break;
                }
                $value = $fieldValue;
                break;
            case 'form':
                if (! is_string($fieldValue)) {
                    $value = 0;
                    break;
                }
                $form = ModelsForm::query()->find($fieldValue);
                if ($form) {
                    $value = $form->code;
                } else {
                    $value = 0;
                }
                break;
            case 'image':
            case 'file':
                if (! is_string($fieldValue)) {
                    $fieldValue = '';
                }
                if (! $fieldValue) {
                    $value = no_image();
                    break;
                }
                if (! str_contains($fieldValue, 'http')) {
                    $fieldValue = 'storage/' . $fieldValue;
                }
                $value = asset($fieldValue);
                $value = preg_replace('#(?<!:)//+#', '/', $value);
                break;
            case 'heading':
                $fieldValue = $this->values[$this->field->name] ?? [];
                if (! is_array($fieldValue) || ! isset($fieldValue['heading_type']) || ! isset($fieldValue['use_page_heading']) || ! isset($fieldValue['use_page_name']) || ! isset($fieldValue['use_custom'])) {
                    $fieldValue = [
                        'heading_type' => 'h1',
                        'use_page_heading' => true,
                        'use_page_name' => false,
                        'use_custom' => false,
                    ];
                }
                if ($fieldValue['use_custom']) {
                    $fieldValue['heading'] = $this->values[current_lang()][$this->field->name] ?? $fieldValue['heading'] ?? '';
                }
                $value = $fieldValue;
                break;
            case 'description':
                $fieldValue = $this->values[$this->field->name] ?? [];
                if (! is_array($fieldValue) || ! isset($fieldValue['is_description']) || ! isset($fieldValue['is_summary']) || ! isset($fieldValue['is_custom'])) {
                    $fieldValue = [
                        'heading_type' => 'h1',
                        'is_description' => true,
                        'is_summary' => false,
                        'is_custom' => false,
                    ];
                }
                if ($fieldValue['is_custom']) {
                    if (isset($this->values[current_lang()])) {
                        $fieldValue['description'] = $this->values[current_lang()][$this->values[current_lang()][$this->field->name]] ?? $fieldValue['description'] ?? '';
                    } else {
                        $fieldValue['description'] = $fieldValue['description'] ?? '';
                    }
                }
                $value = $fieldValue;
                break;

            case 'socials':
                if (! is_array($fieldValue)) {
                    $value = [];
                } else {
                    $socials = [];
                    foreach ($fieldValue as $social) {
                        $soc = socials()[$social] ?? null;
                        if ($soc) {
                            $socials[] = (object) $soc;
                        }
                    }
                    $value = $socials;
                }
                break;
            case 'phone':
                if (! is_string($fieldValue)) {
                    $value = '';
                    break;
                }
                if (! isset(phones()[$fieldValue])) {
                    $value = '';
                } else {
                    $value = phones()[$fieldValue];
                }
                break;
            case 'phones':
                if (! is_array($fieldValue)) {
                    $value = [];
                } else {
                    $phones = [];
                    foreach ($fieldValue as $phone) {
                        if (isset(phones()[$phone])) {
                            $phones[] = phones()[$phone];
                        }
                    }
                    $value = $phones;
                }
                break;
            case 'email':
                if (! is_string($fieldValue)) {
                    $value = '';
                    break;
                }
                if (! isset(emails()[$fieldValue])) {
                    $value = '';
                } else {
                    $value = emails()[$fieldValue];
                }
                break;
            case 'emails':
                if (! is_array($fieldValue)) {
                    $value = [];
                } else {
                    $emails = [];
                    foreach ($fieldValue as $email) {
                        if (isset(emails()[$email])) {
                            $emails[] = emails()[$email];
                        }
                    }
                    $value = $emails;
                }
                break;
            case 'address':
                if (! is_string($fieldValue)) {
                    $value = '';
                    break;
                }
                if (! isset(addresses()[$fieldValue])) {
                    $value = '';
                } else {
                    $value = addresses()[$fieldValue];
                }
                break;
            case 'addresses':
                if (! is_array($fieldValue)) {
                    $value = [];
                } else {
                    $addresses = [];
                    foreach ($fieldValue as $address) {
                        if (isset(addresses()[$address])) {
                            $addresses[] = addresses()[$address];
                        }
                    }
                    $value = $addresses;
                }
                break;
            case 'schedule':
                if (! is_string($fieldValue)) {
                    $value = '';
                    break;
                }
                if (! isset(schedules()[$fieldValue])) {
                    $value = '';
                } else {
                    $value = schedules()[$fieldValue];
                }
                break;
            case 'schedules':
                if (! is_array($fieldValue)) {
                    $value = [];
                    break;
                }
                $value = [];
                foreach ($fieldValue as $schedule) {
                    if (! isset(schedules()[$schedule])) {
                        continue;
                    }
                    $value[] = schedules()[$schedule];
                }
                break;

            case 'menu':
                if (! is_string($fieldValue)) {
                    $value = [];
                    break;
                }
                $value = GetLinks::run($fieldValue);
                break;
            case 'page':
                if (! is_array($fieldValue) || ! isset($fieldValue['id']) || ! isset($fieldValue['parent_id'])) {
                    $fieldValue = [
                        'parent_id' => null,
                        'id' => Page::query()->first()->id ?? 0,
                        'ids' => [],
                    ];
                }
                $value = PageRepository::make()->find($fieldValue['id']);
                break;
            case 'pages':
                if (! is_array($fieldValue) || ! isset($fieldValue['parent_id'])) {
                    $fieldValue = [
                        'parent_id' => null,
                        'ids' => [],
                    ];
                }
                // if (! is_array($fieldValue)) {
                //     throw new Exception('Pages field must be an array');
                // }
                // if (! isset($fieldValue['parent_id'])) {
                //     throw new Exception('Pages field must have parent_id key');
                // }
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
                if (! is_array($fieldValue)) {
                    $fieldValue = [];
                }
                foreach ($fieldValue as $v) {
                    $value[] = (object) self::make($this->field->options, $v);
                }
                break;
            default:
                $value = $fieldValue ?? '';
                break;
        }
        if (is_array($value) && empty($value)) {
            $this->variables[$this->field->name] = [];

            return;
        }
        if ($this->field->type == 'bool') {
            $this->variables[$this->field->name] = (bool) $value;

            return;
        }
        if ($value || (is_string($value) && strlen($value) == 0) || (is_numeric($value) && $value == 0)) {
            $this->variables[$this->field->name] = $value;
        }
    }
}
