<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\BaseModel;
use SmartCms\Core\Traits\HasStatus;

/**
 * class TemplateSection
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the model.
 * @property bool $status The status of the model.
 * @property bool $locked The locked of the model.
 * @property string $design The design of the model.
 * @property array $value The value of the model.
 * @property bool $is_system The is system of the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property TemplateSection[] $morphs The morphs of the model.
 */
class TemplateSection extends BaseModel
{
    use HasStatus;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'locked' => 'boolean',
        'value' => 'array',
    ];

    public function morphs()
    {
        return $this->morphMany(self::class, 'en');
    }
}
