<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Services\Schema\SchemaParser;

class Layout extends BaseModel
{
    protected $fillable = [
        'name',
        'path',
        'schema',
        'value',
    ];

    protected $casts = [
        'schema' => 'array',
        'value' => 'array',
    ];

    public function page()
    {
        return $this->hasMany(Page::class);
    }

    public function getVariables(): array
    {
        return SchemaParser::make($this->schema, $this->value);
    }
}
