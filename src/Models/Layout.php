<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use SmartCms\Core\Services\Frontend\LayoutService;
use SmartCms\Core\Services\Schema\SchemaParser;
use SmartCms\Core\Traits\HasStatus;

/**
 * Class Layout
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the layout.
 * @property string $path The path to the layout template.
 * @property bool $can_be_used Whether the layout can be used.
 * @property bool $status The status of the layout.
 * @property array $schema The schema configuration for the layout.
 * @property array $value The values for the layout configuration.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\Page[] $page The pages using this layout.
 */
class Layout extends BaseModel
{
    use HasFactory;
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
