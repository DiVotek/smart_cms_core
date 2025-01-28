<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Services\Schema\SchemaParser;
use SmartCms\Core\Traits\HasStatus;

class Layout extends BaseModel
{
    use HasStatus;

    protected $fillable = [
        'name',
        'path',
        'schema',
        'value',
        'status',
        'template',
        'can_be_used',
    ];

    protected $casts = [
        'schema' => 'array',
        'value' => 'array',
        'can_be_used' => 'boolean',
    ];

    public function page()
    {
        return $this->hasMany(Page::class);
    }

    public function getVariables(?array $value = null): array
    {
        if (! $value || empty($value)) {
            $value = $this->value;
        }

        return SchemaParser::make($this->schema, $value);
    }
}
