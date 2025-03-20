<?php

namespace SmartCms\Core\Models;

use SmartCms\Core\Traits\HasSorting;
use SmartCms\Core\Traits\HasStatus;

/**
 * class Template
 *
 * @property int $id The unique identifier for the model.
 * @property int $template_section_id The template section identifier for the model.
 * @property int $sorting The sorting of the model.
 * @property array $value The value of the model.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property TemplateSection $section The section of the model.
 * @property mixed $entity The entity of the model.
 */
class Template extends BaseModel
{
    use HasSorting;
    use HasStatus;

    protected $guarded = [];

    protected $casts = [
        'value' => 'array',
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function section()
    {
        return $this->belongsTo(TemplateSection::class, 'template_section_id');
    }
}
