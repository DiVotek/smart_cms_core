<?php

namespace SmartCms\Core\Models;

use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Services\Frontend\LayoutService;
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
        $metadata = LayoutService::make()->getSectionMetadata($this->path ?? '');
        $schema = $metadata['schema'] ?? [];
        if ($value == null && $schema == $this->schema) {
            return Cache::remember('layout_variables_'.$this->id.'_'.current_lang_id(), 60, function () {
                return SchemaParser::make($this->schema, $this->value);
            });
        }
        if (! $value || empty($value)) {
            $value = $this->value;
        }
        if ($schema != $this->schema) {
            $this->update(['schema' => $schema]);
        }

        return SchemaParser::make($schema, $value);
    }
}
