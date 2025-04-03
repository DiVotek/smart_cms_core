<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SmartCms\Core\Services\Frontend\SectionService;
use SmartCms\Core\Services\Schema\ArrayToField;
use SmartCms\Core\Services\Schema\Builder;
use SmartCms\Core\Traits\HasStatus;

/**
 * Class TemplateSection
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the template section.
 * @property string $design The design identifier for the section.
 * @property bool $status The status of the section.
 * @property array $schema The schema configuration for the section.
 * @property array $value The values for the section.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\Template[] $templates The templates using this section.
 */
class TemplateSection extends BaseModel
{
    use HasFactory;
    use HasStatus;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'value' => 'array',
        'schema' => 'array',
    ];

    public function morphs()
    {
        return $this->morphMany(self::class, 'en');
    }

    public function getFields(): array
    {
        $schema = SectionService::make()->getSectionMetadata($this->design);
        $schema = $schema['schema'] ?? [];
        $fields = [];
        foreach ($schema as $field) {
            $field = ArrayToField::make($field, 'value.');
            $componentField = Builder::make($field);
            $fields = array_merge($fields, $componentField);
        }

        return $fields;
    }
}
