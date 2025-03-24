<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\Traits\HasTable;
use SmartCms\Core\Traits\HasTranslate;

/**
 * Class Form
 *
 * @property int $id The unique identifier for the model.
 * @property string $name The name of the form.
 * @property bool $status The status of the form.
 * @property string $code The unique code identifier for the form.
 * @property string $html_id The HTML ID attribute for the form.
 * @property string $class The CSS class for the form.
 * @property array $fields The fields configuration of the form.
 * @property array|null $button The button configuration.
 * @property array|null $notification The notification messages configuration.
 * @property array $data Additional data for the form.
 * @property \DateTime $created_at The date and time when the model was created.
 * @property \DateTime $updated_at The date and time when the model was last updated.
 * @property-read \Illuminate\Database\Eloquent\Collection|\SmartCms\Core\Models\ContactForm[] $submissions The form submissions.
 */
class Form extends BaseModel
{
    use HasFactory;
    use HasTable;
    use HasTranslate;

    protected $guarded = [];

    protected $casts = [
        'fields' => 'array',
        'button' => 'array',
        'notification' => 'array',
        'email_text' => 'array',
        'data' => 'array',
    ];
}
