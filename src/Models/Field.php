<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SmartCms\Core\Traits\HasTranslate;

/**
 * Class Field
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the field.
 * @property string $type The type of the field (text, select, checkbox, etc.).
 * @property string $html_id The HTML ID attribute for the field.
 * @property array $data Additional data for the field (mask, placeholder, description, etc.).
 * @property bool $required Whether the field is required.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 */
class Field extends BaseModel
{
    use HasTranslate;
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'placeholder' => 'array',
        'options' => 'array',
        'label' => 'array',
        'description' => 'array',
        'mask' => 'array',
        'data' => 'array',
    ];
}
