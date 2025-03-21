<?php

namespace SmartCms\Core\Models;

use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Services\Schema\SchemaParser;
use SmartCms\Core\Traits\HasStatus;

class Layout extends BaseModel
{
    use HasStatus;

    protected $guarded = [];

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
        if ($value == null) {
            return Cache::remember('layout_variables_'.$this->id.'_'.current_lang_id(), 60, function () {
                return SchemaParser::make($this->schema, $this->value);
            });
        }

        if (! $value || empty($value)) {
            $value = $this->value;
        }

        return SchemaParser::make($this->schema, $value);
    }
}
