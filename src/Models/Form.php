<?php

namespace SmartCms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use SmartCms\Core\BaseModel;
use SmartCms\Core\Traits\HasTable;

/**
 * class Form
 *
 * @property int $id The unique identifier for the model.
 * @property string $code The code of the model.
 * @property string $name The name of the model.
 * @property string $html_id The html id of the model.
 * @property string $class The class of the model.
 * @property string $style The style of the model.
 * @property array $fields The fields of the model.
 */
class Form extends BaseModel
{
    use HasTable;

    public static function getDb(): string
    {
        return 'forms';
    }

    protected $table = 'forms';

    protected $guarded = [];

    protected $casts = [
        'fields' => 'array',
    ];
}
